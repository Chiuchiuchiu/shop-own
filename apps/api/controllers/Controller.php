<?php

namespace apps\api\controllers;

use apps\api\models\House;
use common\models\ApiLog;
use common\models\MemberHouse;
use common\valueObject\FileCache;
use Yii;
use yii\log\FileTarget;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * Class Controller
 * Description:
 */
class Controller extends ActiveController
{
    /**
     * @var array
     */
    protected $missPermission = ['index', 'article', 'shop'];

    protected $userId;
    protected $headImg;
    protected $butlerId;
    protected $phone;
    protected $nickname;
    protected $projectId;
    protected $accessToken;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function beforeAction($action)
    {
        $res = parent::beforeAction($action);

        if($res){

            //从头部获取access-token
            $header = \Yii::$app->request->headers;

            //首页与文章类不用验证token
            if(in_array($this->uniqueId, $this->missPermission)){

                return $res;
            }

            //判断是否重新获取微信info
            if($action->controller->id != "login"){

                $redis = \Yii::$app->redis;

                $redis->select(1);

                $accessToken = $header->get('access-token', '');
                if(empty($accessToken)){
                    Yii::$app->response->data = [
                        'code' => 40002,
                        'message' => self::resultCode(40002),
                        'data' => []
                    ];
                    return false;
                }

                //查找redis
                $userInfo = json_decode($redis->get($accessToken), true);

                if($userInfo){

                    switch ($action->id){

                        case "login-butler":    //管家登录，目前只有pos缴费需要

                            $this->butlerId  = $userInfo['butlerId'];

                            $this->nickname  = $userInfo['name'];

                            $this->projectId = $userInfo['projectId'];

                            $this->accessToken = $accessToken;
                            break;

                        default:    //用户登录
                            $this->userId    = $userInfo['id'];

                            $this->nickname  = $userInfo['name'];

                            $this->phone     = $userInfo['phone'];

                            $this->projectId = $userInfo['projectId'];

                            $this->headImg = $userInfo['headImg'];

                            $this->accessToken = $accessToken;

                        break;
                    }

                    //重置redis过期时间
                    $redis->expire($accessToken, LoginController::EXPIRES_IN);

                }else{

                    Yii::$app->response->data = [
                        'code' => 40500,
                        'message' => self::resultCode(40500),
                        'data' => []
                    ];
                    return false;
                }
            }
        }

        return $res;
    }

