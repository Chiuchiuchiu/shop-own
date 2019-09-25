<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/23 11:49
 * Description:
 */

namespace apps\www\controllers;


use common\models\AuthHouseNotificationMember;
use common\models\House;
use common\models\MemberHouse;
use common\models\MemberHouseLog;
use common\models\MemberHouseReview;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\ProjectHouseStructure;
use common\models\SysSwitch;
use common\valueObject\PaymentCycle;
use components\newWindow\NewWindow;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use common\models\PmChristmasBillItem;
use common\models\MemberPromotionCode;
use common\models\PmOrderDiscounts;

class HouseController extends Controller
{
    public function actionIndex()
    {
        $houseRs = MemberHouse::find()
            ->where(['member_id' => $this->user->id, 'group' => MemberHouse::GROUP_HOUSE, 'status' => [MemberHouse::STATUS_ACTIVE, MemberHouse::STATUS_WAIT_REVIEW]])
            ->all();

        $parkingRs = MemberHouse::find()
            ->where(['member_id' => $this->user->id, 'group' => MemberHouse::GROUP_PARKING, 'status' => [MemberHouse::STATUS_ACTIVE, MemberHouse::STATUS_WAIT_REVIEW]])
            ->all();

        //begin 圣诞红包活动（2017-12-24 ~ 2018-02-21）
        if(SysSwitch::inVal('testChristmasMember', $this->user->id)){
            $memberAuthRedPack = AuthHouseNotificationMember::findOne(['member_id' => $this->user->id, 'status' => 0]);
        } else {
            $memberAuthRedPack = false;
        }
        //end

        return $this->render('index', [
            'houseRs'           => $houseRs,
            'parkingRs'         => $parkingRs,
            'memberAuthRedPack' => $memberAuthRedPack,
            'currentPage'       => 'house',
            'cdj_header_tip'    => $this->CDJ_TIP,
        ]);
    }

    public function actionList($group = 1)
    {
        $memberHouse = MemberHouse::findAll(['member_id' => $this->user->id, 'group' => $group, 'status' => [MemberHouse::STATUS_WAIT_REVIEW, MemberHouse::STATUS_ACTIVE]]);
        return $this->render('list', ['memberHouse' => $memberHouse]);
    }

    /**
     * @author duantaofeng
     * @description 物业缴费
     * @return string
     */
    public function actionChooseBill()
    {
        $_where = array(
            'member_id' => $this->user->id,
            'status' => MemberHouse::STATUS_ACTIVE
        );

        $houseRs = MemberHouse::find()->where($_where)->all();

        return $this->render('choose-bill', [
            'houseRs' => $houseRs,
            'cdj_header_tip'    => $this->CDJ_TIP,

        ]);
    }

