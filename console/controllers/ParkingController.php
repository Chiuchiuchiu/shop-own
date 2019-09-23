<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/21
 * Time: 16:24
 */

namespace console\controllers;


use common\models\ParkingOrder;
use common\models\ProjectParkingOneToOne;
use components\genvict\Genvict;
use yii\console\Controller;

class ParkingController extends Controller
{
    public function actionCreateTempOrder(int $projectId, int $orderId)
    {
        $projectParkingInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $projectId]);
        $parkingOrder = ParkingOrder::findOne(['id' => $orderId]);
        $orderInfo = [];
        $errors = [];

        switch($parkingOrder->type){
            case ParkingOrder::TYPE_T:
                $postData = [
                    'parkingid' => $projectParkingInfo->parking_id,
                    'plateno' => $parkingOrder->plate_number,
                    'receivable' => $parkingOrder->amount,
                    'calcid' => $parkingOrder->calc_id,
                    'paymenttype' => ParkingOrder::GEN_PAYMENT_WE,
                    'openid' => $parkingOrder->member->wechat_open_id,
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
                    'openid' => $parkingOrder->member->wechat_open_id,
                ];
                $monthCalcFeeOrder = (new Genvict($projectParkingInfo->app_id, $projectParkingInfo->app_key))->createParkingMonthPayOrder($postData);
                $orderInfo = $monthCalcFeeOrder->getValue();
                $errors = $monthCalcFeeOrder->getErrors();
                break;
        }

        var_export($orderInfo);
        var_export($errors);
    }

    public function actionPayNotify($orderId)
    {
        $order = ParkingOrder::findOne(['id' => $orderId]);
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
            $this->stdout(serialize($errors) . "\n");
        } else {
            $this->stdout(serialize($notifyRes) . "\n");
        }

        exit();
    }

    public function actionGetTempBill($projectHouseId, $plateNo)
    {
        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $projectHouseId]);
        $postData = [
            'parkingid' => $projectParkInfo->parking_id,
            'plateno' => $plateNo,
            'platecolor' => '蓝',
        ];

        $res = (new Genvict($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingFeeTempCalcFeePlateno($postData);
        $data = $res->getValue();

        if(empty($data)){
            $errors = $res->getErrors();
            $this->stdout("错误信息：" . serialize($errors) . "\n");
        } else {
            $this->stdout("调用返回：" . serialize($data) . "\n");
        }

        exit();
    }

}