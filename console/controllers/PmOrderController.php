<?php
/**
 * Created by PhpStorm.
 * User: HQ
 * Date: 2018/6/4
 * Time: 16:35
 */

namespace console\controllers;


use common\models\MemberPromotionCode;
use common\models\PmChristmasBillItem;
use common\models\PmOrder;
use common\models\PmOrderItem;
use yii\console\Controller;
use yii\helpers\Console;

class PmOrderController extends Controller
{
    /**
     * 物业缴费订单退款：pmOrderId,pmOrderNumber
     * @param $pmOrderId
     * @param $pmOrderNumber
     */
    public function actionRefund($pmOrderId, $pmOrderNumber)
    {
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId, 'number' => $pmOrderNumber, 'status' => PmOrder::STATUS_PAYED]);

        $this->stdout("pmOrderId：{$pmOrderId} || number：{$pmOrder->number}\n");

        $selectV = Console::select('-----房产：'.$pmOrder->house->ancestor_name, ['y' => '选择', 'n' => '跳过']);

        if($selectV == 'y'){
            $selectV = Console::select('-----房产：'.$pmOrder->house->ancestor_name, ['y' => '选择', 'n' => '跳过']);
        }

        if($selectV != 'y'){
            $this->stdout("退出 \n");
            exit(0);
        }

        if($pmOrder){
            $this->stdout("----------statusText：{$pmOrder->statusText}\n");

            $pmOrder->status = PmOrder::STATUS_REFUND;
            $pmOrder->refund_at = time();
            if($pmOrder->save()){
                $this->stdout("该订单已更改为：退款；正在查找其他关联业务……………………\n");

                $pmOrderItemC = PmOrderItem::find()->where(['pm_order_id' => $pmOrderId])->count();

                $this->stdout("--------------该订单有（{$pmOrderItemC}）条明细，正在更新其状态『2000』\n");

                PmOrderItem::updateAll(['status' => '2000'], ['pm_order_id' => $pmOrderId]);

                $this->stdout("--------------已初始化订单明细状态：2000\n");

                $diC = $pmOrder->discount_status;
                if($diC > 0){
                    $meProCode = MemberPromotionCode::findOne(['house_id' => $pmOrder->house_id, 'member_id' => $pmOrder->member_id, 'promotion_name' => 'auth']);
                    if($meProCode){
                        $this->stdout("该订单已使用优惠券：{$meProCode->amount}，正在初始化优惠券\n");
                        MemberPromotionCode::updateAll(['status' => 0], ['member_id' => $pmOrder->member_id, 'house_id' => $pmOrder->house_id]);
                        $this->stdout("--------------------已初始化该优惠券：{$meProCode->status}\n");

                    }

                    $pmChBillIte = PmChristmasBillItem::findOne(['house_id' => $pmOrder->house_id]);
                    if($pmChBillIte){
                        $this->stdout("该房产已使用优惠。从使用记录表中移除该房产\n");

                        PmChristmasBillItem::deleteAll(['house_id' => $pmOrder->house_id]);

                        $this->stdout("------------------------删除\n");
                    }

                    exit(0);
                }
            }
        } else {
            $this->stdout("--------\n");
        }

        exit(0);
    }
}