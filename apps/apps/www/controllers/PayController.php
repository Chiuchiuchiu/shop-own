<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/3 10:47
 * Description:
 */

namespace apps\www\controllers;


use apps\www\models\MemberCar;
use common\models\MemberPromotionCode;
use common\models\MobileOrder;
use common\models\MobileOrderLog;
use common\models\NewRepairLog;
use common\models\ParkingLog;
use common\models\ParkingOrder;
use common\models\ParkingPayOrderLog;
use common\models\PmChristmasBillItem;
use common\models\PmOrder;
use common\models\PmOrderDiscounts;
use common\models\PmOrderItem;
use common\models\ProjectParkingOneToOne;
use common\models\SysSwitch;
use components\genvict\Genvict;
use components\IRain\IRain;
use components\juhe\Recharge;
use components\newWindow\NewWindow;
use components\wechatSDK\WechatSDK;
use components\wechatSDK\WxPayCallBack;
use yii\web\NotFoundHttpException;

class PayController extends Controller
{
    protected $missPermission = [
        'pay/wx-notify',
        'pay/repair-wx-notify',
        'pay/mobile-wx-notify',
        'pay/parking-wx-notify',
        'pay/irain-tempfee-wxnotify'
    ];

    public $enableCsrfValidation = false;

    /**
     * 微信js支付
     * @return string
     */
    public function actionWxJs()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');
            $wechatSDK = new WechatSDK(\Yii::$app->params['wechat']);
            $pmOder = PmOrder::findOne($orderId);
            if ($pmOder && in_array($pmOder->status, [PmOrder::STATUS_READY, PmOrder::STATUS_WAIT_PAY])) {

                $totalAmount = bcmul($pmOder->total_amount, 100);
                if(empty($totalAmount)){
                    return $this->renderJsonFail("空账单，无需付费！");
                }

                $pmOder->number = PmOrder::createNumber();
                $pmOder->status = PmOrder::STATUS_WAIT_PAY;
                $pmOder->pay_type = PmOrder::PAY_TYPE_SUB_WECHAT_GDZAWY;
                $pmOder->house_type = $pmOder->house->structure->group;
                if ($pmOder->save()) {
                    if (in_array($pmOder->member_id, \Yii::$app->params['test.member.id'])) {
                        $pmOder->total_amount = \Yii::$app->params['test_member_amount'];
                        $pmOder->save();
                    }
                    $js = $wechatSDK->wxJsApiPay(
                        $pmOder->number,
                        $this->user->wechat_open_id,
                        $pmOder->total_amount,
                        ['subMchId' => \Yii::$app->params['wxPay']['subMchId']]
                    );
                    return $this->renderJsonSuccess($js);
                }
            }
        }

        return $this->renderJsonFail("提交信息有误");
    }

    /**
     * 支付回调
     */
    public function actionWxNotify()
    {
        WxPayCallBack::Notify(function ($data) {
            $order = PmOrder::findOne(['number' => $data['out_trade_no']]);
            if ($order === null) {
                throw new NotFoundHttpException();
            }

            if($order->status < PmOrder::STATUS_PAYED){
                $order->payed_at = time();
                $order->status = PmOrder::STATUS_PAYED;
                $order->save();
            }

            //不在测试组并且金额不是1.00 都进行账单核销
            if (!in_array($order->member_id, \Yii::$app->params['test.member.id'])) {
                //账单核销部分
                foreach ($order->items as $item) {
                    if ($item->status != PmOrderItem::STATUS_WAIT) {
                        continue;
                    }
                    /**
                     * @var $item PmOrderItem
                     */
                    $res = (new NewWindow())->payBill(
                        $order->id . '-' . $item->id,
                        $order->house->project_house_id,
                        $item->contract_no,
                        $item->amount,
                        $order->payed_at,
                        $order->bill_type
                    );
                    if ($res) {
                        $item->status = $res[0]['ReturnCode'];
                        $item->bankBillNo = $res[0]['BankBillNo'];
                        $item->completed_at = time();
                        $item->save();
                    }
                }

                //begin 针对圣诞、元旦活动缴费：2017-12-24 ~ 2018-02-21
                $authActivities = \Yii::$app->params['christmas_activities'];
                if(time() <= $authActivities['endTime']){
                    $this->recordPmChristmasBillItem($order->id, $order->house_id, $order->member_id);
                    $this->updateMemberRedPackStatus($order->id, $order->member_id, $order->house_id);
                }
                //end

                $pushData = [
                    'pmOrderId' => $order->id,
                    'payStatus' => $order->status,
                ];
                $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/notify', $pushData);

                return true;
            } else {
                $order->status = PmOrder::STATUS_TEST_PAYED;
                $order->save();
            }
            return true;
        })->Handle(false);
    }

    public function actionWxJsMobile()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');
            $wechatSDK = new WechatSDK(\Yii::$app->params['wechat']);
            $mobileOrder = MobileOrder::findOne($orderId);
            if ($mobileOrder && in_array($mobileOrder->status, [MobileOrder::STATUS_READY, MobileOrder::STATUS_WAIT_PAY])) {
                $mobileOrder->number = MobileOrder::createNumber();//重置一下NumberId
                $mobileOrder->status = MobileOrder::STATUS_WAIT_PAY;
                if ($mobileOrder->save()) {
                    $js = $wechatSDK->wxJsApiPay($mobileOrder->number, $this->user->wechat_open_id, $mobileOrder->amount, [
                        'notifyUrl' => \Yii::$app->request->hostInfo . "/pay/mobile-wx-notify",
                        'goodsTag' => '手机充值'
                    ]);
                    return $this->renderJsonSuccess($js);
                }
            }
        }
        return $this->renderJsonFail("提交信息有误");
    }

    public function actionMobileWxNotify()
    {
        WxPayCallBack::Notify(function ($data) {
            $order = MobileOrder::findOne(['number' => $data['out_trade_no']]);
            if ($order === null)
                throw new NotFoundHttpException;

            if($order->send_status == MobileOrder::SEND_STATUS_DONE){
                return true;
            }

            $order->payed_at = time();
            $order->status = MobileOrder::STATUS_PAYED;
            $order->save();
            //进行充值
            $recharge = new Recharge('526084c4628d4ab41f974241abe1bafb', 'JHdd0b7ae7731aa448e571fc82f898dab9');
            $telRechargeRes = $recharge->telcz($order->mobile, intval($order->amount), $order->number); #可以选择的面额5、10、20、30、50、100、300
            if ($telRechargeRes['error_code'] == '0') {
                //提交话费充值成功，可以根据实际需求改写以下内容
                $order->send_at = time();
                $order->send_status = MobileOrder::SEND_STATUS_DONE;
                $order->save();
            } else {
                $model = new MobileOrderLog();
                $model->data = serialize($telRechargeRes);
                $model->mobile = $order->mobile;
                $model->amount = $order->amount;
                $model->number = $order->number;
                $model->save();

                \Yii::warning(serialize($telRechargeRes));
            }
            return true;
        })->Handle(false);
    }

    public function actionWxJsParking()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');
            $wechatSDK = new WechatSDK(\Yii::$app->params['wechat']);
            $parkingOrder = ParkingOrder::findOne($orderId);
            $orderInfo = [];
            $errors = [];
            $postData = [];

            if ($parkingOrder && in_array($parkingOrder->status, [ParkingOrder::STATUS_DEFAULT, ParkingOrder::STATUS_WAIT])) {
                $parkingOrder->status = ParkingOrder::STATUS_WAIT;
                $projectParkingInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $parkingOrder->project_house_id]);

                if(!SysSwitch::inVal('testMember', $this->user->id)){
                    switch($parkingOrder->type){
                        case ParkingOrder::TYPE_T:
                            $postData = [
                                'parkingid' => $projectParkingInfo->parking_id,
                                'plateno' => $parkingOrder->plate_number,
                                'receivable' => $parkingOrder->amount,
                                'calcid' => $parkingOrder->calc_id,
                                'paymenttype' => ParkingOrder::GEN_PAYMENT_WE,
                                'openid' => $this->user->wechat_open_id,
                            ];
                            $tempCalcFeeOrder = (new Genvict($projectParkingInfo->app_id, $projectParkingInfo->app_key))->createParkingTempPayOrder($postData);
                            $orderInfo = $tempCalcFeeOrder->getValue();
                            $errors = $tempCalcFeeOrder->getErrors();
                            break;
                        case ParkingOrder::TYPE_M:
                            $postData = [
                                'parkingid' => $projectParkingInfo->parking_id,
                                'plateno' => $parkingOrder->plate_number,
                                'receivable' => $parkingOrder->amount,
                                'calcid' => $parkingOrder->calc_id,
                                'paymenttype' => ParkingOrder::GEN_PAYMENT_WE,
                                'openid' => $this->user->wechat_open_id,
                            ];
                            $monthCalcFeeOrder = (new Genvict($projectParkingInfo->app_id, $projectParkingInfo->app_key))->createParkingMonthPayOrder($postData);
                            $orderInfo = $monthCalcFeeOrder->getValue();
                            $errors = $monthCalcFeeOrder->getErrors();
                            break;
                    }
                    if($orderInfo){
                        $parkingOrder->calc_id = $orderInfo['Data'];
                    } else {
                        ParkingLog::writeLog($parkingOrder->member_id, $postData, $errors['msg'], $errors['code']);

                        return $this->renderJsonFail('服务异常，请前去收费岗缴费！');
                    }
                }

                if ($parkingOrder->save()) {
                    $js = $wechatSDK->wxJsApiPay(
                        $parkingOrder->number,
                        $this->user->wechat_open_id,
                        $parkingOrder->amount,
                        [
                            'notifyUrl' => \Yii::$app->request->hostInfo . "/pay/parking-wx-notify",
                            'goodsTag' => '停车缴费',
                            'subMchId' => \Yii::$app->params['wxPay']['subMchId'],
                        ]
                    );
                    return $this->renderJsonSuccess($js);
                }
            }
        }
        return $this->renderJsonFail("提交信息有误");
    }

    public function actionParkingWxNotify()
    {
        WxPayCallBack::Notify(function ($data) {
            $order = ParkingOrder::findOne(['number' => $data['out_trade_no']]);
            if ($order === null){
                throw new NotFoundHttpException;
            }

            if($order->status > ParkingOrder::STATUS_WAIT){
                return true;
            }

            $order->payed_at = time();
            $order->status = ParkingOrder::STATUS_PAYED;
            $order->transaction_id = $data['transaction_id'];
            $order->save();

            if(SysSwitch::inValue('testMember', $order->member_id)){
                $order->status = ParkingOrder::STATUS_TEST_PAYED;
                $order->save();
            } else {
                $postData = [
                    'parkingid' => $order->projectParkingOneToOne->parking_id,
                    'orderid' => $order->calc_id,
                    'transid' => $order->transaction_id,
                    'paidin' => $order->amount,
                    'discount' => $order->disc,
                    'returninfo' => 'SUCCESS',
                    'paytime' => date('Y-m-d H:i:s', $order->payed_at),
                    'status' => 1,
                ];
                $gResponse = (new Genvict($order->projectParkingOneToOne->app_id, $order->projectParkingOneToOne->app_key))->payResultNotify($postData);
                $notifyRes = $gResponse->getValue();
                $errors = $gResponse->getErrors();
                if(empty($notifyRes)){
                    ParkingPayOrderLog::writeLog($errors, $order->id, 'g');
                } else {

                    ParkingPayOrderLog::writeLog($notifyRes, $order->id, 'g');

                    $order->send_at = time();
                    $order->send_status = ParkingOrder::SEND_STATUS_DONE;
                    $order->save();
                }
            }

            MemberCar::createOrUpdate($order->member_id, $order->plate_number, $order->type);

            $pushData = [
                'parkingOrderId' => $order->id
            ];
            $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/parking-order-notify', $pushData);

            return true;
        })->Handle(false);
    }

    /**
     * 艾润道闸：临卡订单支付信息
     * @return string
     */
    public function actionIrainWxJs()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');
            $wechatSDK = new WechatSDK(\Yii::$app->params['wechat']);
            $parkingOrder = ParkingOrder::findOne($orderId);

            if ($parkingOrder && in_array($parkingOrder->status, [ParkingOrder::STATUS_DEFAULT, ParkingOrder::STATUS_WAIT])) {
                $parkingOrder->status = ParkingOrder::STATUS_WAIT;

                if ($parkingOrder->save()) {
                    $js = $wechatSDK->wxJsApiPay(
                        $parkingOrder->number,
                        $this->user->wechat_open_id,
                        $parkingOrder->amount,
                        [
                            'notifyUrl' => \Yii::$app->request->hostInfo . "/pay/irain-tempfee-wxnotify",
                            'goodsTag' => '停车缴费',
                            'subMchId' => \Yii::$app->params['wxPay']['subMchId'],
                        ]
                    );
                    return $this->renderJsonSuccess($js);
                }
            }
        }

        return $this->renderJsonFail("提交信息有误");
    }

    /**
     * 艾润道闸：临卡订单支付成功下发支付成功信息。
     */
    public function actionIrainTempfeeWxnotify()
    {
        WxPayCallBack::Notify(function ($data) {
            $order = ParkingOrder::findOne(['number' => $data['out_trade_no']]);
            if ($order === null)
                throw new NotFoundHttpException;

            //过滤微信会重复回到的可能 zhaowenxi
            if($order->status == ParkingOrder::STATUS_PAYED && $order->send_status == ParkingOrder::SEND_STATUS_DONE){
                return true;
            }

            $order->payed_at = time();
            $order->status = ParkingOrder::STATUS_PAYED;
            $order->transaction_id = $data['transaction_id'];
            $order->save();

            if(SysSwitch::inValue('testMember', $order->member_id)){
                $order->status = ParkingOrder::STATUS_TEST_PAYED;
                $order->save();
            } else {
                $amount = bcmul($order->amount, 100); //金额转换成分

                $postData = [
                    'serial_number' => $order->number,
                    'bill_id' => $order->calc_id,
                    'vpl_number' => $order->plate_number,
                    'park_code' => $order->projectParkingOneToOne->parking_id,
                    'amount' => $amount,
                    'pay_type' => IRain::PAY_TYPE_WECHAT,
                ];
                $tempCalcFeeOrder = (new IRain($order->projectParkingOneToOne->app_id, $order->projectParkingOneToOne->app_key))->createParkingTempPayOrder($postData);

                ParkingPayOrderLog::writeLog($tempCalcFeeOrder, $order->id, 'irain');

                if(isset($tempCalcFeeOrder['data']['order'])){
                    $order->send_at = time();
                    $order->send_status = ParkingOrder::SEND_STATUS_DONE;
                    $order->save();
                }
            }

            MemberCar::createOrUpdate($order->member_id, $order->plate_number, $order->type);

            $pushData = [
                'parkingOrderId' => $order->id
            ];
            $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/parking-order-notify', $pushData);

            return true;
        })->Handle(false);
    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $dataFormat
     * @return string content
     */
    private function http_post($url, $param, $dataFormat = false)
    {
        $strPOST = $param;
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if ($dataFormat) {
            $strPOST = json_encode($param);
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_NOSIGNAL, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * begin 在活动期间（2017-12-24 ~ 2018-02-21）缴费，符合条件的记录到已抵扣表中
     * @param $orderId
     * @param $houseId
     * @param $memberId
     * @return bool
     */
    private function recordPmChristmasBillItem($orderId, $houseId, $memberId)
    {
        $m = PmChristmasBillItem::findOne(['house_id' => $houseId]);
        if($m){
            return false;
        }

        $pmOrderDiscounts = PmOrderDiscounts::findOne(['pm_order_id' => $orderId]);
        if($pmOrderDiscounts){
            $model = new PmChristmasBillItem();
            $model->house_id = $houseId;
            $model->member_id = $memberId;

            return $model->save();
        }

        return false;
    }

    /**
     * end 更新业主使用红包状态
     * @param $orderId
     * @param $memberId
     * @param $houseId
     * @return bool
     */
    private function updateMemberRedPackStatus($orderId, $memberId, $houseId)
    {
        $memberPmOrderDiscount = PmOrderDiscounts::findOne(['pm_order_id' => $orderId]);
        if($memberPmOrderDiscount){
            if($memberPmOrderDiscount->red_pack_status == PmOrderDiscounts::RED_PACK_STATUS_USED){

                $model = MemberPromotionCode::find()->where([
                    'member_id' => $memberId,
                    'house_id' => $houseId,
                    'status' => MemberPromotionCode::STATUS_DEFAULT])->all();

                if($model){
                    return MemberPromotionCode::updateAll(
                        ['status' => MemberPromotionCode::STATUS_USED ],
                        [
                            'member_id' => $memberId,
                            'house_id' => $houseId,
                            'status' => MemberPromotionCode::STATUS_DEFAULT
                        ]
                    );
                }
            }
        }

        return false;
    }

    /**
     * 有偿维修支付
     * @return string
     * @throws \components\wechatSDK\lib\WxPayException
     * @author zhaowenxi
     */
    public function actionWxJsRepair()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');
            $wechatSDK = new WechatSDK(\Yii::$app->params['wechat']);
            $pmOder = PmOrder::findOne($orderId);
            if ($pmOder && in_array($pmOder->status, [PmOrder::STATUS_READY, PmOrder::STATUS_WAIT_PAY])) {

                $totalAmount = bcmul($pmOder->total_amount, 100);
                if(empty($totalAmount)){
                    return $this->renderJsonFail("空账单，无需付费！");
                }

                $pmOder->number = PmOrder::createNumber();
                $pmOder->status = PmOrder::STATUS_WAIT_PAY;
                $pmOder->pay_type = PmOrder::PAY_TYPE_SUB_WECHAT_GDZAWY;
                $pmOder->house_type = $pmOder->house->structure->group;
                if ($pmOder->save()) {
                    if (in_array($pmOder->member_id, \Yii::$app->params['test.member.id'])) {
                        $pmOder->total_amount = \Yii::$app->params['test_member_amount'];
                        $pmOder->save();
                    }
                    $js = $wechatSDK->wxJsApiPay(
                        $pmOder->number,
                        $this->user->wechat_open_id,
                        $pmOder->total_amount,
                        [
                            'notifyUrl' => \Yii::$app->request->hostInfo . "/pay/repair-wx-notify",
                            'goodsTag' => '维修缴费',
                            'subMchId' => \Yii::$app->params['wxPay']['subMchId'],
                        ]
                    );
                    return $this->renderJsonSuccess($js);
                }
            }
        }

        return $this->renderJsonFail("提交信息有误");
    }

    /**
     * 有偿维修支付回调
     * @author zhaowenxi
     */
    public function actionRepairWxNotify()
    {
        WxPayCallBack::Notify(function ($data) {

            $order = PmOrder::findOne(['number' => $data['out_trade_no']]);
            if ($order === null) {
                throw new NotFoundHttpException();
            }

            if($order->status < PmOrder::STATUS_PAYED){
                $order->payed_at = time();
                $order->status = PmOrder::STATUS_PAYED;
                $order->save();
            }

            //不在测试组并且金额不是1.00 都进行账单核销
            if (!in_array($order->member_id, \Yii::$app->params['test.member.id'])) {
                //账单核销部分
                foreach ($order->items as $item) {
                    if ($item->status != PmOrderItem::STATUS_WAIT) {
                        continue;
                    }
                    /**
                     * @var $item PmOrderItem
                     */
                    $res = (new NewWindow())->payBill(
                        $order->id . '-' . $item->id,
                        $order->house->project_house_id,
                        $item->contract_no,
                        $item->amount,
                        $order->payed_at
                    );
                    if ($res) {
                        $item->status = $res[0]['ReturnCode'];
                        $item->bankBillNo = $res[0]['BankBillNo'];
                        $item->completed_at = time();
                        $item->save();
                    }
                }

                $pushData = [
                    'pmOrderId' => $order->id,
                    'payStatus' => $order->status,
                ];

                $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/repair-notify', $pushData);

                return true;
            } else {
                $order->status = PmOrder::STATUS_TEST_PAYED;
                $order->save();
            }
            return true;
        })->Handle(false);
    }
}