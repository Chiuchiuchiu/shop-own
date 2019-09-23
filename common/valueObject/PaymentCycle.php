<?php
/**
 * 物业缴费周期
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018-11-30
 * Time: 14:44
 */

namespace common\valueObject;


class PaymentCycle
{

    /**
     * 月缴： 自然月
     * @author HQM 2018/11/30
     * @param array $_list
     * @param string $chooseT all 全，bill 减除滞纳金
     * @return array
     */
    public static function monthBill($_list, $chooseT='bill')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;
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

        return $list;
    }

    /**
     * 季度缴: 自然月
     * @author HQM 2018/11/30
     * @param $_list
     * @param string $chooseT
     * @return array
     * @throws
     */
    public static function quarterBill($_list, $chooseT = 'bill')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;
        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
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

        return $list;
    }

    /**
     * 半年缴: 自然月
     * @author HQM 2018/11/30
     * @param array $_list
     * @param string $chooseT
     * @return array
     * @throws
     */
    public static function halfYearBill($_list, $chooseT = 'bill')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
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

        return $list;
    }

    /**
     * 一年缴: 自然月
     * @author HQM 2018/11/30
     * @param array $_list
     * @param string $chooseT
     * @return array
     * @throws
     */
    public static function yearBill($_list, $chooseT = 'bill')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;
        //物业服务费
        $discountsChargeItemIds = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
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

        return $list;
    }

    /**
     * 物业管理费分类 ChargeItemID=2 代收水费
     * 自然年缴
     * @author HQM 2018/12/20
     * @param $_list
     * @param string $chooseT
     * @param string $billCategory
     * @return array
     */
    public static function billCategoryYearBill($_list, $chooseT = 'bill', $billCategory='')
    {
        $list = [];
        $tempArray = [];
        $bill = 0;

        if (!in_array($chooseT, ['bill', 'all'])) {
            $chooseT = 'bill';
        }

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

        return $list;
    }

}