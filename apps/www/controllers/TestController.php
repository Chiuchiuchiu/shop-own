<?php
/**
 * Created by
 * Author: zhao
 * Time: 2017/1/9 11:56
 * Description:
 */

namespace apps\www\controllers;

use common\models\House;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\SysSwitch;
use components\newWindow\NewWindow;
use yii\db\Connection;

class TestController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPayDemo()
    {
        if(!SysSwitch::inVal('testPayMember', $this->user->id)){
            die('测试使用，无法通过');
        }

        return $this->render('pay-demo');
    }

    public function actionPayDemoSubmit()
    {
        $orderItems[] = [
            'ContractNo' => '45-33507028-1',
            'ChargeItemID' => '69',
            'ChargeItemName' => '梯灯公摊费',
            'BillAmount' => \Yii::$app->params['test_member_amount'],
            'BillDate' => '20170201-20170228',
        ];

        $house = House::findOne(['house_id' => 73692]);
        $amount = $this->post('amount');

        $order = new PmOrder();
        $order->house_id = $house->house_id;
        $order->project_house_id = $house->project_house_id;
        $order->member_id = $this->user->id;
        $order->total_amount = $amount;
        $res = PmOrder::getDb()->transaction(function (Connection $db) use ($order, $orderItems) {
            if (!$order->save()) {
                $resMessage = $order->getFirstErrors();
                $db->getTransaction()->rollBack();
                return $resMessage;
            }
            foreach ($orderItems as $val){
                $orderItem = new PmOrderItem();
                $orderItem->pm_order_id = $order->id;
                $orderItem->contract_no = $val['ContractNo'];
                $orderItem->charge_item_id = $val['ChargeItemID'];
                $orderItem->charge_item_name = $val['ChargeItemName'];
                $orderItem->amount = round($val['BillAmount'], 2);
                $orderItem->bill_date = $val['BillDate'];
                $orderItem->price = 3.8500;
                $orderItem->usage_amount = 107.57;
                $orderItem->bill_content = serialize($val);
                if (!$orderItem->save()) {
                    $resMessage = $orderItem->getErrors();
                    $db->getTransaction()->rollBack();
                    return $resMessage;
                };
            }
            return true;
        });

        if ($res === true) {
            return $this->renderJsonSuccess(['id' => $order->id]);
        } else {
            return $this->renderJsonFail('error', -1, $res);
        }
    }
    public function actionDemoNewWindow()
    {
       $NewWin = new NewWindow();
       $loginkey = $NewWin->houseStructure(1);
       print_r($loginkey);


    }

}