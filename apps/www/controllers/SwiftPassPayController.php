<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/11
 * Time: 16:43
 */

namespace apps\www\controllers;


use apps\www\models\MemberCar;
use common\models\MemberPromotionCode;
use common\models\NewRepairLog;
use common\models\ParkingLog;
use common\models\ParkingOrder;
use common\models\ParkingPayOrderLog;
use common\models\PayJsLog;
use common\models\PmChristmasBillItem;
use common\models\PmOrder;
use common\models\PmOrderDiscounts;
use common\models\PmOrderItem;
use common\models\ProjectParkingOneToOne;
use common\models\SysSwitch;
use components\genvict\Genvict;
use components\newWindow\NewWindow;
use components\swiftpass\SwiftPass;
use yii\web\NotFoundHttpException;

class SwiftPassPayController extends Controller
{
    protected $missPermission = ['swift-pass-pay/wx-notify', 'swift-pass-pay/repair-wx-notify', 'swift-pass-pay/mobile-wx-notify', 'swift-pass-pay/parking-wx-notify'];

    public $enableCsrfValidation = false;

    public function actionWxJs()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');

            $swiftPassPayParam = \Yii::$app->params['swiftPassPay'];

            if(isset($this->project->projectPayConfig)){
                $swiftPassPayParam['key'] = $this->project->projectPayConfig->key;
                $swiftPassPayParam['mchId'] = $this->project->projectPayConfig->mch_id;
            }

            $swiftPassPay = new SwiftPass($swiftPassPayParam);

