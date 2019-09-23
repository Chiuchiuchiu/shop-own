<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/3
 * Time: 18:11
 */

namespace console\controllers;


use common\models\PmOrder;
use common\models\PmOrderItem;
use yii\console\Controller;

class ExportController extends Controller
{
    /**
     * 导出收费订单 -> $getStartTime, $getEndTime, $projectHouseId, $discountStatus
     * @param $getStartTime
     * @param $getEndTime
     * @param null $projectHouseId
     * @param null $discountStatus
     */
    public function actionExportParent($getStartTime, $getEndTime, $projectHouseId=null, $discountStatus=null)
    {
        $pmOrderWhere = PmOrder::STATUS_PAYED;

        $house_id = $projectHouseId;

        $rs = PmOrder::find()
            ->where(
                [
                    'status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN','payed_at', $getStartTime, $getEndTime])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->orderBy('payed_at DESC');


        //新增
        $pmOrderTotal = PmOrder::find()
            ->where(
                [
                    'status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN','payed_at', $getStartTime, $getEndTime])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->count();

        $defaultLimit = 1000;
        $pageCount = ceil($pmOrderTotal / $defaultLimit);
        $offset = 0;

        do{
            $defaultOffset = $defaultLimit * $offset;
            $model = PmOrder::find()
                ->where(
                    [
                        'status' => $pmOrderWhere
                    ]
                )
                ->limit($defaultLimit)
                ->offset($defaultOffset)
                ->andFilterWhere(['BETWEEN', 'payed_at', $getStartTime, $getEndTime])
                ->andFilterWhere(['project_house_id' => $house_id])
                ->andFilterWhere(['discount_status' => $discountStatus])
                ->orderBy('payed_at DESC')
                ->all();

            $str="订单号,地址,收费对象,手机号,合同号,类目,缴款金额,优惠,账期,付项,缴款时间\n";
            echo mb_convert_encoding($str,'GBK','UTF8');

            foreach($model as $row){
                /**
                 * @var $row PmOrder
                 */
                if(isset($row->pmOrderDiscounts)){
                    $discountAmount = $row->pmOrderDiscounts->discounts_amount;
                } else {
                    $discountAmount = '-';
                }

                $str = implode(',',[
                        "'{$row->number}'",
                        '-',
                        '-',
                        $row->member->phone,
                        '-',
                        '-',
                        '实付：' . number_format($row->total_amount,2,'.',''),
                        $discountAmount,
                        '-',
                        '-',
                        date('Y-m-d H:i:s', $row->payed_at)
                    ]). "\n";

                echo mb_convert_encoding($str,'GBK','UTF8');


                foreach($row->items as $items){
                    /**
                     * @var $items PmOrderItem
                     */
                    $customerName = strtr($items->customer_name, ',', '+');

                    $str = implode(',',[
                            '-',
                            $items->pmOrder->house->ancestor_name,
                            $customerName, $items->pmOrder->member->phone,
                            $items->contract_no,
                            $items->charge_item_name,
                            number_format($items->amount,2,'.',''),
                            '-',
                            $items->bill_date,
                            $items->charge_item_name,
                            date('Y-m-d H:i:s', $items->pmOrder->payed_at),
                        ])."\n";

                    echo mb_convert_encoding($str,'GBK','UTF8');
                }

            }



            $offset++;
            $pageCount--;

        }while($pageCount >= 0);

        die;
    }

    /**
     * 导出各项目优惠总汇 -> $getStartTime, $getEndTime
     * @param $getStartTime
     * @param $getEndTime
     * @param $projectHouseId null
     */
    public function actionExportProjectDiscountsItem($getStartTime, $getEndTime, $projectHouseId=null)
    {
        $pmOrderWhere = PmOrder::STATUS_PAYED;
        $discountBeginTime = '1513526400';
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];
        $tempSaveOrderItem = [];
        $projectInfo = [];

        if($getStartTime < $discountBeginTime){
            $this->stdout("订单开始时间不得小于 2017-12-18 \n");
            die;
        }

        $pmOrderTotal = PmOrder::find()
            ->where(
                [
                    'status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN', 'payed_at', $getStartTime, $getEndTime])
            ->andFilterWhere(['discount_status' => 1])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->count();

