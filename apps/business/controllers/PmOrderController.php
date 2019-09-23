<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/7 16:55
 * Description:
 */

namespace apps\business\controllers;


use common\models\House;
use common\models\MemberPromotionCode;
use common\models\PmChristmasBillItem;
use common\models\PmOrder;
use common\models\PmOrderDiscounts;
use common\models\PmOrderItem;
use common\models\PmOrderRefund;
use common\models\Project;
use common\valueObject\RangDateTime;
use components\minSheng\MinSheng;
use yii\base\Object;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class PmOrderController extends Controller
{
    public function actionItem($status=null)
    {
        $whereStatus = null;
        $pmOrderWhere = PmOrder::STATUS_PAYED;

        switch((string) $status){
            case '0':
                $whereStatus = '0000';
                break;
            case '1':
                $whereStatus = ['1000', '2001'];
                break;
            case '2':
                $whereStatus = '2000';
                $pmOrderWhere = PmOrder::STATUS_REFUND;
                break;
        }

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id', null);
        $cName = $this->get('cName', null);
        $discountStatus = $this->get('discountStatus', null);

        $Amount = PmOrderItem::find()->joinWith('pmOrder')
            ->where(
                [
                    'pm_order.status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['pm_order_item.status' => $whereStatus])
            ->andFilterWhere(['BETWEEN','pm_order.payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['pm_order.project_house_id' => $house_id])
            ->andFilterWhere(['pm_order.discount_status' => $discountStatus])
            ->andFilterWhere(['like', 'pm_order_item.customer_name', $cName])
            ->sum('amount');

        $pmOrderCount = PmOrder::find()
            ->where(['status' => $pmOrderWhere])
            ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->count();


        $projects = $this->projectCache();

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderItem::find()->joinWith('pmOrder')
            ->where(
                [
                    'pm_order.status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['pm_order_item.status' => $whereStatus])
            ->andFilterWhere(['BETWEEN','pm_order.payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['pm_order.project_house_id' => $house_id])
            ->andFilterWhere(['pm_order.discount_status' => $discountStatus])
            ->andFilterWhere(['like', 'pm_order_item.customer_name', $cName])
            ->orderBy('payed_at DESC');

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider->setSort(false);
        return $this->render('item', [
            'dataProvider'=>$dataProvider,
            'dateTime'=>$dateTime,
            'house_id' => $house_id,
            'Amount' => $Amount,
            'status' => $status,
            'discountStatus' => $discountStatus,
            'cName' => $cName,
            'projects' => $projectsArray,
            'pmOrderCount' => $pmOrderCount,
        ]);
    }

    public function actionOrderLists()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $discountStatus = $this->get('discountStatus', null);
        $status = $this->get('status', null);
        $number = $this->get('number', null);
        $payType = $this->get('payType');
        $whereStatus = PmOrder::STATUS_PAYED;
        $wherePayType = null;

        switch ((string)$status) {
            case '0':
                $whereStatus = PmOrder::STATUS_REFUND;
                break;
        }
        switch ($payType){
            case 1:
            case 2:
                $wherePayType = 2;
                break;
            case 3:
                $wherePayType = 3;
                break;
            case 4:
                $wherePayType = 4;
                break;
            case 5:
                $wherePayType = 5;
                break;
        }

        $projectsArray = $this->projectCache();
        $projects = [];
        $projects[''] = '全部';
        $projects['项目列表'] = ArrayHelper::map($projectsArray, 'house_id', 'house_name');

        $Amount = PmOrder::find()
            ->where(
                [
                    'status' => $whereStatus,
                ]
            )
            ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->andFilterWhere(['number' => $number])
            ->andFilterWhere(['pay_type' => $wherePayType])
            ->sum('total_amount');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrder::find()
            ->where(['status' => $whereStatus])
            ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->andFilterWhere(['number' => $number])
            ->andFilterWhere(['pay_type' => $wherePayType])
            ->orderBy("id DESC");

        $dataProvider->setSort(false);

        return $this->render('order-lists', get_defined_vars());
    }

    public function actionShowItem($pmOrderId)
    {
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId]);

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderItem::find()
            ->where(['pm_order_id' => $pmOrderId]);

        $dataProvider->setSort(false);

        return $this->render('show-item', [
            'dataProvider' => $dataProvider,
            'pmOrderStatus' => $pmOrder->status,
        ]);
    }

    public function actionUpdate()
    {
        if($this->isAjax){
            $id = intval($this->post('id'));
            if(!empty($id)){
                $model = PmOrderItem::findOne($id);
                $model->status = '2000';
                $model->m_id = $this->user->id;

                if($model->save()){
                    return $this->renderJsonSuccess(['message' => '退款成功']);
                }
            }
        }

        return $this->renderJsonFail('退款失败');
    }

    public function actionExport($status=null)
    {
        //新的方法
        $whereStatus = null;
        $pmOrderWhere = PmOrder::STATUS_PAYED;

        switch((string) $status){
            case '0':
                $whereStatus = '0000';
                break;
            case '1':
                $whereStatus = ['1000', '2001'];
                break;
            case '2':
                $whereStatus = '2000';
                break;
        }

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $cName = $this->get('cName', null);
        $discountStatus = $this->get('discountStatus', null);

        $pmOrderTotal = PmOrderItem::find()->joinWith('pmOrder')
            ->where(
                [
                    'pm_order.status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['pm_order_item.status' => $whereStatus])
            ->andFilterWhere(['BETWEEN','pm_order.payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['pm_order.project_house_id' => $house_id])
            ->andFilterWhere(['pm_order.discount_status' => $discountStatus])
            ->andFilterWhere(['like', 'pm_order_item.customer_name', $cName])
            ->count();

        $defaultLimit = 500;
        $pageCount = ceil($pmOrderTotal / $defaultLimit);
        $offset = 0;

        $projectName = '当前数据报表--';

        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="地址,收费对象,手机号,合同号,项目,缴款金额,账期,付项,缴款时间\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        do{
            $defaultOffset = $defaultLimit * $offset;

            $rs = PmOrderItem::find()->joinWith('pmOrder')
                ->where(
                    [
                        'pm_order.status' => $pmOrderWhere
                    ]
                )
                ->andFilterWhere(['pm_order_item.status' => $whereStatus])
                ->andFilterWhere(['BETWEEN','pm_order.payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
                ->andFilterWhere(['pm_order.project_house_id' => $house_id])
                ->andFilterWhere(['pm_order.discount_status' => $discountStatus])
                ->andFilterWhere(['like', 'pm_order_item.customer_name', $cName])
                ->limit($defaultLimit)
                ->offset($defaultOffset)
                ->orderBy('pm_order.payed_at DESC')->all();

            foreach($rs as $row){
                /**
                 * @var $row PmOrderItem
                 */
                $customerName = strtr($row->customer_name, ',', '+');
                $ancestorName = strtr($row->pmOrder->house->ancestor_name, ',', '，');

                $str = implode(',',[
                        $ancestorName,
                        $customerName,
                        $row->pmOrder->member->phone,
                        $row->contract_no,
                        $row->pmOrder->house->project->house_name,
                        number_format($row->amount,2,'.',''),
                        $row->bill_date,
                        $row->charge_item_name,
                        date('Y-m-d H:i:s', $row->pmOrder->payed_at)])."\n";

                echo mb_convert_encoding($str,'GBK','UTF8');
            }


            unset($rs);

            ob_flush();
            flush();

            $offset++;
            $pageCount--;

        }while($pageCount >= 0);


        die();
    }

    public function actionPaymentExport($status=null)
    {
        $whereStatus = null;
        $pmOrderWhere = PmOrder::STATUS_PAYED;

        switch((string) $status){
            case '0':
                $whereStatus = '0000';
                break;
            case '1':
                $whereStatus = ['1000', '2001'];
                break;
            case '2':
                $whereStatus = '2000';
                $pmOrderWhere = PmOrder::STATUS_REFUND;
                break;
        }

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');

        $rs = PmOrderItem::find()
            ->select('project.house_id, project.area, project.house_name, pm_order_item.charge_item_name, SUM(pm_order_item.amount) as amount')
            ->leftJoin('pm_order', 'pm_order_item.pm_order_id = pm_order.id')
            ->leftJoin('project', 'pm_order.project_house_id = project.house_id')
            ->where(
                [
                    'pm_order.status' => $pmOrderWhere,
                ]
            )
            ->andFilterWhere(['pm_order_item.status' => $whereStatus])
            ->andFilterWhere(['BETWEEN', 'pm_order.payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['pm_order.project_house_id' => $house_id])
            ->groupBy('pm_order.project_house_id, charge_item_id')
            ->orderBy('project.house_id, pm_order_item.id')->asArray()->all();

        $projectName = '缴费数据报表--';
        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="房产,收费项目,应收金额\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        $newArray = [];

        foreach ($rs as $key => $val){
            $newArray[$val['house_id']][] = $val;
            if(array_key_exists($val['house_id'], $newArray)){
                if(isset($newArray[$val['house_id']]['sum'])){
                    $newArray[$val['house_id']]['sum'] += $val['amount'];
                } else {
                    $newArray[$val['house_id']]['sum'] = $val['amount'];
                }
            }
        }

        foreach($newArray as $keys => &$row){

            $pushData = [
                'house_name' => $row[0]['house_name'],
                'charge_item_name' => '小计',
                'amount' => $row['sum'],
            ];
            array_push($newArray[$keys], $pushData);

            foreach ($row as $values){
                if(is_array($values)){
                    $str = implode(',',[
                                $values['house_name'],
                                $values['charge_item_name'],
                                number_format($values['amount'],2,'.',''),
                            ]
                        )."\n";
                    echo mb_convert_encoding($str,'GBK','UTF8');
                }
            }
        }

        die();
    }

    public function actionFindProject()
    {
        $list = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        return $this->renderJsonSuccess(['list'=>$list]);
    }

    public function actionExportParent()
    {
        $pmOrderWhere = PmOrder::STATUS_PAYED;

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $discountStatus = $this->get('discountStatus', null);

        $rs = PmOrder::find()
            ->where(
                [
                    'status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->orderBy('payed_at DESC');

        $projectName = '当前数据报表--';

        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="订单号,地址,收费对象,手机号,合同号,类目,缴款金额,优惠,账期,付项,缴款时间\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        foreach($rs->each() as $row){
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
                $ancestorName = strtr($items->pmOrder->house->ancestor_name, ',', '+');

                $str = implode(',',[
                        '-',
                        $ancestorName,
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
        die();
    }

    public function actionExportProjectOrderAmount()
    {
        $pmOrderWhere = PmOrder::STATUS_PAYED;
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $rs = PmOrder::find()
            ->select('pm_order.project_house_id, SUM(pm_order.total_amount) AS total_amount, project.house_id, project.house_name AS number')
            ->leftJoin('project', 'pm_order.project_house_id=project.house_id')
            ->where(
                [
                    'pm_order.status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->groupBy('pm_order.project_house_id');

        $projectName = '各项目缴费总额--';

        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="#,项目名,缴款金额\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        foreach($rs->each() as $row){
            /**
             * @var $row PmOrder
             */

            $str = implode(',',[
                    $row->project_house_id,
                    $row->number,
                    number_format($row->total_amount,2,'.',''),
                ]). "\n";

            echo mb_convert_encoding($str,'GBK','UTF8');
        }
        die();
    }

    public function actionExportDiscountsItem()
    {
        $pmOrderWhere = PmOrder::STATUS_PAYED;
        $discountBeginTime = '1513526400';
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $getStartTime = $dateTime->getStartTime();
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];
        $tempSaveOrderItem = [];
        $projectInfo = [];

        if($getStartTime < $discountBeginTime){
            $this->setFlashError('订单开始时间不得小于 2017-12-18');
            return $this->backRedirect();
            die;
        }

        $pmOrderTotal = PmOrder::find()
            ->where(
                [
                    'status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN', 'payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['discount_status' => 1])
            ->count();

        $defaultLimit = 500;
        $pageCount = ceil($pmOrderTotal / $defaultLimit);
        $offset = 0;

        $projectName = '各项目优惠类目汇总--';

        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
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
                ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
                ->andFilterWhere(['discount_status' => 1])
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

            ob_flush();
            flush();
            unset($tempSaveOrderItem);
            unset($projectInfo);
            $offset++;
            $pageCount--;

        }while($pageCount >= 0);


        die();
    }

    public function actionExportDiscountsNotItem()
    {
        $pmOrderWhere = PmOrder::STATUS_PAYED;

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $discountStatus = $this->get('discountStatus', null);


        $pmOrderTotal = PmOrder::find()
            ->where(
                [
                    'status' => $pmOrderWhere
                ]
            )
            ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['discount_status' => $discountStatus])
            ->count();

        $defaultLimit = 500;
        $pageCount = ceil($pmOrderTotal / $defaultLimit);
        $offset = 0;

        $projectName = '当前数据报表（无明细）--';
        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="订单号,房产,收费对象,手机号,合同号,类目,缴款金额,优惠,账期,付项,缴款时间\n";
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
                ->andFilterWhere(['BETWEEN','payed_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
                ->andFilterWhere(['project_house_id' => $house_id])
                ->andFilterWhere(['discount_status' => $discountStatus])
                ->orderBy('payed_at DESC')->all();

            if(!$rs){
                break;
            }

            foreach($rs as $row){
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
                        $row->house->ancestor_name,
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
            }

            unset($tempSaveOrderItem);
            unset($projectInfo);
            $offset++;
            $pageCount--;

        } while($pageCount >= 0);

        die();
    }

    /**
     * 退款
     * @param $pmOrderId
     * @return string|\yii\web\Response
     * @throws Exception
     * @author zhaowenxi
     */
    public function actionRefund($pmOrderId){

        $orderInfo = PmOrder::findOne(['id' => $pmOrderId, 'status' => PmOrder::STATUS_PAYED]);
        $model = new PmOrderRefund();

        if($this->isPost && $model->load($this->post())) {

            $isExist = PmOrderRefund::findOne(['pm_order_id' => $pmOrderId]);

            if($isExist){
                $this->setFlashErrors(["已有退款记录，请勿重复提交"]);
                return $this->refresh();
            }

            $model->pm_order_id      = $orderInfo->id;
            $model->house_id         = $orderInfo->house_id;
            $model->project_house_id = $orderInfo->project_house_id;
            $model->refund_number    = PmOrderRefund::createNumber();
            $model->amount           = $orderInfo->total_amount;
            $model->ip               = \Yii::$app->request->getUserIP();
            $model->status           = PmOrderRefund::STATUS_READY;
            $model->save();

            $transaction = PmOrderRefund::getDb()->beginTransaction();

            try{

                $minShengQuery = [
                    'platformId' => \Yii::$app->params['minShengPay']['platform_id'],
                    'merchantNo' => \Yii::$app->params['minShengPay']['merchant_id'],
                    'merchantSeq' => $orderInfo->number,
                    'mchSeqNo' => $model->refund_number,
                    'orderAmount' => bcmul($model->amount, 100),
                    'orderNote' => $model->reason,
                ];

                $msResult = (new MinSheng())->refund($minShengQuery);

                if($msResult['code'] != 0){

                    $msg = "商户退款失败，原因：" . (isset($msResult['data']) ? $msResult['data'] : $msResult['code']);

                    throw new Exception($msg);
                }

                switch ($msResult['data']->tradeStatus){
                    case "S": $model->status = PmOrderRefund::STATUS_SUCCESS;break;
                    case "E": $model->status = PmOrderRefund::STATUS_FAIL;break;
                    case "R": $model->status = PmOrderRefund::STATUS_WAIT;break;
                }

                $model->updated_at = time();
                $model->result = serialize($msResult['data']);

                if(!$model->save()){
                    throw new Exception("更新退款信息有误");
                }

                //更新原订单信息
                $orderInfo->status    = PmOrder::STATUS_REFUND;
                $orderInfo->refund_at = time();

                if(!$orderInfo->save()){
                    throw new Exception("原订单信息更新有误");
                }

                PmOrderItem::updateAll(['status' => '2000'], ['pm_order_id' => $pmOrderId]);

                $diC = $orderInfo->discount_status;

                if($diC > 0){
                    $meProCode = MemberPromotionCode::findOne(['house_id' => $orderInfo->house_id, 'member_id' => $orderInfo->member_id, 'promotion_name' => 'auth']);

                    if($meProCode){
                        MemberPromotionCode::updateAll(['status' => 0], ['member_id' => $orderInfo->member_id, 'house_id' => $orderInfo->house_id]);
                    }

                    $pmChBillIte = PmChristmasBillItem::findOne(['house_id' => $orderInfo->house_id]);

                    if($pmChBillIte){
                        PmChristmasBillItem::deleteAll(['house_id' => $orderInfo->house_id]);
                    }
                }

                $transaction->commit();

                $this->setFlashSuccess();

            }catch (Exception $e){

                $transaction->rollBack();

                $this->setFlashErrors([$e->getMessage()]);
            }

            return $this->redirect('order-lists');
        }

        return $this->render('refund', ['model' => $model, 'orderInfo' => $orderInfo]);
    }

    /**
     * 查询订单
     * @param $id
     * @return string
     */
    public function actionOrderCheck($id){
        $pmOrder = PmOrder::findOne($id);

        if($pmOrder){
            $minShengQuery = [
                'platformId' => \Yii::$app->params['minShengPay']['platform_id'],
                'merchantNo' => \Yii::$app->params['minShengPay']['merchant_id'],
                'merchantSeq' => $pmOrder->number,
                'mchSeqNo' => $pmOrder->mch_seq_no,
            ];

            $msResult = (new MinSheng())->orderCheck($minShengQuery);

            if($msResult['code'] != 0){
                return $this->renderJsonFail("商户系统没有此订单");
            }

            if($pmOrder->status == PmOrder::STATUS_WAIT_PAY && $msResult['data']->tradeStatus == "S"){
                if(!$this->nwPayBill($pmOrder)){
                    return $this->renderJsonFail("该订单支付状态异常，未能核销。请联系技术人员");
                }
            }

            return $this->renderJsonSuccess([
                'status' => $msResult['data']->tradeStatus,
                'remark' => $msResult['data']->remark,
                'amount' => bcdiv($msResult['data']->amount, 100, 2),
            ]);

        }else{
            return $this->renderJsonFail("没有此订单");
        }
    }

    /**
     * 退款列表
     * @author zhaowenxi
     */
    public function actionRefundLists(){

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id', null);
        $status   = $this->get('status', null);
        $number   = $this->get('number', null);

        $projectsArray = $this->projectCache();
        $projects = [];
        $projects[''] = '全部';
        $projects['项目列表'] = ArrayHelper::map($projectsArray, 'house_id', 'house_name');

        $pmData = PmOrderRefund::find()
            ->filterWhere(['status' => $status])
            ->andFilterWhere(['BETWEEN','created_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['refund_number' => $number]);

        $Amount = $pmData->sum('amount');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $pmData;

        $dataProvider->setSort(false);

        return $this->render('refund-lists', get_defined_vars());
    }

    /**
     * 查询退款订单
     * @param $id
     * @return false|string
     * @throws Exception
     * @author zhaowenxi
     */
    public function actionRefundOrderCheck($id){

        $reOrder = PmOrderRefund::findOne($id);

        if($reOrder){

            $minShengQuery = [
                'platformId' => \Yii::$app->params['minShengPay']['platform_id'],
                'merchantNo' => \Yii::$app->params['minShengPay']['merchant_id'],
                'merchantSeq' => $reOrder->pmOrder->number,
                'mchSeqNo' => $reOrder->refund_number,
            ];

            $msResult = (new MinSheng())->orderCheck($minShengQuery);

            if($msResult['code'] != 0){
                return $this->renderJsonFail("商户系统没有此订单");
            }

            if($reOrder->status != PmOrderRefund::STATUS_SUCCESS && $msResult['data']->tradeStatus == "S"){

                $pmOrder = PmOrder::findOne($reOrder->pm_order_id);

                $transaction = PmOrderRefund::getDb()->beginTransaction();

                try{

                    //退款操作
                    switch ($msResult['data']->tradeStatus){
                        case "S": $reOrder->status = PmOrderRefund::STATUS_SUCCESS;break;
                        case "E": $reOrder->status = PmOrderRefund::STATUS_FAIL;break;
                        case "R": $reOrder->status = PmOrderRefund::STATUS_WAIT;break;
                    }

                    $reOrder->updated_at = time();
                    $reOrder->result = serialize($msResult['data']);

                    if(!$reOrder->save()){
                        throw new Exception("更新退款信息有误");
                    }

                    if($pmOrder && $pmOrder->status != PmOrder::STATUS_REFUND){

                        //更新原订单信息
                        $pmOrder->status    = PmOrder::STATUS_REFUND;
                        $pmOrder->refund_at = time();

                        if(!$pmOrder->save()){
                            throw new Exception("原订单信息更新有误");
                        }

                        PmOrderItem::updateAll(['status' => '2000'], ['pm_order_id' => $reOrder->pm_order_id]);

                        $diC = $pmOrder->discount_status;

                        if($diC > 0){
                            $meProCode = MemberPromotionCode::findOne(['house_id' => $pmOrder->house_id, 'member_id' => $pmOrder->member_id, 'promotion_name' => 'auth']);

                            if($meProCode){
                                MemberPromotionCode::updateAll(['status' => 0], ['member_id' => $pmOrder->member_id, 'house_id' => $pmOrder->house_id]);
                            }

                            $pmChBillIte = PmChristmasBillItem::findOne(['house_id' => $pmOrder->house_id]);

                            if($pmChBillIte){
                                PmChristmasBillItem::deleteAll(['house_id' => $pmOrder->house_id]);
                            }
                        }
                    }

                    $transaction->commit();

                }catch (Exception $e){

                    $transaction->rollBack();

                    return $this->renderJsonFail($e->getMessage());
                }
            }

            return $this->renderJson(['code' => 0, 'data' => [
                'status' => $msResult['data']->tradeStatus,
                'remark' => $msResult['data']->remark,
                'amount' => bcdiv($msResult['data']->amount, 100, 2),
            ]]);

        }else{
            return $this->renderJsonFail("没有此订单");
        }
    }

    /**
     * 补核销
     * @param PmOrder $order
     * @return bool
     */
    private function nwPayBill($order){

        if(!$order instanceof PmOrder){
            return false;
        }

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

                if(!$item->save()){
                    return false;
                }
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
        $this->http_post('http://testbusiness.51homemoney.com/member-bill-notify/notify', $pushData);

        return true;
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