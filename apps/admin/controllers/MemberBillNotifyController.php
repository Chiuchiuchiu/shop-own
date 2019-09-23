<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/5
 * Time: 14:57
 */

namespace apps\admin\controllers;


use apps\admin\models\FpzzLog;
use common\models\Butler;
use common\models\HouseBillOutline;
use common\models\ParkingOrder;
use common\models\ParkingOrderNotifyErrorLog;
use common\models\PmOrder;
use common\models\PmOrderFpzz;
use common\models\PmOrderItem;
use common\models\PmOrderToButlerErrorLog;
use common\models\QyWeixinNotifyLog;
use components\email\Email;
use components\newWindow\NewWindow;
use components\wechatSDK\QYWechatSDK;
use components\wechatSDK\WechatSDK;
use console\models\ReminderLog;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

class MemberBillNotifyController extends Controller
{
    protected $missPermission = ['member-bill-notify/notify',
                                 'member-bill-notify/repair-notify',
                                 'member-bill-notify/tcis-open-fp',
                                 'member-bill-notify/bill-charge-offs',
                                 'member-bill-notify/parking-order-notify',
                                 'member-bill-notify/re-log',
                                 'member-bill-notify/newwindow-open-fp'];
    public $enableCsrfValidation = false;

    /**
     * 业主付费通知
     * @throws ErrorException
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionNotify()
    {
        $postData = $this->post();
        $butlerUserId = isset($postData['butlerUserId']) ? $postData['butlerUserId'] : null;
        $pmOrderId = $postData['pmOrderId'];
        $send = isset($postData['send']) ? $postData['send'] : false;

        $keyExists = $this->redisKeyExists($pmOrderId, 'order_', 120);
        if($keyExists === false){
            $this->notificationButler($pmOrderId, $butlerUserId);
        }

        if($send){
            $this->pmOrderToButlerErrorLog($pmOrderId);
        }

        if(isset($postData['payStatus'])){
            if($postData['payStatus'] == PmOrder::STATUS_PAYED){
                $this->billChargeOffs($pmOrderId);
            }
        }
    }

    /**
     * 有偿维修付费通知
     * @throws ErrorException
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionRepairNotify()
    {
        $postData = $this->post();
        $butlerUserId = isset($postData['butlerUserId']) ? $postData['butlerUserId'] : null;
        $pmOrderId = $postData['pmOrderId'];
        $send = isset($postData['send']) ? $postData['send'] : false;

        $keyExists = $this->redisKeyExists($pmOrderId, 'order_', 120);
        if($keyExists === false){
            $this->repairNotificationButler($pmOrderId, $butlerUserId);
        }

        if($send){
            $this->repairOrderToButlerErrorLog($pmOrderId);
        }

        if(isset($postData['payStatus'])){
            if($postData['payStatus'] == PmOrder::STATUS_PAYED){
                $this->billChargeOffs($pmOrderId);
            }
        }
    }

    /**
     * 停车收费通知处理
     * @return string
     */
    public function actionParkingOrderNotify()
    {
        $postData = $this->post();
        $butlerUserId = isset($postData['butlerUserId']) ? $postData['butlerUserId'] : null;
        $parkingOrderId = $postData['parkingOrderId'];

        $keyExists = $this->redisKeyExists($parkingOrderId, 'parking_');
        if($keyExists === false){
            $this->parkingOrderNotificationButler($parkingOrderId, $butlerUserId);
        }

        return 'success';
    }

    public function actionReLog()
    {
        $postData = $this->post();
        $data = $postData;
        ReminderLog::writeLog(1, $data);
    }

    /**
     * 账单销账
     * @throws ErrorException
     */
    public function actionBillChargeOffs()
    {
        $postData = $this->post();
        $pmOrderId = $postData['pmOrderId'];

        if(isset($postData['payStatus'])){
            if($postData['payStatus'] == PmOrder::STATUS_PAYED){
                $this->billChargeOffs($pmOrderId);
            }
        }
    }