        $defaultLimit = 500;
        $pageCount = ceil($pmOrderTotal / $defaultLimit);
        $offset = 0;

        $str="项目,类目,总金额,总实付金额,总优惠金额\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        do{
            $defaultOffset = $defaultLimit * $offset;

            $rs = PmOrder::find()
                ->where(
                    [
                        'status' => $pmOrderWhere
                    ]
                )
                ->limit($defaultLimit)
                ->offset($defaultOffset)
                ->andFilterWhere(['BETWEEN', 'payed_at', $getStartTime, $getEndTime])
                ->andFilterWhere(['discount_status' => 1])
                ->andFilterWhere(['project_house_id' => $projectHouseId])
                ->orderBy('project_house_id DESC,id DESC')->all();

            foreach($rs as $row){
                /**
                 * @var $row PmOrder
                 */
                $discountAmount = $row->pmOrderDiscounts->discounts_amount;
                $orderTotalAmount = bcadd($row->total_amount, $discountAmount, 2);

                if(!isset($tempSaveOrderItem[$row->project_house_id])){
                    $tempSaveOrderItem[$row->project_house_id]['amount']['orderTotalAmount'] = 0;
                    $tempSaveOrderItem[$row->project_house_id]['amount']['orderPayTotalAmount'] = 0;
                    $tempSaveOrderItem[$row->project_house_id]['amount']['discountsTotalAmount'] = 0;
                    $tempSaveOrderItem[$row->project_house_id]['lists'] = [];
                    $projectInfo[$row->project_house_id]['name'] = $row->house->project->house_name;
                }

                $tempSaveOrderItem[$row->project_house_id]['amount']['orderTotalAmount'] = bcadd($tempSaveOrderItem[$row->project_house_id]['amount']['orderTotalAmount'], $orderTotalAmount, 2);
                $tempSaveOrderItem[$row->project_house_id]['amount']['orderPayTotalAmount'] = bcadd($tempSaveOrderItem[$row->project_house_id]['amount']['orderPayTotalAmount'], $row->total_amount, 2);
                $tempSaveOrderItem[$row->project_house_id]['amount']['discountsTotalAmount'] = bcadd($tempSaveOrderItem[$row->project_house_id]['amount']['discountsTotalAmount'], $discountAmount, 2);

                foreach($row->items as $items){
                    /**
                     * @var $items PmOrderItem
                     */
                    if(in_array($items->charge_item_id, $discountsChargeItemIds)){

                        if(!array_key_exists($items->charge_item_id, $tempSaveOrderItem[$row->project_house_id]['lists'])){
                            $tempSaveOrderItem[$row->project_house_id]['lists'][$items->charge_item_id] = [
                                'name' => $items->charge_item_name,
                                'totalAmount' => $items->amount
                            ];
                        } else {
                            $tempTotalAmount = bcadd($tempSaveOrderItem[$row->project_house_id]['lists'][$items->charge_item_id]['totalAmount'], $items->amount, 2);

                            $tempSaveOrderItem[$row->project_house_id]['lists'][$items->charge_item_id] = [
                                'name' => $items->charge_item_name,
                                'totalAmount' => $tempTotalAmount
                            ];
                        }
                    }
                }
            }

            unset($rs);

            if(!empty($tempSaveOrderItem)){
                foreach($tempSaveOrderItem as $tKey => $tRow){
                    $str = implode(',',[
                            $projectInfo[$tKey]['name'],
                            '所有费项总金额：',
                            number_format($tRow['amount']['orderTotalAmount'], 2, '.',''),
                            number_format($tRow['amount']['orderPayTotalAmount'], 2, '.',''),
                            number_format($tRow['amount']['discountsTotalAmount'], 2, '.',''),
                        ])."\n";

                    echo mb_convert_encoding($str,'GBK','UTF8');

                    foreach($tRow['lists'] as $key => $row){

                        $str = implode(',',[
                                '-',
                                $row['name'],
                                number_format($row['totalAmount'], 2, '.',''),
                                '-',
                                '-',
                            ])."\n";

                        echo mb_convert_encoding($str,'GBK','UTF8');
                    }
                }

            }

            unset($tempSaveOrderItem);
            unset($projectInfo);
            $offset++;
            $pageCount--;

        }while($pageCount >= 0);


        die();
    }

}