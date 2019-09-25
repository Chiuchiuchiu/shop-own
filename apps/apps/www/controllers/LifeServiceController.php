<?php
/**
 * Created by
 * Author: zhao
 * Time: 2017/2/6 15:25
 * Description:
 */

namespace apps\www\controllers;


use apps\api\models\House;
use common\models\MemberHouse;
use common\models\MobileOrder;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\ProjectServiceCategory;
use common\models\ProjectServicePhone;
use common\models\SysSwitch;
use components\juhe\Recharge;
use components\newWindow\NewWindow;
use yii\data\ActiveDataProvider;
use yii\db\Connection;

class LifeServiceController extends Controller
{
    public function actionMobile(){
        if($this->isPost){
            $mobile =  $this->post('mobile');
            $amount =  $this->post('amount');

            $recharge = new Recharge('526084c4628d4ab41f974241abe1bafb','JHdd0b7ae7731aa448e571fc82f898dab9');
            if(!$recharge->telcheck($mobile,$amount)){
                return $this->renderJsonFail("该手机不支持此充值");
            }
            $order = new MobileOrder();
            $order->mobile = $mobile;
            $order->amount = $amount;
            $order->member_id = $this->user->id;
            $order->recharge_type = MobileOrder::RECHARGE_TYPE_DEPOSIT;
            if($order->save()){
                //创建支付
                return $this->renderJsonSuccess(['id'=>$order->id]);
            }else{
                return $this->renderJsonFail("订单创建失败");
            }
        }

        return $this->render('mobile',[]);
    }

    /**
     * 测试
     * 有偿维修账单列表（微信支付）
     * @param null $id
     * @return string|\yii\web\Response
     * @throws \yii\base\ErrorException
     * @author zhaowenxi
     */
    public function actionTestBill($id=null)
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

        $useNewPay = 'w';   //直接使用微信支付 zhaowenxi

        $list = $tempArray = [];

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {

            $_list = (new NewWindow())->getBill($model->house_id);

            if (sizeof($_list) > 0) {
                foreach($_list as $k => $v){
                    if($v['ChargeItemTypeID'] == PmOrder::CHARGE_TYPE_3){   //临时缴费
                        $billDate = explode('-', $v['BillDate']);
                        $date = strtotime($billDate[0]);
                        $bill = bcsub($v['BillAmount'], $v['BillFines'], 2);
                        if($bill > 0){
                            $list[date('Y-m', $date)]['totalAmount'] = $bill;
                            $list[date('Y-m', $date)]['shouldChargeDate'] = $v['ShouldChargeDate'];
                            $list[date('Y-m', $date)]['list'][] = [
                                'chargeItemName' => $v['ChargeItemName'],
                                'billAmount' => $v['BillAmount'],
                                'contractNo' => $v['ContractNo'],
                                'billDate' => $v['BillDate'],
                                'chargeItemID' => $v['ChargeItemID'],
                            ];
                        }
                    }
                }

                ksort($list);
            }
        }

        return $this->render('test-bill', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 有偿维修账单列表（微信支付）
     * @param null $id
     * @return string|\yii\web\Response
     * @throws \yii\base\ErrorException
     * @author zhaowenxi
     */
    public function actionBillList($id=null)
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

        $useNewPay = 'w';   //直接使用微信支付 zhaowenxi

        $list = $tempArray = [];

        if ($model && $model->status == MemberHouse::STATUS_ACTIVE) {

            $_list = (new NewWindow())->getBill($model->house_id);

            if (sizeof($_list) > 0) {
                foreach($_list as $k => $v){
                    if($v['ChargeItemTypeID'] == PmOrder::CHARGE_TYPE_3){   //临时缴费
                        $billDate = explode('-', $v['BillDate']);
                        $date = strtotime($billDate[0]);
                        $bill = bcsub($v['BillAmount'], $v['BillFines'], 2);
                        if($bill > 0){
                            $list[date('Y-m', $date)]['totalAmount'] = $bill;
                            $list[date('Y-m', $date)]['shouldChargeDate'] = $v['ShouldChargeDate'];
                            $list[date('Y-m', $date)]['list'][] = [
                                'chargeItemName' => $v['ChargeItemName'],
                                'billAmount' => $v['BillAmount'],
                                'contractNo' => $v['ContractNo'],
                                'billDate' => $v['BillDate'],
                                'chargeItemID' => $v['ChargeItemID'],
                            ];
                        }
                    }
                }

                ksort($list);
            }
        }

        return $this->render('bill-list', [
            'model' => $model,
            'list' => $list,
            'autoPay'=> null,
            'useNewPay' => $useNewPay,
        ]);
    }

    /**
     * 缴费按钮
     * 生成订单，并调用wx-js
     * @return string
     * @throws \yii\base\ErrorException
     * @author zhaowenxi
     */
    public function actionRepairBillSubmit()
    {
        if ($this->isGet) {
            return $this->renderJsonFail("forbidden");
        }

        $houseId = $this->post('houseId');

        $contractNo = $this->post('contractNo');
        $contractNo = explode(',', $contractNo);

        $house = House::findOne($houseId);
        if (!$house) {
            return $this->renderJsonFail("提交信息有误");
        }

        if(SysSwitch::inVal('pauseWeChatPayment', $house->project_house_id)){
            return $this->renderJsonFail("该项目暂取消“微信缴费”业务！");
        }

        $billList = (new NewWindow())->getBill($houseId);

        if (!is_array($billList)) {
            return $this->renderJsonFail("账单接口维护中,请稍后再试");
        }

        $totalAmount = 0;
        $orderItems = [];

        foreach ($billList as $bill) {
            foreach ($contractNo as $k => $no) {
                if ($no == $bill['ContractNo']) {
                    $totalAmount += round($bill['BillAmount'], 2);
                    $orderItems[] = $bill;

                    unset($contractNo[$k]);
                    break;
                }
            }
        }

        $discountStatus = 0;

        $order = new PmOrder();
        $order->house_id = $house->house_id;
        $order->project_house_id = $house->project_house_id;
        $order->member_id = $this->user->id;
        $order->total_amount = $totalAmount;
        $order->bill_type = PmOrder::BILL_TYPE_ONE;
        $order->discount_status = $discountStatus;
        $order->charge_type = PmOrder::CHARGE_TYPE_3;

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

    /**
     * 便民电话列表
     * @author zhaowenxi
     */
    public function actionTelephone($selectedCategory = null){

        $category = ProjectServiceCategory::find()
            ->where(["status" => 1, 'project_house_id' => $this->project->house_id])
            ->orWhere(['project_house_id' => 0])->orderBy("`project_house_id` ASC, `order_by` DESC")
            ->asArray()->all();

        $selectedCategory || $selectedCategory = $category ? $category[0]['id'] : 0;

        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = ProjectServicePhone::find()
            ->where(['project_house_id' => $this->project->house_id, 'status' => ProjectServicePhone::STATUS_ACTIVE])
            ->andFilterWhere(['category_id' => $selectedCategory]);

        return $this->render('telephone', [
            'dataProvider' => $dataProvider,
            'category' => $category,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}