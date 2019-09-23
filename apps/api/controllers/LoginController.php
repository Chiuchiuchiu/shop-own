<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use apps\api\models\ButlerAuth;
use apps\api\models\Member;
use common\models\MemberHouse;
use components\wechatSDK\WxMiniProgram;

class LoginController extends Controller
{
    public $modelClass = 'apps\api\models\Member';

    const EXPIRES_IN = 7200;    //有效时间

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['create']);
        unset($actions['update']);

        return $actions;
    }

    /**
     * 小程序登录
     * @author zhaowenxi
     */
    public function actionCreate(){

        $redis = \Yii::$app->redis;

        $redis->select(1);

        $wechatMini = new WxMiniProgram(\Yii::$app->params['wechatMini']);

        $jsCode = $this->post('js_code');

        $wxUser = $wechatMini->code2Session($jsCode);

        //注，小程序请求成功是没有errcode返回字段
        if(isset($wxUser['errcode'])){
            return $this->renderJsonFail(40005, $wxUser['errmsg']);
        }
//        if(!isset($wxUser['unionid'])){
//            return $this->renderJsonFail(40003);
//        }

        $this->accessToken = $wxUser['session_key'];

        //获取用户微信信息
  //>> feng0822      $member = Member::findOne(['wechat_unionid' => $wxUser['unionid']]);
        $member = Member::findOne(['mp_open_id' => $wxUser['openid']]);
        if(!$member){
            return $this->renderJsonFail(40003);
        }

        //保存小程序 openId
        if(empty($member->mp_open_id)){
            $member->mp_open_id = $wxUser['openid'];
            $member->save();
        }

        //查找业主所属项目信息，默认第一个
        $project = MemberHouse::find()
            ->leftJoin('house', 'house.house_id = member_house.house_id')
            ->select("house.project_house_id")
            ->where(['member_house.member_id' => $member->id])
            ->asArray()
            ->one();

        $this->projectId = $project['project_house_id'];

        //写入redis
        $userJson = json_encode([
            'id' => $member->id,
            'headImg' => $member->headimg,
            'name' => $member->nickname,
            'phone' => $member->phone,
            'projectId' => $this->projectId
        ]);

        $redis->set($wxUser['session_key'], $userJson);

        $redis->expire($wxUser['session_key'], self::EXPIRES_IN);

        return $this->renderJsonSuccess(200, ['access_token' => $wxUser['session_key'],'projectId' => $this->projectId, 'expires_in' => self::EXPIRES_IN]);
    }

    /**
     * 管家登录
     * @author zhaowenxi
     */
    public function actionLoginButler(){

        $post = $this->post();

        if(!isset($post['name']) || !isset($post['pwd']) || !isset($post['sign']))
        {
            return $this->renderJsonFail(40010);
        }

        //查找管家
        $butler = ButlerAuth::findOne(['account' => $post['name'], 'password' => $post['pwd'], 'status' => ButlerAuth::STATUS_USED]);

        if(!$butler){
            return $this->renderJsonFail(60001);
        }

        $signArr = ['name' => $butler->account, 'pwd' => $butler->password, "salt" => \Yii::$app->params['butler_config']['salt']];

        $sign = self::createSign($signArr);

        if($butler->account == $post['name'] && $butler->password == $post['pwd'] && $sign == $post['sign']){

            //生成随机的token
            $this->accessToken = "bu" . $butler->used_to . uniqid() . time();

            $butlerJson = json_encode([
                'butlerId' => $butler->used_to,
                'phone' => $butler->account,
                'projectId' => $butler->butler->project_house_id,
                'name' => $butler->butler->nickname
            ]);

            //写入redis
            $redis = \Yii::$app->redis;

            $redis->select(1);

            $redis->set($this->accessToken, $butlerJson);

            $redis->expire($this->accessToken, self::EXPIRES_IN);

            return $this->renderJsonSuccess(200, ['access-token' => $this->accessToken, 'expires_in' => self::EXPIRES_IN]);

        }else{

            return $this->renderJsonFail(40009);
        }
    }

    /**
     * 通过参数生成签名
     * @param $params
     * @return bool|string
     * @author zhaowenxi
     */
    private static function createSign($params){

        $str = '';

        foreach ($params as $v){

            if(!$v) return false;

            $str .= $v;
        }

        return md5(md5($str));
    }
}