<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/10/30
 * Time: 14:46
 */

namespace apps\api\controllers;


use apps\api\models\House;
use common\models\MemberHouse;
use common\models\MemberPromotionCode;
use common\models\PmChristmasBillItem;
use common\models\SysSwitch;
use common\valueObject\PaymentCycle;
use components\newWindow\NewWindow;

class HouseController extends Controller
{
    public $modelClass = 'apps\api\models\House';

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['save']);

        return $actions;
    }

    /**
     * 获取欠费列表，区分月缴、年缴、季度、半年
     * @param $houseId
     * @param string $type
     * @return string
     * @throws \yii\base\ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBill($houseId, $type = "all")
    {
        $authActivities = \Yii::$app->params['christmas_activities'];

        $memberHouse = MemberHouse::findOne(['house_id' => $houseId]);
        if(!$memberHouse){
            return $this->renderJsonFail(50002, $houseId);
        }

        //停用微信支付的项目
        $houseProjectId = $memberHouse->house->project_house_id;
        if (SysSwitch::inVal('pauseWeChatPayment', $houseProjectId)) {
            return $this->renderJsonFail(50003);
        }

        //(2017-12-28)检查缴车位管理费之前是否已经缴物业管理费
        if ($memberHouse->group == 2) {
            $allowBillPa = $this->checkHousePayBill($houseId, $memberHouse->group, $memberHouse->member_id);
            if ($allowBillPa) {
                return $this->renderJsonFail(50004);
            }
        }

        $response = [
            'houseInfo' => [
                'houseName' => $memberHouse->house->showName,
                'ancestorName' => $memberHouse->house->ancestor_name,
                'houseId' => $houseId,
                'billType' => $type,
            ],
            'billList' => [],
            'discount' => 0,
            'allowRelief' => 'n'
        ];
        $billList = [];
        $getBill = true;

        if (time() <= $authActivities['allowedMaxTime']) {
            //排除海南分公司
            if(!in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
                $redPa = MemberPromotionCode::findOne([
                    'member_id' => $this->userId,
                    'promotion_name' => 'auth',
                    'status' => MemberPromotionCode::STATUS_DEFAULT,
                    'house_id' => $houseId
                ]);
                if ($redPa) {
                    $response['discount'] = $redPa->amount;
                }
            }
        }

        //2018-12-01 启用
        if(time() <= $authActivities['endTime']){
            //排除海南分公司
            if(!in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
                $pmChris = PmChristmasBillItem::findOne(['house_id' => $houseId]);
                if(!$pmChris){
                    $response['allowRelief'] = 'y';
                }
            }
        }

        //凤池岛和三江尊园、东海银湾项目特殊处理
        if (in_array($houseProjectId, [468497, 117847, 230975])) {
            if($houseProjectId == 230975){
                $chargeItemID = [3, 21, 168];
            } else {
                $chargeItemID = [34, 33];
            }

            $billCategory = $this->get('billCategory', '');
            $billList = $this->billCategoryMonth($houseId, $type, $billCategory, $chargeItemID);
            $getBill = false;
        }

        /**
         * 杭州东方花城，代收水费、物业管理费
         * @author HQM 2018/12/26
         */
        if($houseProjectId == 296152){
            $billCategory = $this->get('billCategory', '');
            $billList = $this->billCategoryYearBill($memberHouse->house_id, $type, $billCategory);
            $getBill = false;
        }

        //根据项目缴费周期
        $feeCycle = $memberHouse->house->project->project_fee_cycle_id; //缴费周期
        $houseParentId = $memberHouse->house->parent_id;//房产父级

        //针对广州南国奥园商铺月缴
        if(in_array($houseParentId, [87869,87882,87895,87927,87938])){
            $feeCycle = 1;
        } else if($houseProjectId == 84244){
            $preHouseParent = House::findOne(['house_id' => $houseParentId]);
            $preHouseParentId = $preHouseParent->parent_id;
            if(in_array($preHouseParentId, [88889,229740])){
                $feeCycle = 1;
            }
        }

        if(count($billList) < 1 && $getBill){
            switch ($feeCycle) {
                //月缴
                case 1:
                    $billList = $this->monthBill($memberHouse, $type);
                    break;
                //季度缴
                case 2:
                    $billList = $this->quarterBill($memberHouse, $type);
                    break;
                //半年缴
                case 3:
                    $billList = $this->halfYearBill($memberHouse, $type);
                    break;
                //年缴
                case 4:
                    $billList = $this->YearBill($memberHouse, $type);
                    break;
            }
        }

        if(count($billList) > 0){
            foreach($billList as $key => $value){
                $response['billList'][]['info'] = $value;
            }
        }

        $this->writeFilelog($billList);

        return $this->renderJsonSuccess(200, $response);
    }

    /**
     * 缴车位管理费，检测与房产同一账单是否已经缴物业管理费
     * @param $houseId
     * @param $houseGroup
     * @param $memberId
     * @return bool
     * @throws \yii\base\ErrorException
     * @author zhaowenxi
     */
    private function checkHousePayBill($houseId, $houseGroup, $memberId)
    {
        if ($houseGroup == 2) {
            $_list = (new NewWindow())->getBill($houseId);

            $houseBillArray = [];
            $carBillArray = [];
            $hBillDate = 0;
            $pBillDate = 0;

            if (count($_list) > 0) {

                foreach ($_list as $pRow) {
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
                if ($memberAllHouse) {

                    foreach ($memberAllHouse as $hKey => $hV) {
                        $billList = (new NewWindow())->getBill($hV['house_id']);

                        if (count($billList) > 0) {
                            foreach ($billList as $row) {
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

                    if (empty($hBillDate) && $pBillDate > 0) {
                        return false;
                    }

                    if (empty($hBillDate) && empty($pBillDate)) {
                        return false;
                    }

                    if ($pBillDate >= $hBillDate) {
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
     * 月缴
     * @param MemberHouse $memberHouse
     * @param string $chooseT
     * @return array
     * @throws \yii\base\ErrorException
     */
    protected function monthBill(&$memberHouse, $chooseT = 'bill')
    {
        $res = [];
        $list = [];
        //物业服务费
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        if ($memberHouse && $memberHouse->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($memberHouse->house_id);
            if (sizeof($_list) > 0) {
                if ($chooseT == 'bill') {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);
                        $dateFormat = date('Ym', $date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if ($bill > 0) {
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];
                            
                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = date('Y-m', $date);
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] += $bill;
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount'] ?? 0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $bill, 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }

                    }

                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);
                        $dateFormat = date('Ym', $date);
                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if ($compValue > 0) {
                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = date('Y-m', $date);
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }

                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] += $row['BillAmount'];
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount'] ?? 0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $row['BillAmount'], 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        return $list;
    }

    /**
     * 季度缴
     * @param MemberHouse $memberHouse
     * @param string $chooseT
     * @return array
     * @throws
     */
    protected function quarterBill(&$memberHouse, $chooseT = 'bill')
    {
        $list = [];
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];
        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
        }

        if ($memberHouse && $memberHouse->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($memberHouse->house_id);

            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $dateFormat = date('Y', $date) . '01';
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $month = date('n', $date);
                            $billKey = '';
                            switch($month){
                                case 1:
                                case 2:
                                case 3:
                                    $dateFormat = date('Y', $date) . '01';
                                    $billKey = date('Y', $date) . '-01';
                                    break;
                                case 4:
                                case 5:
                                case 6:
                                    $dateFormat = date('Y', $date) . '04';
                                    $billKey = date('Y', $date) . '-04';
                                    break;
                                case 7:
                                case 8:
                                case 9:
                                    $dateFormat = date('Y', $date). '07';
                                    $billKey = date('Y', $date) . '-07';
                                    break;
                                case 10:
                                case 11:
                                case 12:
                                    $dateFormat = date('Y', $date). '10';
                                    $billKey = date('Y', $date) . '-10';
                                    break;
                            }

                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = $billKey;
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] = bcadd($list[$dateFormat]['chargeItemAmount'], $bill, 2);
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount'] ?? 0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $bill, 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }

                    }
                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $dateFormat = date('Y', $date) . '01';

                        $month = date('n', $date);
                        $billKey = '';
                        switch($month){
                            case 1:
                            case 2:
                            case 3:
                                $dateFormat = date('Y', $date) . '01';
                                $billKey = date('Y', $date) . '-01';
                                break;
                            case 4:
                            case 5:
                            case 6:
                                $dateFormat = date('Y', $date) . '04';
                                $billKey = date('Y', $date) . '-04';
                                break;
                            case 7:
                            case 8:
                            case 9:
                                $dateFormat = date('Y', $date). '07';
                                $billKey = date('Y', $date) . '-07';
                                break;
                            case 10:
                            case 11:
                            case 12:
                                $dateFormat = date('Y', $date). '10';
                                $billKey = date('Y', $date) . '-10';
                                break;
                        }

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = $billKey;
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }

                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] = bcadd($list[$dateFormat]['chargeItemAmount'], $row['BillAmount'], 2);
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount'] ?? 0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $row['BillAmount'], 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        return $list;
    }

    /**
     * 半年缴
     * @param MemberHouse $memberHouse
     * @param null $id
     * @param string $chooseT
     * @return array
     * @throws
     */
    protected function halfYearBill(&$memberHouse, $id = null, $chooseT = 'bill')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
        }

        if ($memberHouse && $memberHouse->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($memberHouse->house_id);

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
                                $dateFormat = date('Y', $date) . '01';
                                $bilDate = date('Y', $date) . '-01';
                            } else {
                                $dateFormat = date('Y', $date) . '07';
                                $bilDate = date('Y', $date) . '-07';
                            }

                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = $bilDate;
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] = bcadd($list[$dateFormat]['chargeItemAmount'], $bill, 2);
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount']??0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $bill, 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
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
                                $dateFormat = date('Y', $date) . '01';
                                $bilDate = date('Y', $date) . '-01';
                            } else {
                                $dateFormat = date('Y', $date) . '07';
                                $bilDate = date('Y', $date) . '-07';
                            }

                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = $bilDate;
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] = bcadd($list[$dateFormat]['chargeItemAmount'], $row['BillAmount'], 2);
                            }

                            $list[$bilDate]['totalAmount'] = $list[$bilDate]['totalAmount']??0;
                            $list[$bilDate]['totalAmount'] = bcadd($list[$bilDate]['totalAmount'], $row['BillAmount'], 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$bilDate]['list'][] = $tempArray;
                        }
                    }
                }

            }

            ksort($list);
        }

        return $list;
    }

    /**
     * 年缴
     * @param MemberHouse $memberHouse
     * @param string $chooseT
     * @return array
     * @throws
     */
    protected function yearBill(&$memberHouse, $chooseT = 'bill')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;
        //物业服务费
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
        }

        if ($memberHouse && $memberHouse->status == MemberHouse::STATUS_ACTIVE) {
            $_list = (new NewWindow())->getBill($memberHouse->house_id);
            if (sizeof($_list) > 0) {
                if($chooseT == 'bill'){
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $dateFormat = date('Y', $date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if($bill > 0){
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];
                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = date('Y', $date);
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] += $bill;
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount']??0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $bill, 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }
                    }
                } else {
                    foreach ($_list as $row) {
                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = $billDate[0];
                        $date = strtotime($date);
                        $dateFormat = date('Y', $date);

                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if($compValue > 0){
                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = date('Y', $date);
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] += $bill;
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount']??0;

                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $row['BillAmount'], 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        return $list;
    }

    /**
     * 针对凤池岛
     * 月缴。除物业管理费之外其他缴费类目可选
     * @param $houseId
     * @param string $chooseT
     * @param string $billCategory
     * @param array $chargeItemID
     * @return array
     * @throws \yii\base\ErrorException
     */
    private function billCategoryMonth($houseId, $chooseT='bill', $billCategory='', $chargeItemID=[34, 33])
    {
        $list = [];
        //物业服务费
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        if ($houseId) {
            $_list = (new NewWindow())->getBill($houseId);
            if (sizeof($_list) > 0) {
                if ($chooseT == 'bill') {
                    foreach ($_list as $row) {
                        if($billCategory == 'h' && in_array($row['ChargeItemID'], $chargeItemID)){
                            continue;
                        } else if($billCategory == 'notH' && !in_array($row['ChargeItemID'], $chargeItemID)){
                            continue;
                        }

                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);
                        $dateFormat = date('Ym', $date);
                        $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        if ($bill > 0) {
                            $tempArray = $row;
                            $tempArray['BillAmount'] = $bill;
                            $tempArray['BillTotalAmount'] = $row['BillAmount'];

                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = date('Y-m', $date);
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }
                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] += $bill;
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount'] ?? 0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $bill, 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }

                    }

                } else {
                    foreach ($_list as $row) {
                        if($billCategory == 'h' && in_array($row['ChargeItemID'], $chargeItemID)){
                            continue;
                        } else if($billCategory == 'notH' && !in_array($row['ChargeItemID'], $chargeItemID)){
                            continue;
                        }

                        $billDate = explode('-', $row['BillDate']);
                        //管理费按期初，其他按期末
                        $date = in_array($row['ChargeItemID'], [1, 14]) ? $billDate[0] : $billDate[1];
                        $date = strtotime($date);
                        $dateFormat = date('Ym', $date);
                        $tempArray = $row;
                        $tempArray['showBillFines'] = 'true';
                        $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                        //当期欠费大于0
                        $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                        if ($compValue > 0) {
                            if(!isset($list[$dateFormat]['date'])){
                                $list[$dateFormat]['date'] = date('Y-m', $date);
                                $list[$dateFormat]['isOpen'] = true;
                                $list[$dateFormat]['isCheckbox'] = false;
                            }

                            //物业服务费
                            $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                            if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                                $list[$dateFormat]['chargeItemAmount'] += $row['BillAmount'];
                            }

                            $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount'] ?? 0;
                            $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $row['BillAmount'], 2);
                            $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                            $list[$dateFormat]['list'][] = $tempArray;
                        }
                    }
                }
            }

            ksort($list);
        }

        return $list;
    }

    /**
     * 东方花城，物业管理费分类 ChargeItemID=2 代收水费
     * 自然年缴
     * @author HQM 2018/12/26
     * @param $houseId
     * @param string $chooseT
     * @param string $billCategory
     * @return array
     * @throws
     */
    private function billCategoryYearBill($houseId, $chooseT = 'bill', $billCategory='')
    {
        //物业服务费
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];
        $list = [];
        $tempArray = [];
        $bill = 0;

        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
        }

        $_list = (new NewWindow())->getBill($houseId);
        if (sizeof($_list) > 0) {
            if($chooseT == 'bill'){
                foreach ($_list as $row) {
                    $isH = in_array($row['ChargeItemID'], [2]);
                    if($billCategory == 'h' && $isH){
                        continue;
                    } elseif($billCategory == 'w' && !$isH){
                        continue;
                    }

                    $billDate = explode('-', $row['BillDate']);
                    //管理费按期初，其他按期末
                    $date = $billDate[0];
                    $date = strtotime($date);
                    $dateFormat = date('Y', $date);
                    $bill = bcsub($row['BillAmount'], $row['BillFines'], 2);

                    if($bill > 0){
                        $tempArray = $row;
                        $tempArray['BillAmount'] = $bill;
                        $tempArray['BillTotalAmount'] = $row['BillAmount'];
                        if(!isset($list[$dateFormat]['date'])){
                            $list[$dateFormat]['date'] = date('Y', $date);
                            $list[$dateFormat]['isOpen'] = true;
                            $list[$dateFormat]['isCheckbox'] = false;
                        }
                        //物业服务费
                        $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                        if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                            $list[$dateFormat]['chargeItemAmount'] += $bill;
                        }

                        $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount']??0;
                        $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $bill, 2);
                        $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                        $list[$dateFormat]['list'][] = $tempArray;
                    }
                }
            } else {
                foreach ($_list as $row) {
                    $isH = in_array($row['ChargeItemID'], [2]);
                    if($billCategory == 'h' && $isH){
                        continue;
                    } elseif($billCategory == 'w' && !$isH){
                        continue;
                    }

                    $billDate = explode('-', $row['BillDate']);
                    //管理费按期初，其他按期末
                    $date = $billDate[0];
                    $date = strtotime($date);
                    $dateFormat = date('Y', $date);

                    $tempArray = $row;
                    $tempArray['showBillFines'] = 'true';
                    $tempArray['currentAmountOf'] = bcsub($row['BillAmount'], $row['BillFines'], 2);

                    //当期欠费大于0
                    $compValue = bccomp($tempArray['currentAmountOf'], 0, 2);
                    if($compValue > 0){
                        if(!isset($list[$dateFormat]['date'])){
                            $list[$dateFormat]['date'] = date('Y', $date);
                            $list[$dateFormat]['isOpen'] = true;
                            $list[$dateFormat]['isCheckbox'] = false;
                        }
                        //物业服务费
                        $list[$dateFormat]['chargeItemAmount'] = $list[$dateFormat]['chargeItemAmount'] ?? 0;
                        if(in_array($row['ChargeItemID'], $discountsChargeItemIds)){
                            $list[$dateFormat]['chargeItemAmount'] += $bill;
                        }

                        $list[$dateFormat]['totalAmount'] = $list[$dateFormat]['totalAmount']??0;

                        $list[$dateFormat]['totalAmount'] = bcadd($list[$dateFormat]['totalAmount'], $row['BillAmount'], 2);
                        $list[$dateFormat]['contractNo'][] = $row['ContractNo'];
                        $list[$dateFormat]['list'][] = $tempArray;
                    }
                }
            }
        }

        ksort($list);

        return $list;
    }

}