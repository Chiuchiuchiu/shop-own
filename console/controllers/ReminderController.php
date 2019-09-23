<?php
/**
 * Created by PhpStorm.
 * User: HuangQiMIn
 * Date: 2016/12/27
 * Time: 下午4:09
 * 物业费催缴功能
 */

namespace console\controllers;


use common\models\House;
use common\models\Member;
use common\models\MemberHouse;
use components\wechatSDK\WechatSDK;
use console\models\ReminderLog;
use yii\console\Controller;
use common\models\HouseBillOutline;
use yii\helpers\ArrayHelper;

class ReminderController extends Controller
{

    //通过模板消息向业主推送物业账单信息
    public function actionPushUtilityBills($inputDateNumber=false){

        $this->getBillOutlineDataSendTemplate($inputDateNumber);

    }


    //获取表 house_bill_outline 数据，发送催费通知业主
    protected function getBillOutlineDataSendTemplate($inputDateNumber)
    {
        //一个月中的第几天，不带前导零（1 到 31）
        $getDateNumber = date('j', time());

        if($inputDateNumber){
            $getDateNumber = $inputDateNumber;
        }

        if (!in_array($getDateNumber, [1,10,25])){
            $this->stdout('请输入日期号：1|10|25');
            return;
        }

        $number = 0;
        $templateId = '';
        $messThemeValue = '';
        $billDateValue = '';
        $billAmountValue = '';

        foreach (HouseBillOutline::find()->where( ['process_status' => 0])->each(100) as $row)
        {
            /**
             * @var $row HouseBillOutline
             */

            switch ($getDateNumber){
                case 1:
                    $templateId = \Yii::$app->params['wechat']['templateIds'][$getDateNumber]['templateId'];
                    $messThemeValue = \Yii::$app->params['wechat']['templateIds'][$getDateNumber]['message'];
                    break;
                case 10:
                    $templateId = \Yii::$app->params['wechat']['templateIds'][$getDateNumber]['templateId'];
                    $messThemeValue = \Yii::$app->params['wechat']['templateIds'][$getDateNumber]['message'];
                    break;
                case 25:
                    $templateId = \Yii::$app->params['wechat']['templateIds'][$getDateNumber]['templateId'];
                    $messThemeValue = \Yii::$app->params['wechat']['templateIds'][$getDateNumber]['message'];
                    break;
            }

            $cost = $this->statisticalCost(unserialize($row['aggregate_data']));
            $billAmountValue = $cost.'元';


            //获取 MemberHouse 关联用户的 微信 open_id
            $memberResults = MemberHouse::find()->select(['member_id',])->with('member')->where(['house_id' => $row['house_id']])->all();

            if ($memberResults){

                foreach ($memberResults as $key => $memberResultsRow){

                    /**
                     * @var $memberResultsRow MemberHouse
                     */
                    $weChatOpenId = null;
                    $weChatOpenId = $memberResultsRow->member->wechat_open_id;

                    $billAddressValue = $row->house->showName;

                    //发送微信模板消息
                    $this->sendWxTemplateMessage($weChatOpenId, $templateId, $messThemeValue, $billAddressValue, $billAmountValue);
                    $this->updateHouseBillOutlineProcessStatus($row['house_id'], $row['process_status']);
                }
            }
        }

    }

    //筛选客户物业服务费统计费用
    protected function statisticalCost($billData)
    {
        if (empty($billData)) return 1;

        $init_number = 0.00;

        foreach ( $billData as $key => $value){

            switch ($value['ChargeItemID']){
                case 1:
                case 14:
                    $date = explode('-', $value['BillDate']);
                    if (strtotime($date[0]) < time()){
                        $first = bcadd($value['BillAmount'], $value['BillFines'], 2);
                        $init_number = bcadd($first, $init_number, 2);
                    }
                    break;
            }

        }

        return $init_number;

    }


    /**
     * 发送微信模板消息
     * @param string $toUserOpenId
     * @param string $templateId
     * @param string $messThemeValue
     * @param string $billAddressValue
     * @param string $billAmountValue
     */
    protected function sendWxTemplateMessage($toUserOpenId, $templateId, $messThemeValue, $billAddressValue, $billAmountValue)
    {
        if (empty($toUserOpenId)) return;

        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);

        //推送模板消息内容
        $sendData = [
            'touser' => $toUserOpenId,
            'template_id' => $templateId,
            'data' => [
                'mess_theme' => [
                    'value' => $messThemeValue,
                ],
                'bill_date' => [
                    'value' => $billAddressValue
                ],
                'bill_amount' => [
                    'value' => $billAmountValue,
                    'color' => '#FF3030'
                ],
            ],
        ];


        $send_result = $wx_obj->sendTemplateMessage($sendData);

        $reminderLogModel = ReminderLog::createSelfObj();

        $reminderLogModel->to_wechat_open_id = $toUserOpenId;
        $reminderLogModel->log_data = serialize($send_result);


        if ($send_result){
            \Yii::info("发送成功".serialize($send_result), 'ReminderController');

        } else {
            $reminderLogModel->send_status = 1;

            \Yii::error("发送失败：".serialize($send_result), 'ReminderController');
        }

        $reminderLogModel->insert();

    }


    protected function updateHouseBillOutlineProcessStatus($houseId, $processStatus)
    {
        if ($processStatus > 0){
            return;
        }

        $model = HouseBillOutline::findOrCreate($houseId);

        $model->process_status = 1;

        $res = $model->save();

        $this->stdout('更新 House_bill_outline 催费处理状态', serialize($res));
    }

}