            $pmOder = PmOrder::findOne($orderId);
            if ($pmOder && in_array($pmOder->status, [PmOrder::STATUS_READY, PmOrder::STATUS_WAIT_PAY])) {
                $totalAmount = bcmul($pmOder->total_amount, 100);
                if(empty($totalAmount)){
                    return $this->renderJsonFail("空账单，无需付费！");
                }
                $pmOder->number = PmOrder::createNumber();
                $pmOder->status = PmOrder::STATUS_WAIT_PAY;
                $pmOder->pay_type = PmOrder::PAY_TYPE_SUB_SW_GDZAWY;
                $pmOder->house_type = $pmOder->house->structure->group;
                if ($pmOder->save()) {
                    if (in_array($pmOder->member_id, \Yii::$app->params['test.member.id'])) {
                        $pmOder->total_amount = \Yii::$app->params['test_member_amount'];
                        $pmOder->save();
                    }
                    $js = $swiftPassPay->submitOrderInfo(
                        $this->user->wechat_open_id,
                        $pmOder->number,
                        $pmOder->total_amount,
                        \Yii::$app->request->hostInfo."/swift-pass-pay/wx-notify"
                    );



                    return $this->renderJsonSuccess($js);
                }
            }
        }

        return $this->renderJsonFail("提交信息有误");
    }

    public function actionWxNotify()
    {
        $xml = file_get_contents('php://input');

        $swiftPass = new SwiftPass(\Yii::$app->params['swiftPassPay']);

        $swiftPass->resHandler->setContent($xml);
//        $swiftPass->resHandler->setKey($swiftPass->cfg['key']);
        if($swiftPass->resHandler->getParameter('status') == 0 && $swiftPass->resHandler->getParameter('result_code') == 0){
            $tradeno = $swiftPass->resHandler->getParameter('out_trade_no');
            // 此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
            //更改订单状态

            $order = PmOrder::findOne(['number' => $tradeno]);
            if ($order === null){
                throw new NotFoundHttpException();
            }

            if($order->status < PmOrder::STATUS_PAYED){
                $order->payed_at = time();
                $order->status = PmOrder::STATUS_PAYED;
                $order->save();
            }

            //不在测试组并且金额不是1.00 都进行账单核销
            if (!SysSwitch::inVal('testPayMember', $order->member_id)) {
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

                echo 'success';
                exit();
            } else {
                $order->status = PmOrder::STATUS_TEST_PAYED;
                $order->save();

                $pushData = [
                    'pmOrderId' => $order->id,
                    'butlerUserId' => 'huangqimin',
                ];
                $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/notify', $pushData);
            }

            echo 'success';
            exit();
        }else{
            $pushData = [
                'data' => $swiftPass->resHandler->getAllParameters(),
            ];
            $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/re-log', $pushData);

            echo 'failure1';
            exit();
        }

    }

    //停车费
    public function actionWxJsParking()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');
            $swiftPassPay = new SwiftPass(\Yii::$app->params['swiftPassPay']);


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
                    $js = $swiftPassPay->submitOrderInfo(
                        $this->user->wechat_open_id,
                        $parkingOrder->number,
                        $parkingOrder->amount,
                        \Yii::$app->request->hostInfo."/swift-pass-pay/wx-js-parking"
                    );
                    return $this->renderJsonSuccess($js);
                }
            }
        }
        return $this->renderJsonFail("提交信息有误");
    }

    public function actionParkingWxNotify()
    {
        $xml = file_get_contents('php://input');

        $swiftPass = new SwiftPass(\Yii::$app->params['swiftPassPay']);

        $swiftPass->resHandler->setContent($xml);
        $swiftPass->resHandler->setKey($swiftPass->cfg['key']);
        if($swiftPass->resHandler->isTenpaySign()){
            if($swiftPass->resHandler->getParameter('status') == 0 && $swiftPass->resHandler->getParameter('result_code') == 0){
                $tradeno = $swiftPass->resHandler->getParameter('out_trade_no');
                // 此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                //更改订单状态

                $order = ParkingOrder::findOne(['number' => $tradeno]);
                if ($order === null)
                    throw new NotFoundHttpException();
                $order->payed_at = time();
                $order->status = ParkingOrder::STATUS_PAYED;
                $order->save();
                //不在测试组并且金额不是1.00 都进行账单核销
                if (!SysSwitch::inVal('testPayMember', $order->member_id)) {
                    //账单核销部分
                    $postData = [
                        'parkingid' => $order->projectParkingOneToOne->parking_id,
                        'orderid' => $order->calc_id,
                        'transid' => $order->transaction_id,
                        'paidin' => $order->amount,
                        'discount' => $order->disc,
                        'returninfo' => 'SUCCESS',
                        'paytime' => date('YmdHis', $order->payed_at),
                        'status' => 1,
                    ];
                    $gResponse = (new Genvict($order->projectParkingOneToOne->app_id, $order->projectParkingOneToOne->app_key))->payResultNotify($postData);
                    $notifyRes = $gResponse->getValue();
                    $errors = $gResponse->getErrors();
                    if(empty($notifyRes)){
                        ParkingPayOrderLog::writeLog($errors, $order->id, 'g');
                    } else {
                        $order->send_at = time();
                        $order->send_status = ParkingOrder::SEND_STATUS_DONE;
                        $order->save();
                    }

                } else {
                    $order->status = ParkingOrder::STATUS_TEST_PAYED;
                    $order->save();
                }

                MemberCar::createOrUpdate($order->member_id, $order->plate_number, $order->type);

                echo 'success';
                exit();
            }else{
                echo 'failure1';
                exit();
            }
        }else{
            echo 'failure2';
        }

        exit();
    }

    /**
     * 有偿维修支付
     * @return string
     * @author zhaowenxi
     */
    public function actionWxJsRepair()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');

            $swiftPassPayParam = \Yii::$app->params['swiftPassPay'];

            if(isset($this->project->projectPayConfig)){
                $swiftPassPayParam['key'] = $this->project->projectPayConfig->key;
                $swiftPassPayParam['mchId'] = $this->project->projectPayConfig->mch_id;
            }

            $swiftPassPay = new SwiftPass($swiftPassPayParam);

            $pmOder = PmOrder::findOne($orderId);

            if ($pmOder && in_array($pmOder->status, [PmOrder::STATUS_READY, PmOrder::STATUS_WAIT_PAY])) {
                $totalAmount = bcmul($pmOder->total_amount, 100);
                if(empty($totalAmount)){
                    return $this->renderJsonFail("空账单，无需付费！");
                }

                $pmOder->status = PmOrder::STATUS_WAIT_PAY;
                $pmOder->pay_type = PmOrder::PAY_TYPE_SUB_SW_GDZAWY;
                $pmOder->house_type = $pmOder->house->structure->group;

                if ($pmOder->save()) {
                    if (in_array($pmOder->member_id, \Yii::$app->params['test.member.id'])) {

                        $pmOder->total_amount = \Yii::$app->params['test_member_amount'];
                        $pmOder->save();
                    }
                    $js = $swiftPassPay->submitOrderInfo(
                        $this->user->wechat_open_id,
                        $pmOder->number,
                        $pmOder->total_amount,
                        \Yii::$app->request->hostInfo."/swift-pass-pay/repair-wx-notify"
                    );

                    return $this->renderJsonSuccess($js);
                }
            }
        }

        return $this->renderJsonFail("提交信息有误");
    }

    /**
     * 有偿维修支付回调
     * @throws NotFoundHttpException
     * @throws \yii\base\ErrorException
     * @author zhaowenxi
     */
    public function actionRepairWxNotify()
    {
        $xml = file_get_contents('php://input');

        $swiftPass = new SwiftPass(\Yii::$app->params['swiftPassPay']);

        $swiftPass->resHandler->setContent($xml);

        if($swiftPass->resHandler->getParameter('status') == 0 && $swiftPass->resHandler->getParameter('result_code') == 0){
            $tradeno = $swiftPass->resHandler->getParameter('out_trade_no');
            // 此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
            //更改订单状态

            $order = PmOrder::findOne(['number' => $tradeno]);
            if ($order === null){
                throw new NotFoundHttpException();
            }

            if($order->status < PmOrder::STATUS_PAYED){
                $order->payed_at = time();
                $order->status = PmOrder::STATUS_PAYED;
                $order->save();
            }

            //不在测试组并且金额不是1.00 都进行账单核销
            if (!SysSwitch::inVal('testPayMember', $order->member_id)) {
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
                $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/notify', $pushData);

                echo 'success';
                exit();
            } else {
                $order->status = PmOrder::STATUS_TEST_PAYED;
                $order->save();

                $pushData = [
                    'pmOrderId' => $order->id,
                    'butlerUserId' => 'zhaowenxi',
                ];
                $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/notify', $pushData);
            }

            $pushData = [
                'data' => $swiftPass->resHandler->getAllParameters(),
            ];
            $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/re-log', $pushData);
            
            echo 'success';
            exit();
        }else{
            $pushData = [
                'data' => $swiftPass->resHandler->getAllParameters(),
            ];
            $this->http_post('http://testmgt.51homemoney.com/member-bill-notify/re-log', $pushData);

            echo 'failure1';
            exit();
        }

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


}