    /**
     * 新视窗开具电子发票
     * @return bool
     * @throws ErrorException
     */
    public function actionNewwindowOpenFp()
    {
        $postData = $this->post();
        if($this->redisKeyExists($postData['pmOrderFpzzId'],'orderFp_',60)){
            return true;
        }

        $pmOrderId = $postData['pmOrderFpzzId'];

        $pmOrder = PmOrder::findOne(['id' => $pmOrderId, 'status' => PmOrder::STATUS_PAYED]);
        if(isset($pmOrder->items)){
            $chargeDetailIdList = [];
            foreach ($pmOrder->items as $row){
                /**
                 * @var PmOrderItem $row
                 */
                if(!empty($row->charge_detail_id_list)){
                    $chargeDetailIdList[] = $row->charge_detail_id_list;
                } else {
                    $unserizleData = unserialize($row->bill_content);
                    if(isset($unserizleData['ChargeDetailIDList'])){
                        $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                    }
                }
            }

            if(!empty($chargeDetailIdList)){
                $chargeDetailIdList = implode(',', $chargeDetailIdList);
                $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList, 1);

                $this->writeFpzzLog($pmOrderId, $chargeDetailIdList, $newWindows);

                if(!$newWindows['Response']['Data']['Record'][0]['BillPDFUrl']){

                    /*$pmOrderFpzzModel = PmOrderFpzz::findOne(['pm_order_id' => $pmOrderId]);
                    if($pmOrderFpzzModel){
                        $pmOrderFpzzModel->status = PmOrderFpzz::STATUS_WAIT_REVIEW;
                        $pmOrderFpzzModel->save();
                    }*/

                    $setSub = '请求新视窗开具发票失败，返回错误码：' . $newWindows['Response']['Data']['NWRespCode'];
                    $NWErrMsg = $newWindows['Response']['Data']['NWErrMsg'];
                    $NWErrMsg .= '--------订单ID：' . $pmOrder->id;

                    $newRes = '返回值：' . json_encode($newWindows['Response']['Data']['Record']) ?? '-';

                    $projectKeyUrl = $pmOrder->project->url_key;

                    //发送发票模板消息通知用户
                    $this->sendWxTemplateToMember('o4sxcxLBzmvgpO_0BVo9gPbkQWxE', $projectKeyUrl, $newRes);
//                    (new Email())->sendToAdmin($setSub, '315780351@qq.com', $NWErrMsg);
                    exit(0);

                }
            }
        }