    public function afterAction($action, $result)
    {
        //接口记录操作
        $params = '';
        $saveResult = serialize($result);
        $controller = $action->controller->id;
        $ac = $action->id;
        $token = $this->accessToken ?? '-';
        $ip = \Yii::$app->request->getUserIP();

        ApiLog::writesLog($params, $saveResult, $controller, $ac, $token, $ip);

        return parent::afterAction($action, $result);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
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
                'member_id' => $this->userId,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->with('house')->all();

        foreach ($model as $row) {
            /* @var $row MemberHouse */
            if ($row->house->project->house_id == $this->projectId) {
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
     * 通过houseId获取项目
     * @author zhaowenxi
     * @param integer $houseId
     * @return object|bool
     */
    protected function getProject($houseId = 0)
    {
        if($houseId > 0){

            return House::findOne($houseId) ?? false;

        }

        return false;
    }

    /**
     * 获取post参数
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed
     * @author zhaowenxi
     */
    public function post($name = null, $defaultValue = null)
    {
        return Yii::$app->request->post($name, $defaultValue);
    }

    /**
     * 获取get参数
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed
     * @author zhaowenxi
     */
    public function get($name = null, $defaultValue = null)
    {
        return Yii::$app->request->get($name, $defaultValue);
    }

    /**
     * json返回格式
     * @param $params
     * @return array
     * @author zhaowenxi
     */
    protected function renderJson($params)
    {
        //操作记录
//        self::afterAction($this->action, $params);

        return $params;
    }

    /**
     * 成功json返回格式
     * @param $params
     * @param $code
     * @return array|string
     * @author zhaowenxi
     */
    protected function renderJsonSuccess($code = 200, $params = []){

        $message = self::resultCode($code);

        return $this->renderJson([
            'code'=>$code,
            'message'=>$message,
            'data'=>$params
        ]);
    }

    /**
     * 失败json返回格式
     * @param $params
     * @return array|string
     * @author zhaowenxi
     */
    protected function renderJsonFail($code = -1, $params = []){

        $message = self::resultCode($code);

        return $this->renderJson([
            'code'=>$code,
            'message'=>$message,
            'data'=>$params
        ]);
    }

    /**
     * 返回码
     * @param int $code
     * @return mixed
     * @author zhaowenxi
     */
    private static function resultCode($code = 0){
        $res = [
            200 => "success",

            //系统返回
            40001 => "unknown function",    //方法名没找到
            40002 => "invalid token",       //头部没有token
            40003 => "member not found",    //用户没有找到
            40004 => "request not found",   //未知请求（404）
            40005 => "wechat error",        //微信请求错误
            40006 => "unknown method",      //错误的请求方式
            40008 => "unknown config",      //未知配置
            40009 => "sign error",          //签名错误
            40010 => "unknown params",      //参数缺失
            40011 => "params error",        //参数错误
            40012 => "invalid key",         //商城接口没有key
            40500 => 'session timeout',

            //公共返回类
            41000 => "上传失败",
            41001 => "提交失败",
            41002 => "订单创建失败",
            41003 => "请不要重复提交",
            41004 => '未找到相关记录',
            41005 => '已评价',
            41006 => '该手机号未在物业系统中绑定房产，请联系管家',
            41007 => '谢谢参与！',
            41008 => '谢谢参与！房产已领取过红包',
            41009 => '红包被抢光啦！！',
            41010 => '活动已结束！',
            41011 => '未找到房产',

            //用户提示类
            50001 => "请填写正确的手机号",
            50002 => "该房产未与您的账号进行绑定！",
            50003 => "该项目暂取消“微信缴费”业务！",
            50004 => "请先缴纳物业管理费用！",
            50005 => "该手机不支持此充值！",
            50006 => "验证码错误",
            50007 => "无法配对原手机号",
            50010 => '未绑定手机号',
            50011 => '您有红包未领取！',
            50012 => '账单接口维护中,请稍后再试',

            //管家类
            60001 => "没有找到管家或账号已停用!",

            //报事类
            70001 => "",

            //支付类
            80001 => "没有找到订单",

            //抄表类
            90001 => "两次抄表数不正确",
            90002 => "读数不能小于上期读数",
        ];

        return isset($res[$code]) ? $res[$code] : "unknown error";
    }

    /**
     * 获取验证码，记录在阿里大于查
     * @param $phone
     * @return bool
     * @author zhaowenxi
     */
    public function getCode($phone){
        $code = false && YII_ENV == YII_ENV_DEV ? 1111 : rand(1000, 9999);

        \Yii::$app->cache->set('verifyPhone_' . $phone,
            [
                'code' => $code,
                'time' => time(),
            ]
            , 60 * 10);
        if (false && YII_ENV == YII_ENV_DEV) {
            $resp['result']['err_code']=0;
        } else {
            require(\Yii::getAlias('@components/alidayuSDK/TopSdk.php'));
            $c = new \TopClient(\Yii::$app->params['alidayu.app'], \Yii::$app->params['alidayu.secret']);
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("财到家");
            $req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"财到家\"}");
            $req->setRecNum($phone);
            $req->setSmsTemplateCode("SMS_25460346");
            $resp = $c->execute($req);
            $resp = json_encode($resp);
            $resp = json_decode($resp, true);
        }

        return (isset($resp['result']['err_code']) && $resp['result']['err_code'] == 0) ?? false;
    }

    /**
     * 记录请求新视窗返回参数
     * @author HQM
     * @param $msgLog
     * @param string $fileName
     * @throws \yii\base\InvalidConfigException
     */
    protected function writeFilelog($msgLog, $fileName='houseBill')
    {
        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/' . $fileName . '.log';
        $fileLog->messages[] = [$msgLog, 8, 'application', microtime(true)];
        $fileLog->export();
    }

}