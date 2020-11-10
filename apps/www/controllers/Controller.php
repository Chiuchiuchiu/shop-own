<?php

namespace apps\www\controllers;

use apps\www\models\Member;
use common\models\House;
use common\models\MemberHouse;
use common\models\OperationLog;
use common\models\Project;
use common\valueObject\FileCache;
use components\wechatSDK\WechatSDK;
use Yii;
use yii\helpers\Url;
use common\models\ShopShow;


/**
 * Class Controller
 * Description:
 */
class Controller extends \common\controllers\Controller
{
    /**
     * @var array
     */
    protected $missPermission = ['default/error'];
    protected $CDJ_TIP = '到家科技';
    protected $JW_TIP = '建屋物业';
    /**
     * @var Member
     */
    protected $user;

    /**
     * @var Project|null
     */
    protected $project;
    protected $from;
    const PROJECT_KEY_COOKIE_NAME='GAhr5ErblY4LsrOg';

    protected $ignoreList = ['shopping/activity', 'question/welcome-develop', 'question/perfect-develop', 'question/develop', 'question/develop-save', 'default/to-weixin'];

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
		
		$this->from = $this->getCookie("from") ? $this->getCookie("from") : '';
    }

    private function getTitleTip($key){
        if(!empty($key)){
            $model = Project::findOne(['url_key'=>$key]);
            if(!empty($model)){
                if($model->project_region_id == Project::JW_REGION_ID){
                    $this->CDJ_TIP = $this->JW_TIP;
                }
            }
        }
    }

    public function beforeAction($action)
    {
        $res = parent::beforeAction($action);

        $host = str_replace(['http://','https://'],'',Yii::$app->request->hostInfo);

        $path = Yii::$app->request->pathInfo;

        if($host == Yii::$app->params['domain.www'] && in_array($path, $this->ignoreList)){
            return $res;
        }

        $projectKey = '';
        if($host==Yii::$app->params['domain.www']){
            $projectKey = $this->getCookie(self::PROJECT_KEY_COOKIE_NAME);
	   $this->getTitleTip($projectKey);
            if($projectKey){
                $this->project = Project::findOne(['url_key' => $projectKey]);
            }
        }elseif($path == 'project-enter/index'){

        }else{
            $projectKey = str_replace(Yii::$app->params['domain.p'], '', $host);
            $projectKey = substr($projectKey,0,-1);
	   $this->getTitleTip($projectKey);
            if ($projectKey) {
                $project = Project::findOne(['url_key' => $projectKey]);

                if ($project) {
                    $this->setCookie(self::PROJECT_KEY_COOKIE_NAME,$projectKey,0,Yii::$app->params['domain.root']);
                }
                $requestUrl = $this->getRequestUrl(Yii::$app->request->url);

				$this->setCookie('from', '', -3600, Yii::$app->params['domain.www']);
                $this->redirect('https://'.Yii::$app->params['domain.www'] . $requestUrl);

                return false;
            }
        }

        if (in_array($this->route, $this->missPermission)) {
            return $res;
        }
        if ($res) {
            if (Yii::$app->user->isGuest) {
                if(YII_ENV==YII_ENV_DEV && YII_DEBUG){
                    if($this->get('__login_id')){
                        $this->user = Member::findOne($this->get('__login_id'));

                        OperationLog::writeLog($this->user, $this->project->house_id);

                        Yii::$app->user->login($this->user);

                        $this->refresh();

                        return false;
                    }
                }
                if(!$this->isWechat()){
                    if($path == "house/quick-pay-qr"){
                        $this->redirect('http://www.51homemoney.com/default/to-weixin');
                    }else{
                        $this->redirect('http://51homemoney.com/');
                    }
                    return false;
                }
                $this->user = $this->wechatInfo($projectKey);
                if ($this->user instanceof Member) {

                      OperationLog::writeLog($this->user, isset($this->project->house_id) ? $this->project->house_id : 0);

                    return Yii::$app->user->login($this->user) && $res;
                }
                return false;

            } else {
                $this->user = Member::findOne(Yii::$app->user->id);

				OperationLog::writeLog($this->user, isset($this->project->house_id) ? $this->project->house_id : 0);

                return Yii::$app->user->login($this->user) && $res;
            }
        }
        return $res;
    }

    public function render($view, $params = [])
    {
        $params['_user'] = $this->user;
        $params['_project'] = $this->project;
        //ajax 时候无需提供js支持
        if(!$this->isAjax && $this->isWechat()){
            if($this->project != null && $this->project->project_region_id == Project::JW_REGION_ID){
                $params['_wechatJs'] = (new WechatSDK(\Yii::$app->params['jw-wechat']))->getJsSign(\Yii::$app->request->hostInfo . \Yii::$app->request->url);
            }else{
                $params['_wechatJs'] = (new WechatSDK(\Yii::$app->params['wechat']))->getJsSign(\Yii::$app->request->hostInfo . \Yii::$app->request->url);
            }
        }
        return parent::render($view, $params);
    }

    protected function wechatInfo($projectKey='')
    {
        $homeUrl = 'https' . '://' . $_SERVER['HTTP_HOST'];

         if($this->project != null && $this->project->project_region_id == Project::JW_REGION_ID){
             $wechatSDK = new WechatSDK(Yii::$app->params['jw-wechat']);
         }else{
             $wechatSDK = new WechatSDK(Yii::$app->params['wechat']);
         }
        if ($this->isWechat() || 1) {
            if (($wechatUser = $wechatSDK->getOauthAccessToken()) === false) {
                $requestUrl = $this->getRequestUrl($_SERVER['REQUEST_URI']);

                $this->redirect($wechatSDK->getOauthRedirect($homeUrl . $requestUrl, '', 'snsapi_base'));
                return false;
            } else {
                $member = Member::findOne(['wechat_open_id' => $wechatUser['openid']]);
                if (!$member) {
                    if ($wechatUser['scope'] == 'snsapi_base') {
                        $requestUrl = $this->getRequestUrl($_SERVER['REQUEST_URI']);
                        $this->redirect($wechatSDK->getOauthRedirect($homeUrl . $requestUrl));
                        return false;
                    } else {
                        //拉取微信用户信息,建立用户
                        $userInfo = $wechatSDK->getOauthUserinfo($wechatUser['access_token'], $wechatUser['openid']);
                        if (!$member) {
                            $member = new Member();
                        }
                        $member->headimg = $userInfo['headimgurl'];
                        $member->wechat_open_id = $userInfo['openid'];

                        $nickname = trim($userInfo['nickname']);
                        $nickname = empty($nickname) ? '未设置昵称' : $nickname;

                        $member->nickname = $nickname;
                        if(isset($userInfo['unionid'])){
                            $member->wechat_unionid = $userInfo['unionid'];
                        }
                        $member->save();
                    }
                } else {
                    if(empty($member->wechat_unionid)){
                        //拉取微信用户信息,建立用户
                        $userInfo = $wechatSDK->getOauthUserinfo($wechatUser['access_token'], $wechatUser['openid']);

                        if(isset($userInfo['unionid'])){
                            $member->wechat_unionid = $userInfo['unionid'];
                            $member->save();
                        }
                    }
                }
                return $member;
            }
        } else {
            return false;
        }
    }

    protected function getRequestUrl($url)
    {
        $requestUrl = $url;
        $str = substr($url, 0, 4);

        if($str == '/mp?'){
            $requestUrl = '/?' . substr($url, 4);
        }else if(substr($str, 0, 2) == '/&'){
            $requestUrl = '/?' . substr($url, 2);
        }else if(substr($str, 0, 2) == '/0'){
            $requestUrl = '/?' . substr($url, 2);
        }

        return $requestUrl;
    }

    public function isWechat()
    {
        return strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'micromessenger') !== false;
    }
    public function redirect($url, $statusCode = 302, $https = 'https')
    {
        if($this->isAjax){
            return $this->renderJson(['code'=>$statusCode,'url'=>Url::to($url)]);
        }else{
            $url = Url::to($url, $https);
            return parent::redirect($url, $statusCode);
        }
    }

    /**
     * 判断用户是否有对应项目房产
     * @return bool
     * @author zhaowenxi
     */
    public function hasHouse(){

        $hasHouse = false;

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])->all();

        foreach ($model as $row) {
            /* @var $row MemberHouse */
            if ($row->house->project->house_id == $this->project->house_id) {
                $hasHouse = true;
                break;
            }
        }

        return $hasHouse;
    }

    /**
     * 项目列表缓存
     * @author HQM
     * @param int $ex
     * @return array|mixed
     */
    protected function projectListCache($ex=7200)
    {
        $key = "projectListCache_";
        $projectList = FileCache::init()->get($key);
        if(empty($projectList)){
            $projectList = \yii\helpers\ArrayHelper::map(
                \common\models\Project::findAll(['status'=>1]),
                'house_id',
                'house_name'
            );

            FileCache::init()->set($key, $projectList, $ex);
        }

        return $projectList;
    }

    /**
     * 获取项目ID
     * @author HQM
     * @param bool $projectRegionId
     * @return array|bool|int
     */
    protected function getProjectId($projectRegionId=false)
    {
        if(isset($this->project->house_id)){
            if($projectRegionId){
                return [
                    'projectId' => $this->project->house_id,
                    'projectRegionId' => $this->project->project_region_id,
                ];
            }

            return $this->project->house_id;
        }

        return false;
    }

    protected static function checkPhone($phone){

        return preg_match("/^1[3456789]\d{9}$/", $phone);
    }


    public function isShopShowFunc($projectId){
        $res = false;

        $count = ShopShow::find()
            ->Where(['like', 'projects', ",".$projectId .","])
            ->count();
        if(intval($count) > 0){
            $res = true;
        }
        return $res;
    }

	protected function getSubIdAndShortName($houseId){

        $find = House::findOne($houseId);

        $res = ['houseInfo' => '', 'shortName' => $find->project->house_name, 'subId' => $houseId];

        $find && $res['houseInfo'] = $find;

        !empty($find->project->short_name) && $res['shortName'] = $find->project->short_name;

        $find->sub_house_id && $res['subId'] = $find->sub_house_id;

        return $res;
    }

    
    
    public function fileLog($fileName,$content){
// todo
        $path = 'log/';
        $path = \Yii::getAlias(\Yii::$app->params['attached.path']. $path . $fileName);
        file_put_contents($path,$content.PHP_EOL, FILE_APPEND);
    }


    /**
     * 通过房产ID 获取SubDBConfigID 分区号
     * @author Administrator
     * @Date: 2020年4月6日
     * @Time: 上午10:05:50
     * @Description
     */
    public function getSubDBConfigID($houseId){
        $model = House::findOne(['house_id'=>$houseId]);
        if(!empty($model)){
            $subDBConfigID = $model->sub_db_config_id;
        }
        return $subDBConfigID;
    }

}
