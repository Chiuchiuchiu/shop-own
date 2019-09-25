<?php
/**
 * 民生支付
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/28
 * Time: 16:33
 */

namespace apps\www\controllers;

use common\models\MemberPromotionCode;
use common\models\PmChristmasBillItem;
use common\models\PmOrder;
use common\models\PmOrderDiscounts;
use common\models\PmOrderItem;
use common\models\SysSwitch;

use components\minSheng\MinSheng;
use yii\log\FileTarget;
use yii\web\NotFoundHttpException;

class MinShengController extends Controller
{
    protected $missPermission = ['min-sheng/wx-notify', 'min-sheng/repair-wx-notify', 'min-sheng/mobile-wx-notify', 'min-sheng/parking-wx-notify'];

    public $enableCsrfValidation = false;

    public function actionWxJs()
    {
        if ($this->isPost) {
            $orderId = $this->post('orderId');

            $pmOder = PmOrder::findOne($orderId);

            if ($pmOder && in_array($pmOder->status, [PmOrder::STATUS_READY, PmOrder::STATUS_WAIT_PAY])) {
                $totalAmount = bcmul($pmOder->total_amount, 100);
                if(empty($totalAmount)){
                    return $this->renderJsonFail("空账单，无需付费！");
                }

                $pmOder->number = PmOrder::createNumber();
                $pmOder->mch_seq_no = PmOrder::createNumber() . rand(0,99);    //民生流水号
                $pmOder->status = PmOrder::STATUS_WAIT_PAY;
                $pmOder->pay_type = PmOrder::PAY_TYPE_SUB_MS_GDAZWY;
                $pmOder->house_type = $pmOder->house->structure->group;

                if ($pmOder->save()) {
                    if (in_array($pmOder->member_id, \Yii::$app->params['test.member.id'])) {
                        $pmOder->total_amount = \Yii::$app->params['test_member_amount'];
                        $pmOder->save();

                        $totalAmount = bcmul($pmOder->total_amount, 100);
                    }

                    $time = time();

                    $orderInfo = [
                        'platformId' => \Yii::$app->params['minShengPay']['platform_id'],
                        'merchantNo' => \Yii::$app->params['minShengPay']['merchant_id'],
                        'selectTradeType' => MinSheng::SELECT_TRADE_WXJSAPI,  //JSAPI支付
                        'amount' => $totalAmount,           //单位分
                        'orderInfo' => "物业服务费",
                        'merchantSeq' => $pmOder->number,
                        'mchSeqNo' => $pmOder->mch_seq_no,
                        'transDate' => date('Ymd', $time),
                        'transTime' => date('Ymdhis', $time),
                        'notifyUrl' => \Yii::$app->request->hostInfo."/min-sheng/wx-notify",
                        'subAppId' => \Yii::$app->params['wechat']['appId'],
                        'subOpenId' => $this->user->wechat_open_id,
                    ];

                    $model = new MinSheng();
                    $js = $model->submitOrderInfo($orderInfo);

                    if($js['code'] != 0){
                        return $this->renderJsonFail("商户支付信息有误，err:{$js['code']}");
                    }

                    return $this->renderJson($js);
                }
            }
        }

        return $this->renderJsonFail("提交信息有误");
    }

    public function actionWxNotify()
    {
        $contents = file_get_contents('php://input');

        $model = new MinSheng();

        $result = $model->openNotify($contents);

        if($result['code'] != 0){
            echo 'FAILURE';
            exit();
        }

        $order = PmOrder::findOne(['number' => $result['data']->orderNo]);
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

            echo 'SUCCESS';
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

        echo 'SUCCESS';
        exit();
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

}