        exit(0);
    }

    /**
     * @param integer $pmOrderId
     * @param string|array $postData
     * @param string|array $result
     * @param string $type
     */
    protected function writeFpzzLog($pmOrderId, $postData, $result, $type='-')
    {
        $fpzzLog = new FpzzLog();
        $fpzzLog->pm_order_id = $pmOrderId;
        $fpzzLog->post_data = serialize($postData);
        $fpzzLog->result = serialize($result);
        $fpzzLog->fp_cached_id = isset($result['object']['id']) ? $result['object']['id'] : '';
        $fpzzLog->type = $type;
        $fpzzLog->save();
    }

    /**
     * 物业缴费通知
     * @param $pmOrderId
     * @param null $butlerUserId
     * @param string $msgtype
     * @param int $agentid
     * @return bool|int|mixed|string
     */
    protected function notificationButler($pmOrderId, $butlerUserId=null, string $msgtype='text',int $agentid=53)
    {
        $pmOrderSe = PmOrderToButlerErrorLog::findOne(['pm_order_id' => $pmOrderId, 'status' => 1]);
        if($pmOrderSe){
            return false;
        }

        $order = PmOrder::findOne(['id' => $pmOrderId]);
        $pmOrderHouseId = $order->house_id;
        $users = $butlerUserId;
        $contents = '';

        $butlerModel = Butler::find()->select('wechat_user_id')->where(['group' => [1,3,4,5], 'status' => Butler::STATUS_ENABLE, 'project_house_id' => $order->project_house_id])->asArray()->all();

        if($butlerModel){
            if(empty($users)){
                $users = ArrayHelper::getColumn($butlerModel, 'wechat_user_id');
                $users = implode($users,'|');
            }
            if(is_array($order->items)){
                foreach ($order->items as $item) {
                    /* @var $item PmOrderItem*/
                    $contents .= $item->charge_item_name . '：' . $item->bill_date . "，";
                }
            }
	    
            $pmOrderDiscountsAmount = '-';
            if(isset($order->pmOrderDiscounts)){
                $pmOrderDiscountsAmount = $order->pmOrderDiscounts->discounts_amount;
            }
            $billDate = date('Y-m-d H:i:s', $order->payed_at);
            $data = [
                'touser' => $users,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "物业缴费【{$billDate}】，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n房产【{$order->house->ancestor_name}】，\n优惠【{$pmOrderDiscountsAmount}】，\n缴费类型【{$order->billTypeText}】，\n实付金额【{$order->total_amount}】，\n商户订单号：【{$order->number}】，\n收费项目：【{$contents}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(empty($res)){
                $res = $wechatQYSDK->sendMsg($data);
                if(empty($res)){
                    PmOrderToButlerErrorLog::writeLog($pmOrderId, $users);
                }
            }

            //从历史欠费记录表删除
            HouseBillOutline::deleteAll(['house_id' => $pmOrderHouseId]);
            PmOrderToButlerErrorLog::writeLog($pmOrderId, 'Hq', 1);

            return $res;
        }

        return 0;
    }

    /**
     * 停车收费通知管家
     * @param $parkingOrderId
     * @param null $butlerUserId
     * @param string $msgtype
     * @param int $agentid
     * @return bool|int|mixed|string
     */
    protected function parkingOrderNotificationButler($parkingOrderId, $butlerUserId=null, string $msgtype='text',int $agentid=53)
    {
        $order = ParkingOrder::findOne(['id' => $parkingOrderId]);
        $users = $butlerUserId;

        $butlerModel = Butler::find()
            ->select('wechat_user_id')
            ->where(['group' => [1,2,3], 'status' => Butler::STATUS_ENABLE, 'project_house_id' => $order->project_house_id])
            ->asArray()
            ->all();

        if($butlerModel){
            if(empty($users)){
                $users = ArrayHelper::getColumn($butlerModel, 'wechat_user_id');
                $users = implode($users,'|');
            }

            $data = [
                'touser' => $users,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "停车收费，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n车牌号【{$order->plate_number}】，\n实付金额【{$order->amount}】，\n商户订单号：【{$order->number}】，\n类型：【{$order->typeText}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(empty($res)){
                $res = $wechatQYSDK->sendMsg($data);
                if(empty($res)){
                    ParkingOrderNotifyErrorLog::writeLog($parkingOrderId, $users);
                }

                return $this->renderJsonFail('error');
            }

            return $res;
        }

        return 0;
    }

    /**
     * 有偿维修缴费通知管家
     * @param $pmOrderId
     * @param null $butlerUserId
     * @param string $msgtype
     * @param int $agentid
     * @return bool|int|mixed|string
     * @author zhaowenxi
     */
    protected function repairNotificationButler($pmOrderId, $butlerUserId=null, string $msgtype='text',int $agentid=53)
    {
        $pmOrderSe = PmOrderToButlerErrorLog::findOne(['pm_order_id' => $pmOrderId, 'status' => 1]);
        if($pmOrderSe){
            return false;
        }

        $order = PmOrder::findOne(['id' => $pmOrderId, 'status' => PmOrder::CHARGE_TYPE_3]);
        $pmOrderHouseId = $order->house_id;
        $users = $butlerUserId;
        $contents = '';

        $butlerModel = Butler::find()->select('wechat_user_id')->where(['group' => [1,3,4,5], 'status' => Butler::STATUS_ENABLE, 'project_house_id' => $order->project_house_id])->asArray()->all();

        if($butlerModel){
            if(empty($users)){
                $users = ArrayHelper::getColumn($butlerModel, 'wechat_user_id');
                $users = implode($users,'|');
            }
            if(is_array($order->items)){
                foreach ($order->items as $item) {
                    /* @var $item PmOrderItem*/
                    $contents .= $item->charge_item_name . '：' . $item->bill_date . "，";
                }
            }

            $pmOrderDiscountsAmount = '-';
            if(isset($order->pmOrderDiscounts)){
                $pmOrderDiscountsAmount = $order->pmOrderDiscounts->discounts_amount;
            }
            $billDate = date('Y-m-d H:i:s', $order->payed_at);
            $data = [
                'touser' => $users,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "个人维修缴费【{$billDate}】，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n房产【{$order->house->ancestor_name}】，\n优惠【{$pmOrderDiscountsAmount}】，\n缴费类型【{$order->billTypeText}】，\n实付金额【{$order->total_amount}】，\n商户订单号：【{$order->number}】，\n收费项目：【{$contents}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(empty($res)){
                $res = $wechatQYSDK->sendMsg($data);
                if(empty($res)){
                    PmOrderToButlerErrorLog::writeLog($pmOrderId, $users);
                }
            }

            //从历史欠费记录表删除
            HouseBillOutline::deleteAll(['house_id' => $pmOrderHouseId]);
            PmOrderToButlerErrorLog::writeLog($pmOrderId, 'Hq', 1);

            return $res;
        }

        return 0;
    }

    /**
     * 有偿维修缴费通知管家失败，重新处理通知 $msgtype: text, $agentid: 53
     * @param integer $pmOrderId
     * @param string $msgtype
     * @param int $agentid
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    protected function repairOrderToButlerErrorLog($pmOrderId, $msgtype='text', $agentid=53)
    {
        $model = PmOrderToButlerErrorLog::findOne(['pm_order_id' => $pmOrderId]);

        if($model){
            $order = PmOrder::findOne(['id' => $model->pm_order_id, 'status' => PmOrder::CHARGE_TYPE_3]);
            $contents = '';
            $billDate = date('Y-m-d H:i:s', $order->payed_at);
            if(is_array($order->items)){
                foreach ($order->items as $item) {
                    /* @var $item PmOrderItem */
                    $contents .= $item->charge_item_name . '：' . $item->bill_date . "，";
                }
            }
            $pmOrderDiscountsAmount = '-';
            if(isset($order->pmOrderDiscounts)){
                $pmOrderDiscountsAmount = $order->pmOrderDiscounts->discounts_amount;
            }
            $data = [
                'touser' => $model->to_user_id,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "个人维修缴费【{$billDate}】，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n房产【{$order->house->ancestor_name}】，\n实付金额【{$order->total_amount}】，\n订单号：【{$order->number}】，\n收费项目：【{$contents}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(!empty($res)){
                $model->delete();
            }

        }

        exit();
    }

    /**
     * @param array|string $sendData
     * @param array|string $result
     */
    protected function writeQyWeixinNotifyLog($sendData, $result)
    {
        $model = new QyWeixinNotifyLog();
        $model->send_data = serialize($sendData);
        $model->result = serialize($result);
        $model->ip = \Yii::$app->request->userIP;

        $model->save();
    }

    /**
     * 账单核销部分
     * @param $pmOrderId
     * @param string $status
     * @throws ErrorException
     */
    protected function billChargeOffs($pmOrderId, $status='0000')
    {
        $model = PmOrderItem::find()->where(['pm_order_id' => $pmOrderId])->orderBy('id DESC');

        foreach($model->each() as $row){
            /**
             * 账单核销部分
             * @var $row PmOrderItem
             */
            $res = (new NewWindow())->payBill(
                $row->pm_order_id . '-' . $row->id,
                $row->pmOrder->project_house_id,
                $row->contract_no,
                $row->amount,
                $row->pmOrder->payed_at
            );
            if ($res) {
                /*$row->status = $res[0]['ReturnCode'];
                $row->bankBillNo = $res[0]['BankBillNo'];*/
                $row->completed_at = time();
                $row->save();
            }
        }
    }

    /**
     * 业主缴费通知管家失败，重新处理通知 $msgtype: text, $agentid: 53
     * @param integer $pmOrderId
     * @param string $msgtype
     * @param int $agentid
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    protected function pmOrderToButlerErrorLog($pmOrderId, $msgtype='text', $agentid=53)
    {
        $model = PmOrderToButlerErrorLog::findOne(['pm_order_id' => $pmOrderId]);

        if($model){
            $order = PmOrder::findOne(['id' => $model->pm_order_id]);
            $contents = '';
            $billDate = date('Y-m-d H:i:s', $order->payed_at);
            if(is_array($order->items)){
                foreach ($order->items as $item) {
                    /* @var $item PmOrderItem */
                    $contents .= $item->charge_item_name . '：' . $item->bill_date . "，";
                }
            }
            $pmOrderDiscountsAmount = '-';
            if(isset($order->pmOrderDiscounts)){
                $pmOrderDiscountsAmount = $order->pmOrderDiscounts->discounts_amount;
            }
            $data = [
                'touser' => $model->to_user_id,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "物业缴费【{$billDate}】，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n房产【{$order->house->ancestor_name}】，\n优惠【{$pmOrderDiscountsAmount}】，\n实付金额【{$order->total_amount}】，\n商户订单号：【{$order->number}】，\n收费项目：【{$contents}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(!empty($res)){
                $model->delete();
            }

        }

        exit();
    }

    private function redisKeyExists($redisKeyValue, $redisKeyPrefix='order_', $expire=60)
    {
        $key = $redisKeyPrefix . $redisKeyValue;

        $redis = \Yii::$app->redis;
        if($redis->exists($key)){
            return true;
        }

        $redis->set($key, $redisKeyValue);
        $redis->expire($key, $expire);

        return false;
    }

    /**
     * 电子发票模板消息，目前只发送开发者，以后稳定可改为发送给业主
     * @param string $memberOpenId
     * @param string $projectKeyUrl
     * @param string $newRes
     */
    private function sendWxTemplateToMember($memberOpenId, $projectKeyUrl, $newRes)
    {
        $url = 'http://'.$projectKeyUrl.'.'.\Yii::$app->params['domain.p'];
        $url .= '/tcis/lists?';

        $postData = [
            'touser' => $memberOpenId,
            'template_id' => 'RhrZ9NcEOOisF_vnyw7fq5FE4uljcl_k5Hdcj68x0kU',
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => '您好，您的电子发票申请状态进度',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => date('Y-m-d H:i:s'),
                ],
                'keyword2' => [
                    'value' => '开票失败：设备异常。待技术核实原因',
                ],
                'keyword3' => [
                    'value' => "第一次" . $newRes,
                ],
                'keyword4' => [
                    'value' => '',
                ],
                'keyword5' => [
                    'value' => '-',
                ],
                'remark' => [
                    'value' => '如有疑问，请联系项目管家',
                ],
            ]
        ];
        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);
        $wx_obj->sendTemplateMessage($postData);
    }

}
