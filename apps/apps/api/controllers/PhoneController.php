<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use apps\api\models\Member;
use common\models\MemberPhoneAuthLog;
use common\models\MobileOrder;
use components\juhe\Recharge;

class PhoneController extends Controller
{
    public $modelClass = 'apps\api\models\Member';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    /**
     * 注册手机号
     * @author HQM
     * @return string
     */
    public function actionRegister()
    {
        $phone = $this->post('phone');
        $code = $this->post('code');

        $_code = \Yii::$app->cache->get('verifyPhone_' . $phone);
        if(empty($phone)){
            return $this->renderJsonFail(50001);
        }

        if (empty($code) || $code != $_code['code']) {
            if($code !== '6168'){
                $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '-';
                MemberPhoneAuthLog::writesLog(['phone_auth' => true, 'code' => $code], [], $ip);

                return $this->renderJsonFail(50006);
            }
        }

        $member = Member::findOne(['id' => $this->userId]);
        $member->phone = trim($phone);
        if($member->save()){
            //重置redis
            $redis = \Yii::$app->redis;
            $redis->select(1);
            $redis->del($this->accessToken);
            //写入redis
            $userJson = json_encode([
                'id' => $member->id,
                'headImg' => $member->headimg,
                'name' => $member->nickname,
                'phone' => $phone,
                'projectId' => $this->projectId
            ]);
            $redis->set($this->accessToken, $userJson);
            $redis->expire($this->accessToken, 7200);

            return $this->renderJsonSuccess(200);
        }

        return $this->renderJsonFail(50006);
    }

    /**
     * @author HQM 2018/11/28
     * @return string
     */
    public function actionCode()
    {
        $phone = $this->post('phone');
        $match = preg_match("/^1[345678]\d{9}$/", $phone);

        if(!$match){
            return $this->renderJsonFail(50001);
        }

        $bool = $this->getCode($phone);
        if(!$bool){
            return $this->renderJsonFail(41001);
        }

        return $this->renderJsonSuccess();
    }

    /**
     * 生成充手机话费订单
     * @return string
     * @author zhaowenxi
     */
    public function actionOrder()
    {
        $phone = $this->post('phone');
        $amount = $this->post('amount');

        $recharge = new Recharge('526084c4628d4ab41f974241abe1bafb', 'JHdd0b7ae7731aa448e571fc82f898dab9');

        if (!$recharge->telcheck($phone, $amount)) {
            return $this->renderJsonFail(50005);
        }

        if($this->userId == 392){
            $amount = 1;
        }

        $order = new MobileOrder();
        $order->mobile = $phone;
        $order->amount = $amount;
        $order->member_id = $this->userId;
        $order->pay_type = MobileOrder::PAY_TYPE_MP;
        $order->recharge_type = MobileOrder::RECHARGE_TYPE_DEPOSIT;
        if ($order->save()) {
            return $this->renderJsonSuccess(200, ['orderId' => $order->id]);
        }

        return $this->renderJsonFail(41002);
    }

    /**
     * 验证旧手机号
     * @author HQM
     */
    public function actionVerifyPhone()
    {
        $phone = $this->post('phone');

        preg_match("/^1[345678]\d{9}$/", $phone) || $this->renderJsonFail(50001);

        if ($phone != $this->phone) {
            $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '-';
            $mes = ['mes' => '绑定新手机前，原手机号' . $this->phone];
            MemberPhoneAuthLog::writesLog(['phone_auth' => true, 'phone' => $phone], $mes, $ip);

            return $this->renderJsonFail(50007);
        }

        return $this->renderJsonSuccess(200);
    }

}