    /**
     * 年缴、月缴
     * @param null $id
     * @param string $chooseT
     * @return false|string|\yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionShowNewBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        $list = [];
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $projectFeeCycleId = $model->house->project->project_fee_cycle_id;
            $houseProjectId = $model->house->project_house_id;//项目ID
            $houseParentId = $model->house->parent_id;//房产父级

            if(!in_array($projectFeeCycleId, [1,4])){
                 $_list = [];
                //针对广州南国奥园商铺月缴
                if(in_array($houseParentId, [87869,87882,87895,87927,87938])){
                    $_list = (new NewWindow())->getBill($model->house_id);
                } else if($houseProjectId == 84244){
                    $preHouseParent = House::findOne(['house_id' => $houseParentId]);
                    $preHouseParentId = $preHouseParent->parent_id;
                    if(in_array($preHouseParentId, [88889,229740])){
                        $_list = (new NewWindow())->getBill($model->house_id);
                    }
                }
            } else {
                $_list = (new NewWindow())->getBill($model->house_id);
            }

            if (sizeof($_list) > 0) {

                if(SysSwitch::inValue('projectInPay', $projectFeeCycleId)){

                    if($chooseT == 'bill'){
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);
                            $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($bill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $bill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                                $list[date('Y', $date)]['totalAmount'] += $bill;
                                $list[date('Y', $date)]['list'][] = $tempArray;
                            }

                        }
                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);

                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);
                            //当期欠费大于0
                            $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                            if($compValue > 0){
                                $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                                $list[date('Y', $date)]['totalAmount'] += $row['BillAmount'];
                                $list[date('Y', $date)]['list'][] = $tempArray;
                            }
                        }
                    }

                } else {

                    if($chooseT == 'bill'){

                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                            $date = strtotime($date);
                            $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($bill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $bill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                                $list[date('Y-m', $date)]['totalAmount'] += $bill;
                                $list[date('Y-m', $date)]['list'][] = $tempArray;
                            }

                        }

                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                            $date = strtotime($date);

                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            //当期欠费大于0
                            $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                            if($compValue > 0){
                                $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                                $list[date('Y-m', $date)]['totalAmount'] += $row['BillAmount'];
                                $list[date('Y-m', $date)]['list'][] = $tempArray;
                            }

                        }
                    }

                }
            }
            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'billType' => $chooseT,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 账单取于新视窗，有问题直接反馈给新视窗即可
     * @param null $id
     * @param null $autoPay
     * @return string
     */
    public function actionBill($id = null, $autoPay = null)
    {
        $billType = $this->get('chooseT', 'bill');

        //锦绣江南临时处理
        if($this->project->house_id == 59113 && $autoPay){
            return $this->redirect(str_replace('house/bill', 'house/bill-to-butler', \Yii::$app->request->url));
        }

        /**
         * @var MemberHouse $model
         */
        if ($autoPay) {
            $model = new MemberHouse(['member_id' => $this->user->id, 'status' => MemberHouse::STATUS_ACTIVE, 'house_id' => $id]);
        } else {
            $model = MemberHouse::find()
                ->where([
                    'member_id' => $this->user->id,
                    'status' => [MemberHouse::STATUS_ACTIVE],
                ])->andFilterWhere(['house_id' => $id])
                ->with('house')->one();
        }

        if(!$model){
            return $this->redirect('/auth');
        }

        $initBill = 0;
        $list = [];
        $tempArray = [];
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            if (sizeof($_list) > 0) {
                $projectFeeCycleId = $model->house->project->project_fee_cycle_id;

                if(SysSwitch::inValue('projectInPay', $projectFeeCycleId)){

                    if($billType == 'bill'){
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);
                            $initBill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($initBill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $initBill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                                $list[date('Y', $date)]['totalAmount'] += $initBill;
                                $list[date('Y', $date)]['list'][] = $tempArray;
                            }

                        }
                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);
                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                            $list[date('Y', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y', $date)]['list'][] = $tempArray;
                        }
                    }

                } else {

                    if($billType == 'bill'){
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            $initBill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($initBill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $initBill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                //管理费按期初，其他按期末
                                $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                                $date = strtotime($date);

                                $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                                $list[date('Y-m', $date)]['totalAmount'] += $initBill;
                                $list[date('Y-m', $date)]['list'][] = $tempArray;
                            }

                        }
                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                            $date = strtotime($date);
                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }
                    }

                }
            }
            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('bill', [
            'model' => $model,
            'list' => $list,
            'autoPay' => $autoPay,
            'billType' => $billType,
            'useNewPay' => $useNewPay,
        ]);
    }

    public function actionBillContent($houseId)
    {
        $list = [];
        $_list = (new NewWindow())->getBill($houseId);
        if ($_list) {
            foreach ($_list as $row) {
                $row['BillDate'] = explode('-', $row['BillDate']);
                $row['BillDate'] = array_map(function ($value) {
                    return date('Y-m-d', strtotime($value));
                }, $row['BillDate']);
                $isPayNow = strtotime($row['BillDate'][0]) - 30 * 86400 < time();
                $ordering = $row['BillDate'][0];
                $row['BillDate'] = implode('至', $row['BillDate']);
                $list[] = [
                    'BillDate' => $row['BillDate'],
                    'ContractNo' => $row['ContractNo'],
                    'ChargeItemName' => $row['ChargeItemName'],
                    'BillAmount' => $row['BillAmount'],
                    'isPayNow' => (int)$isPayNow,
                    'ordering' => $ordering
                ];
            }
            ArrayHelper::multisort($list, 'ordering');
        }
        return $this->renderJsonSuccess($list);
    }

    public function actionBillSubmit()
    {
//        return $this->renderJsonFail('服务升级造成不稳定，暂停缴费！');

        if ($this->isGet) {
            return $this->renderJsonFail("forbidden");
        }
        $houseId = $this->post('houseId');
        $contractNo = $this->post('contractNo');
        $billType = $this->post('billType');
        $memberBillType = 1;

        $contractNo = explode(',', $contractNo);
        $house = House::findOne($houseId);
        if (!$house) {
            return $this->renderJsonFail("提交信息有误");
        }

        if(SysSwitch::inVal('pauseWeChatPayment', $house->project_house_id)){
            return $this->renderJsonFail("该项目暂取消“微信缴费”业务！");
        }

        if(SysSwitch::inVal('testMember', $this->user->id)){

            $orderItems[] = [
                'ContractNo' => '45-33507028-1',
                'ChargeItemID' => '69',
                'ChargeItemName' => '梯灯公摊费',
                'BillAmount' => \Yii::$app->params['test_member_amount'],
                'BillDate' => '20170201-20170228',
            ];

            $order = new PmOrder();
            $order->house_id = $house->house_id;
            $order->project_house_id = $house->project_house_id;
            $order->member_id = $this->user->id;
            $order->total_amount = \Yii::$app->params['test_member_amount'];
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

        } else {
            $billList = (new NewWindow())->getBill($houseId);
            if (!is_array($billList)) {
                return $this->renderJsonFail("账单接口维护中,请稍后再试");
            }
            $_contractNo = ArrayHelper::getColumn($billList, 'ContractNo');
            $totalAmount = 0;
            $billToAmount = 0;
            $orderItems = [];
            $tempArray = [];

            foreach ($billList as $bill) {
                if($billType == 'bill'){
                    $memberBillType = 2;
                    foreach ($contractNo as $k => $no) {
                        if ($no == $bill['ContractNo']) {
                            $tempArray = $bill;
                            $billToAmount = bcsub($bill['BillAmount'], $bill['BillFines'], 2);
                            $totalAmount += $billToAmount;

                            $tempArray['BillAmount'] = $billToAmount;
                            $tempArray['BillTotalAmount'] = $bill['BillAmount'];

                            $orderItems[] = $tempArray;
                            unset($contractNo[$k]);
                            break;
                        }
                    }
                } else {
                    $memberBillType = 1;
                    foreach ($contractNo as $k => $no) {
                        if ($no == $bill['ContractNo']) {
                            $totalAmount += round($bill['BillAmount'], 2);
                            $orderItems[] = $bill;
                            unset($contractNo[$k]);
                            break;
                        }
                    }
                }
            }
            $order = new PmOrder();
            $order->house_id = $house->house_id;
            $order->project_house_id = $house->project_house_id;
            $order->member_id = $this->user->id;
            $order->total_amount = $totalAmount;
            $order->bill_type = $memberBillType;

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
                    $orderItem->charge_detail_id_list = $val['ChargeDetailIDList'];
                    $orderItem->charge_item_id = $val['ChargeItemID'];
                    $orderItem->charge_item_name = $val['ChargeItemName'];
                    $orderItem->amount = round($val['BillAmount'], 2);
                    $orderItem->bill_date = $val['BillDate'];
                    $orderItem->price = isset($val['Price']) ? $val['Price'] : 0;
                    $orderItem->usage_amount = isset($val['Amount']) ? $val['Amount'] : 0;
                    $orderItem->customer_name = isset($val['CustomerName']) ? $val['CustomerName'] : '-';
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

    }

    public function actionMember()
    {
        //查找业主
        $list = MemberHouse::find()->where([
            'identity' => MemberHouse::IDENTITY_OWNER,
            'member_id' => $this->user->id,
            'status' => MemberHouse::STATUS_ACTIVE
        ])->all();
        return $this->render('member', [
            'list' => $list
        ]);
    }

    public function actionMemberRemove()
    {
	    return $this->renderJsonFail('活动期间暂停该业务！');

        $houseId = $this->post('houseId');
        $memberIds = $this->post('memberIds');
        $memberIds = explode(',',$memberIds);
        foreach ($memberIds as $memberId) {
            $res = MemberHouse::findOne(['house_id' => $houseId, 'member_id' => $memberId]);
            if ($res) {
                $res->delete();
                $log = new MemberHouseLog();
                $log->member_id = $res->member_id;
                $log->house_id = $res->house_id;
                $log->operator = MemberHouseLog::OPERATOR_MEMBER;
                $log->operator_id = $this->user->id;
                $log->data = serialize($res);
                $log->action = MemberHouseLog::ACTION_DELETE;
                $log->save();
            }
        }
        return $this->renderJsonSuccess([]);
    }

    public function actionManager()
    {
        $list = MemberHouse::find()->where([
            'member_id' => $this->user->id,
            'status' => MemberHouse::STATUS_ACTIVE
        ])->all();
        return $this->render('manager', [
            'list' => $list
        ]);
    }

    public function actionDelHouse($id)
    {
        return $this->renderJsonFail('活动期间关闭删除房产业务！');

	    $res = MemberHouse::findOne(['member_id'=>$this->user->id, 'house_id'=>$id]);

        $memberHouseReview = MemberHouseReview::findOne([
            'member_id'=>$this->user->id,
            'house_id'=>$id
        ]);

        if($res){
            $res->delete();
            if($memberHouseReview){
                $memberHouseReview->delete();
            }
        }
        return $this->renderJsonSuccess([]);
    }

    public function actionQuery($houseId, $group = 1)
    {
        /**
         * @var $house House
         */
        $house = House::findOne($houseId);
        if (!$house) {
            return $this->renderJsonFail("找不到对应的数据");
        } else {
            $res = array_filter($house->showChild, function ($row) use ($group) {
                $needs = $group == 1 ? [2, 3, 4, 5, 6, 7, 8] : [2, 9, 10, 11];
                return in_array($row->reskind, $needs);
            });
            ArrayHelper::multisort($res, ['ordering', 'house_id'], [SORT_DESC, SORT_ASC]);
            $list = [];
            foreach ($res as $key => $row) {
                $list[] = [
                    'house_id' => $row['house_id'],
                    'house_name' => $row['house_alias_name'] ? $row['house_alias_name'] : $row['house_name'],
                ];

            }
            $structure = ProjectHouseStructure::find()
                ->where(['project_house_id' => $house->project_house_id, 'group' => $group, 'type' => 1])
                ->orderBy('ordering DESC')->select('name')
                ->asArray()->all();
            $structure = ArrayHelper::getColumn($structure, 'name');
            return $this->renderJsonSuccess([
                'list' => $list,
                'structure' => $structure
            ]);
        }
    }

    /**
     * 不知道为什么要加：要修复
     * @return string|\yii\web\Response
     */
    public function actionUndefined()
    {
        return $this->redirect('/');
    }

    /**
     * 半年一缴：自然月-> 1~6，7~12
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     */
    public function actionSemiannualBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        $list = [];
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            if (sizeof($_list) > 0) {

                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $idDate = date('m', $date);
                            if($idDate <= '06'){
                                $bilDate = date('Y', $date) . '-01';
                            } else {
                                $bilDate = date('Y', $date) . '-07';
                            }

                            $list[$bilDate]['totalAmount'] = $list[$bilDate]['totalAmount']??0;
                            $list[$bilDate]['totalAmount'] += $bill;
                            $list[$bilDate]['list'][] = $tempArray;
                        }

                    }
                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $idDate = date('m', $date);
                            if($idDate <= '06'){
                                $bilDate = date('Y', $date) . '-01';
                            } else {
                                $bilDate = date('Y', $date) . '-07';
                            }

                            $list[$bilDate]['totalAmount'] = $list[$bilDate]['totalAmount']??0;
                            $list[$bilDate]['totalAmount'] += $row['BillAmount'];
                            $list[$bilDate]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('semiannual-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'billType' => $chooseT,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 季度缴费：自然月
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     */
    public function actionQuarterlyBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        $list = [];
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $month = date('n', $date);
                            $billKey = '';
                            switch($month){
                                case 1:
                                case 2:
                                case 3:
                                    $billKey = date('Y', $date) . '-01';
                                    break;
                                case 4:
                                case 5:
                                case 6:
                                    $billKey = date('Y', $date) . '-04';
                                    break;
                                case 7:
                                case 8:
                                case 9:
                                    $billKey = date('Y', $date) . '-07';
                                    break;
                                case 10:
                                case 11:
                                case 12:
                                    $billKey = date('Y', $date) . '-10';
                                    break;
                            }

                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $list[$billKey]['totalAmount'] = $list[$billKey]['totalAmount'] ?? 0;
                            $list[$billKey]['totalAmount'] += $bill;
                            $list[$billKey]['list'][] = $tempArray;
                        }

                    }
                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $month = date('n', $date);
                            $billKey = '';
                            switch($month){
                                case 1:
                                case 2:
                                case 3:
                                    $billKey = date('Y', $date) . '-01';
                                    break;
                                case 4:
                                case 5:
                                case 6:
                                    $billKey = date('Y', $date) . '-04';
                                    break;
                                case 7:
                                case 8:
                                case 9:
                                    $billKey = date('Y', $date) . '-07';
                                    break;
                                case 10:
                                case 11:
                                case 12:
                                    $billKey = date('Y', $date) . '-10';
                                    break;
                            }

                            $list[$billKey]['totalAmount'] = $list[$billKey]['totalAmount'] ?? 0;
                            $list[$billKey]['totalAmount'] += $row['BillAmount'];
                            $list[$billKey]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('quarterly-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'billType' => $chooseT,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     *begin 活动
     * 一年一缴
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     * @throws
     */
    public function actionYearsToPay($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */
        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectHouseId = $model->house->project_house_id;
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            //杭州东方花城，代收水费、物业管理费
            if($projectHouseId == 296152){
                $billCategory = $this->get('Coll', null);
                $list = PaymentCycle::billCategoryYearBill($_list, $chooseT, $billCategory);
                unset($_list);
                $_list = [];
            }

            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                            $list[date('Y', $date)]['totalAmount'] += $bill;
                            $list[date('Y', $date)]['list'][] = $tempArray;
                        }

                    }

                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                            $list[date('Y', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y', $date)]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }
        return $this->render('years-to-pay', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 半年一缴：自然月
     * 有效使用期：2017-12-24~待定
     *
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     * @throws
     */
    public function actionChristmasSemiannualBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $NWerror = false;
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            is_array($_list) || $NWerror = true;

            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $idDate = date('m', $date);
                            if($idDate <= '06'){
                                $bilDate = date('Y', $date) . '-01';
                            } else {
                                $bilDate = date('Y', $date) . '-07';
                            }

                            $list[$bilDate]['totalAmount'] = $list[$bilDate]['totalAmount']??0;
                            $list[$bilDate]['totalAmount'] += $bill;
                            $list[$bilDate]['list'][] = $tempArray;
                        }

                    }
                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $idDate = date('m', $date);
                            if($idDate <= '06'){
                                $bilDate = date('Y', $date) . '-01';
                            } else {
                                $bilDate = date('Y', $date) . '-07';
                            }

                            $list[$bilDate]['totalAmount'] = $list[$bilDate]['totalAmount']??0;
                            $list[$bilDate]['totalAmount'] += $row['BillAmount'];
                            $list[$bilDate]['list'][] = $tempArray;
                        }
                    }
                }

            }
            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('christmas-semiannual-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'NWerror'=> $NWerror,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 有效使用期：2017-12-24~待定
     * 月缴
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     * @throws
     */
    public function actionChristmasBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }


        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $NWerror = false;
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            is_array($_list) || $NWerror = true;

            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $bill;
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }

                    }

                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('christmas-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'NWerror'=> $NWerror,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 季度缴费：2017-12-24~待定
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     * @throws
     */
    public function actionChristmasQuarterlyBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */

        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }


        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');

        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $NWerror = false;
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            is_array($_list) || $NWerror = true;

            if (sizeof($_list) > 0) {

                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){

                            $month = date('n', $date);
                            $billKey = '';
                            switch($month){
                                case 1:
                                case 2:
                                case 3:
                                    $billKey = date('Y', $date) . '-01';
                                    break;
                                case 4:
                                case 5:
                                case 6:
                                    $billKey = date('Y', $date) . '-04';
                                    break;
                                case 7:
                                case 8:
                                case 9:
                                    $billKey = date('Y', $date) . '-07';
                                    break;
                                case 10:
                                case 11:
                                case 12:
                                    $billKey = date('Y', $date) . '-10';
                                    break;
                            }

                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $list[$billKey]['totalAmount'] = $list[$billKey]['totalAmount'] ?? 0;
                            $list[$billKey]['totalAmount'] += $bill;
                            $list[$billKey]['list'][] = $tempArray;
                        }

                    }
                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);

                        $month = date('n', $date);
                        $billKey = '';
                        switch($month){
                            case 1:
                            case 2:
                            case 3:
                                $billKey = date('Y', $date) . '-01';
                                break;
                            case 4:
                            case 5:
                            case 6:
                                $billKey = date('Y', $date) . '-04';
                                break;
                            case 7:
                            case 8:
                            case 9:
                                $billKey = date('Y', $date) . '-07';
                                break;
                            case 10:
                            case 11:
                            case 12:
                                $billKey = date('Y', $date) . '-10';
                                break;
                        }

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $list[$billKey]['totalAmount'] = $list[$billKey]['totalAmount'] ?? 0;
                            $list[$billKey]['totalAmount'] += $row['BillAmount'];
                            $list[$billKey]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('christmas-quarterly-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'NWerror'=> $NWerror,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * end
     * 临时使用，有效使用期：2017-12-24~2018-01-10
     */
    public function actionChristmasBillSubmit()
    {
//        return $this->renderJsonFail('系统服务升级，暂停缴费！');

        if ($this->isGet) {
            return $this->renderJsonFail("forbidden");
        }
        $authActivities = \Yii::$app->params['christmas_activities'];

        if(time() > $authActivities['allowedMaxTime']){
            return $this->renderJsonFail('活动已结束！');
        }

        $houseId = $this->post('houseId');
        $contractNo = $this->post('contractNo');
        $billType = $this->post('billType');
        $discountCoupon = $this->post('discountCoupon');
        $memberBillType = 1;
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        $contractNo = explode(',', $contractNo);
        $house = House::findOne($houseId);
        if (!$house) {
            return $this->renderJsonFail("提交信息有误");
        }

        $isMemberHouse = AuthHouseNotificationMember::findOne(['member_id' => $this->user->id, 'status' => AuthHouseNotificationMember::STATUS_DEFAULT, 'house_id' => $houseId]);
        if($isMemberHouse){
            $goUrl = '/activities/red-pack?houseId=' . $houseId;
            return $this->renderJsonFail('您有红包未领取！请返回首页', -1, ['goUrl' => $goUrl]);
        }

        if(SysSwitch::inVal('pauseWeChatPayment', $house->project_house_id)){
            return $this->renderJsonFail("该项目暂取消“微信缴费”业务！");
        }

        $billList = (new NewWindow())->getBill($houseId);

        if (!is_array($billList)) {
            return $this->renderJsonFail("账单接口维护中,请稍后再试");
        }
        $_contractNo = ArrayHelper::getColumn($billList, 'ContractNo');
        $totalAmount = 0;
        $billToAmount = 0;
        $orderItems = [];
        $tempArray = [];
        $discountChargeAmount = 0;

        foreach ($billList as $bill) {
            if($billType == 'bill'){
                $memberBillType = 2;
                foreach ($contractNo as $k => $no) {
                    if ($no == $bill['ContractNo']) {
                        $tempArray = $bill;
                        $billToAmount = bcsub($bill['BillAmount'], $bill['BillFines'], 2);
                        $totalAmount += $billToAmount;

                        $tempArray['BillAmount'] = $billToAmount;
                        $tempArray['BillTotalAmount'] = $bill['BillAmount'];

                        $orderItems[] = $tempArray;

                        //2017-12-24 ~ 2018-02-21 期间缴费优惠
                        if(in_array($bill['ChargeItemID'], $discountsChargeItemIds)){
                            $discountChargeAmount += $billToAmount;
                        }

                        unset($contractNo[$k]);
                        break;
                    }
                }
            } else {
                $memberBillType = 1;
                foreach ($contractNo as $k => $no) {
                    if ($no == $bill['ContractNo']) {
                        $totalAmount += round($bill['BillAmount'], 2);
                        $orderItems[] = $bill;

                        //2017-12-24 ~ 2018-02-21 期间缴费优惠
                        if(in_array($bill['ChargeItemID'], $discountsChargeItemIds)){
                            $billToAmount = round($bill['BillAmount'], 2);
                            $discountChargeAmount += $billToAmount;
                        }

                        unset($contractNo[$k]);
                        break;
                    }
                }
            }
        }

        $discountStatus = 0;
        $getDiscount = $this->buildBillDiscounts($totalAmount, $discountChargeAmount, $houseId);
        if(is_array($getDiscount) && $getDiscount['amount'] > 0){
            $totalAmount = $getDiscount['amount'];
            if(!empty($getDiscount['discount'])){
                $discountStatus = 1;
            }
        }

        $order = new PmOrder();
        $order->house_id = $house->house_id;
        $order->project_house_id = $house->project_house_id;
        $order->member_id = $this->user->id;
        $order->total_amount = $totalAmount;
        $order->bill_type = $memberBillType;
        $order->discount_status = $discountStatus;

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
                $orderItem->charge_detail_id_list = $val['ChargeDetailIDList'];
                $orderItem->charge_item_id = $val['ChargeItemID'];
                $orderItem->charge_item_name = $val['ChargeItemName'];
                $orderItem->amount = round($val['BillAmount'], 2);
                $orderItem->bill_date = $val['BillDate'];
                $orderItem->price = isset($val['Price']) ? $val['Price'] : 0;
                $orderItem->usage_amount = isset($val['Amount']) ? $val['Amount'] : 0;
                $orderItem->customer_name = isset($val['CustomerName']) ? $val['CustomerName'] : '-';
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
            if(is_array($getDiscount) && $getDiscount['amount'] > 0){
                PmOrderDiscounts::findOrCreate($order->id, $getDiscount['discount'], $getDiscount['useRedPack']);
            }

            return $this->renderJsonSuccess(['id' => $order->id]);
        } else {
            return $this->renderJsonFail('error', -1, $res);
        }

    }

    /**
     * 用于调试新的支付通道 2018-03-03
     * 季度缴/月缴
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     */
    public function actionTestBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */
        if(!in_array($this->user->id, [392, 117155])){
            echo '测试通道';die;
        }
        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->with('house')->one();

        if(!$model){
            return $this->redirect('/auth');
        }


        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $NWerror = false;
        $tempArray = [];
        $bill = 0;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            is_array($_list) || $NWerror = true;

            if (sizeof($_list) > 0) {
                $projectFeeCycleId = $model->house->project->project_fee_cycle_id;

                if(SysSwitch::inValue('projectInPay', $projectFeeCycleId)){

                    if($chooseT == 'bill'){
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);
                            $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($bill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $bill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                                $list[date('Y', $date)]['totalAmount'] += $bill;
                                $list[date('Y', $date)]['list'][] = $tempArray;
                            }

                        }
                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);

                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                            $list[date('Y', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y', $date)]['list'][] = $tempArray;
                        }
                    }

                } else {

                    if($chooseT == 'bill'){

                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                            $date = strtotime($date);
                            $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($bill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $bill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                                $list[date('Y-m', $date)]['totalAmount'] += $bill;
                                $list[date('Y-m', $date)]['list'][] = $tempArray;
                            }

                        }

                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                            $date = strtotime($date);

                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }
                    }

                }
            }
            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('test-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'NWerror'=> $NWerror,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 针对凤池岛、三江尊园、东海银湾项目做特殊处理 2018-09-03
     * 月缴，除物业管理之外其他缴费类目可选
     * @author HQM
     * @param string $billCategory
     * @param null $id
     * @param string $chooseT
     * @return false|string|\yii\web\Response
     * @throws \yii\base\ErrorException
     */
    public function actionSpecialBill($billCategory='', $id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */
        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $NWerror = false;
        $tempArray = [];
        $bill = 0;
        $projectHouseId = $model->house->project_house_id;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if($projectHouseId == 230975){
            $chargeItemID = [3, 21, 168];
        } else {
            $chargeItemID = [34, 33];
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {

            $_list = (new NewWindow())->getBill($model->house_id);

            !is_array($_list) && $NWerror = true;

            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        if($billCategory == 'h'){
                            if(in_array($row['ChargeItemID'], $chargeItemID)){
                                continue;
                            }
                        } else if($billCategory == 'notH') {
                            if(!in_array($row['ChargeItemID'], $chargeItemID)){
                                continue;
                            }
                        }

                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $bill;
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }

                    }

                } else {
                    foreach ($_list as $row) {
                        if($billCategory == 'h'){
                            if(in_array($row['ChargeItemID'], $chargeItemID)){
                                continue;
                            }
                        } else if($billCategory == 'notH') {
                            if(!in_array($row['ChargeItemID'], $chargeItemID)){
                                continue;
                            }
                        }

                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }
                    }
                }
            }
            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('special-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'NWerror'=> $NWerror,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 针对亲亲家园二期项目无序选择账单
     * 月缴
     * @author HQM 2018/12/19
     * @param null $id
     * @param string $chooseT
     * @return string|\yii\web\Response
     * @throws
     */
    public function actionDisorderlyBill($id=null, $chooseT='bill')
    {
        /**
         * @var MemberHouse $model
         */
        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->andFilterWhere(['house_id' => $id])
            ->one();

        if(!$model){
            return $this->redirect('/auth');
        }

        $projectHouseId = $model->house->project_house_id;
        if($projectHouseId != 392072){
            echo "<h1>仅供【亲亲家园项目二期】使用</h1>";
            die();
        }

        //begin 跟活动相关的，用户数据查询（）
        $memberPromotion = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $id])->sum('amount');
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $id]);
        //end

        $list = [];
        $NWerror = false;
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if(!in_array($chooseT, ['bill', 'all'])){
            $chooseT = 'bill';
        }

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);
            is_array($_list) || $NWerror = true;
            $list = PaymentCycle::monthBill($_list, $chooseT);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('disorderly-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'NWerror'=> $NWerror,
            'billType' => $chooseT,
            'memberPromotion' => $memberPromotion,
            'pmChristmasBillItem' => $pmChristmasBillItem,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 查询缴费周期
     * @return false|string
     * @throws \yii\base\ErrorException
     */
    public function actionQueryBillType()
    {
        if($this->isAjax){
            $authActivities = \Yii::$app->params['christmas_activities'];

            $houseId = $this->post('id', null);
            $billCategory = $this->post('billCategory');    //bill item
            if(empty($houseId)){
                return $this->renderJsonFail('无法查询');
            }

            $memberHouse = MemberHouse::findOne(['member_id' => $this->user->id, 'house_id' => $houseId]);
            if(!$memberHouse){
                return $this->renderJsonFail('该房产未与您的账号进行绑定！');
            }

            $houseProjectId = $memberHouse->house->project_house_id;//项目ID
            $houseParentId = $memberHouse->house->parent_id;//房产父级

            if(SysSwitch::inVal('pauseWeChatPayment', $houseProjectId) || $memberHouse->house->project->status == 0){
                return $this->renderJsonFail("该项目暂取消“微信缴费”业务！");
            }

            //检查缴车位管理费之前是否已经缴物业管理费，东方花城除外
            if($houseProjectId != 296152){
                $allowBillPa = $this->checkHousePayBill($houseId, $memberHouse->group, $this->user->id);
                if($allowBillPa){
                    return $this->renderJsonFail("请先缴纳物业管理费用！");
                }
            }

            //针对杭州亲亲家园项目二期账单无序缴费
            if($houseProjectId == 392072){
                return $this->renderJsonSuccess(['goUrl' => '/house/disorderly-bill?']);
            }

            //凤池岛和三江尊园、东海银湾项目特殊处理
            if(in_array($houseProjectId, [468497, 117847, 230975])){
                return $this->renderJsonSuccess(['goUrl' => '/house/special-bill?billCategory='.$billCategory.'&']);
            }

            /**
             * 杭州东方花城，代收水费、物业管理费
             * @auth HQM 2018/12/20
             */
            if($houseProjectId == 296152){
                $Coll = $this->post('Coll');
                return $this->renderJsonSuccess(['goUrl' => '/house/years-to-pay?Coll='.$Coll.'&']);
            }

            //新的支付通道 2018-03-03
            if(SysSwitch::inVal('testPayMember', $this->user->id)){
                return $this->renderJsonSuccess(['goUrl' => '/house/test-bill?']);
            }

            //begin 活动期间有效
            if(time() <= $authActivities['allowedMaxTime']){
                //排除海南分公司项目
                if(in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
                    $pmChristmasBillItem = true;
                } else {
                    $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $houseId]);
                }
                if(!$pmChristmasBillItem){
                    //根据项目缴费周期
                    $feeCycle = $memberHouse->house->project->project_fee_cycle_id; //缴费周期

                    $url = "/house/christmas-bill?";

                    switch ($feeCycle){
                        //月缴
                        case 1:
                            $url = "/house/christmas-bill?";
                            break;
                        //季度缴
                        case 2:
                            $url = "/house/christmas-quarterly-bill?";

                            //针对广州南国奥园商铺月缴
                            if(in_array($houseParentId, [87869,87882,87895,87927,87938])){
                                $url = "/house/christmas-bill?";
                            } else if($houseProjectId == 84244){
                                $preHouseParent = House::findOne(['house_id' => $houseParentId]);
                                $preHouseParentId = $preHouseParent->parent_id;
                                if(in_array($preHouseParentId, [88889,229740])){
                                    $url = "/house/christmas-bill?";
                                }
                            }
                            break;
                        //半年缴
                        case 3:
                            $url = "/house/christmas-semiannual-bill?";
                            break;
                        //年缴
                        case 4:
                            $url = "/house/years-to-pay?";
                            break;
                    }

                    return $this->renderJsonSuccess(['goUrl' => $url]);
                }
            }
            //end

            //根据项目缴费周期
            $feeCycle = $memberHouse->house->project->project_fee_cycle_id;
            switch($feeCycle){
                case 2:
                    $goUrl = '/house/quarterly-bill?';
                    //针对广州南国奥园商铺月缴
                    if(in_array($houseParentId, [87869,87882,87895,87927,87938])){
                        $goUrl = "/house/show-new-bill?";
                    } else if($houseProjectId == 84244){
                        $preHouseParent = House::findOne(['house_id' => $houseParentId]);
                        $preHouseParentId = $preHouseParent->parent_id;
                        if(in_array($preHouseParentId, [88889,229740])){
                            $goUrl = "/house/show-new-bill?";
                        }
                    }
                    break;
                case 3:
                    $goUrl = '/house/semiannual-bill?';
                    break;
                default:
                    $goUrl = '/house/show-new-bill?';
                    break;
            }

            return $this->renderJsonSuccess(['goUrl' => $goUrl]);
        }

        return $this->renderJsonFail('请求出错');
    }

    /**
     * 从管家端生成二维码扫码支付
     * @param null $id
     * @param null $autoPay
     * @return false|string|\yii\web\Response
     * @throws \yii\base\ErrorException
     * @author zhaowenxi
     */
    public function actionBillToButler($id = null, $autoPay = null)
    {
        $billType = $this->get('chooseT', 'bill');
        if(!in_array($billType, ['bill', 'all'])){

            //默认改为去除滞纳金 20190815 zhaowenxi
            $billType = 'bill';
        }
        /**
         * @var MemberHouse $model
         */
        if ($autoPay) {
            $model = new MemberHouse(['member_id' => $this->user->id, 'status' => MemberHouse::STATUS_ACTIVE, 'house_id' => $id]);
        } else {
            $model = MemberHouse::find()
                ->where([
                    'member_id' => $this->user->id,
                    'status' => [MemberHouse::STATUS_ACTIVE],
                ])->andFilterWhere(['house_id' => $id])
                ->with('house')->one();
        }

        if(!$model){
            return $this->redirect('/auth');
        }

        $initBill = 0;
        $list = [];
        $tempArray = [];
        $useNewPay = 'w';
        $projectPayType = isset($this->project->pay_type) ? $this->project->pay_type : null;

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($model->house_id);

            if (sizeof($_list) > 0) {
                $projectFeeCycleId = $model->house->project->project_fee_cycle_id;

                if(SysSwitch::inValue('projectInPay', $projectFeeCycleId)){

                    if($billType == 'bill'){
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);
                            $initBill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($initBill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $initBill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                                $list[date('Y', $date)]['totalAmount'] += $initBill;
                                $list[date('Y', $date)]['list'][] = $tempArray;
                            }

                        }
                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = $billDate[0];
                            $date = strtotime($date);
                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            $list[date('Y', $date)]['totalAmount'] = $list[date('Y', $date)]['totalAmount']??0;
                            $list[date('Y', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y', $date)]['list'][] = $tempArray;
                        }
                    }

                } else {

                    if($billType == 'bill'){
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            $initBill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            if($initBill > 0){
                                $tempArray = $row;
                                $tempArray['BillAmount'] = $initBill;
                                $tempArray['BillTotalAmount'] = $row['BillAmount'];

                                //管理费按期初，其他按期末
                                $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                                $date = strtotime($date);

                                $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                                $list[date('Y-m', $date)]['totalAmount'] += $initBill;
                                $list[date('Y-m', $date)]['list'][] = $tempArray;
                            }

                        }
                    } else {
                        foreach ($_list as $row) {
                            $billDate = explode('-', $row['BillDate']);
                            //管理费按期初，其他按期末
                            $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                            $date = strtotime($date);
                            $tempArray = $row;
                            $tempArray['showBillFines'] = 'true';
                            $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                            $list[date('Y-m', $date)]['totalAmount'] = $list[date('Y-m', $date)]['totalAmount']??0;
                            $list[date('Y-m', $date)]['totalAmount'] += $row['BillAmount'];
                            $list[date('Y-m', $date)]['list'][] = $tempArray;
                        }
                    }

                }
            }
            ksort($list);
        }

        //检查订阅号主体是否是财到家，如果是则使用招商支付通道
        if(in_array($projectPayType, \Yii::$app->params['projectSwiftPassPay'])){
            $useNewPay = 's';
        }elseif($projectPayType == 3){  //民生
            $useNewPay = 'm';
        }

        //查找账单,按账单月份合并
        return $this->render('bill-to-butler', [
            'model' => $model,
            'list' => $list,
            'autoPay' => $autoPay,
            'billType' => $billType,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * Date: 2018-02-23
     * 缴车位管理费，检测与房产同一账单是否已经缴物业管理费
     * @param $houseId
     * @param $houseGroup
     * @param $memberId
     * @return bool
     * @throws \yii\base\ErrorException
     */
    private function checkHousePayBill($houseId, $houseGroup, $memberId)
    {
        if($houseGroup == 2){
            $_list = (new NewWindow())->getBill($houseId);

            $carBillArray = [];
            $houseBillArray = [];
            $hBillDate = 0;
            $pBillDate = 0;

            if(count($_list) > 0){

                foreach($_list as $pRow){
                    $ShouldChargeDate = substr($pRow['ShouldChargeDate'], 0, 6);
                    $pAmount = bcsub($pRow['BillAmount'], $pRow['BillFines'], 2);
                    if($pAmount > 0){
                        $carBillArray[] = $ShouldChargeDate;
                    }
                }

                sort($carBillArray);
                $pBillDate = isset($carBillArray[0]) ? $carBillArray[0] : 0;

                $memberAllHouse = MemberHouse::find()
                    ->select('house_id')
                    ->where(['member_id' => $memberId, 'group' => MemberHouse::GROUP_HOUSE, 'status' => MemberHouse::STATUS_ACTIVE])
                    ->asArray()
                    ->all();
                if($memberAllHouse){
                    foreach($memberAllHouse as $hKey => $hV){
                        $billList = (new NewWindow())->getBill($hV['house_id']);

                        if(count($billList) > 0){
                            foreach($billList as $row){
                                $ShouldChargeDate = substr($row['ShouldChargeDate'], 0, 6);
                                $hBill = bcsub($row['BillAmount'], $row['BillFines'], 2);
                                if($hBill > 0){
                                    $houseBillArray[] = $ShouldChargeDate;
                                }
                            }

                            sort($houseBillArray);
                            if(isset($houseBillArray[0])){
                                $hBillDate = $houseBillArray[0];
                                break;
                            }
                        }
                    }

                    if(empty($hBillDate) && $pBillDate > 0){
                        return false;
                    }

                    if(empty($hBillDate) && empty($pBillDate)){
                        return false;
                    }

                    if($pBillDate >= $hBillDate){
                        return true;
                    }

                    return false;
                }

                return false;
            }

            return false;
        }

        return false;
    }

    /**
     * 跨年缴物业费优惠
     * @param $totalAmount
     * @param  $discountChargeAmount
     * @param $houseId
     * @return mixed
     */
    private function buildBillDiscounts($totalAmount, $discountChargeAmount, $houseId)
    {
        $pmChristmasBillItem = PmChristmasBillItem::findOne(['house_id' => $houseId]);
        if($pmChristmasBillItem){
            return false;
        }

        if(empty($discountChargeAmount)){
            return false;
        }

        $discountAmount = 0;
        $receivedBill = 0;
        $data = [];
        $memberRedPackAmount = 0;

        $memberUseCoupon = MemberPromotionCode::find()->where(['member_id' => $this->user->id, 'status' => MemberPromotionCode::STATUS_DEFAULT, 'house_id' => $houseId])->sum('amount');
        if($memberUseCoupon){
            $memberRedPackAmount = $memberUseCoupon;
        }

        /*if($discountChargeAmount > 6000){
            $discountAmount = 200;
        } else if($discountChargeAmount >= 5000 && $discountChargeAmount < 6000){
            $discountAmount = 150;
        } else if($discountChargeAmount >= 4000 && $discountChargeAmount < 5000){
            $discountAmount = 100;
        } else if($discountChargeAmount >= 3000 && $discountChargeAmount < 4000){
            $discountAmount = 80;
        } else if($discountChargeAmount >= 2000 && $discountChargeAmount < 3000){
            $discountAmount = 50;
        } else if($discountChargeAmount >= 1000 && $discountChargeAmount < 2000){
            $discountAmount = 20;
        } else if($discountChargeAmount > 0) {
            $discountAmount = 5;
        }*/

        $discountAmount = $discountAmount + $memberRedPackAmount;

        $receivedBill = bcsub($totalAmount, $discountAmount, 2);

        $data['useRedPack'] = $memberRedPackAmount > 0 ? 1 : false;
        $data['amount'] = $receivedBill;
        $data['discount'] = $discountAmount;

        return $data;
    }

}
