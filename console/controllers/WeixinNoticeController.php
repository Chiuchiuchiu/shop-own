<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/10
 * Time: 14:26
 */

namespace console\controllers;


use common\models\Butler;
use common\models\Member;
use common\models\ParkingOrder;
use common\models\ParkingPayOrderLog;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\PmOrderToButlerErrorLog;
use common\models\Project;
use common\models\QyWeixinNotifyLog;
use common\models\Repair;
use components\email\Email;
use components\weChat\WechatTemplate;
use components\wechatSDK\QYWechatSDK;
use components\wechatSDK\WechatSDK;
use yii\console\Controller;

class WeixinNoticeController extends Controller
{
    /**
     * @param int $memberId
     */
    public function actionNotice(int $memberId)
    {
        $memberModel = Member::findOne(['id' => $memberId]);
        if(!$memberModel){
            $this->stdout('No Member');
            die();
        }

        $postData = [
            'touser' => $memberModel->wechat_open_id,
            'msgtype' => 'text',
            'text' => [
                'content' => '测试内容'
            ]
        ];

        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);
        $res = $wx_obj->sendCustomMessage($postData);
        var_dump($res);
    }

    /**
     * 发送报事模板消息：memberId，repairId，templateId[default
     * @param int $memberId
     * @param int $repairId
     * @param string $templateId
     */
    public function actionSendTemplate(int $memberId,int $repairId, string $templateId = '78Mp_ZYMgjs2_g_ozaIBQvgeqO8Cjp1kIrVB94zE4f8')
    {
        $memberModel = Member::findOne(['id' => $memberId]);
        if(!$memberModel){
            $this->stdout('No Member');
            die();
        }

        $repair = Repair::findOne(['id' => $repairId]);

        $postData = [
            'touser' => $memberModel->wechat_open_id,
            'template_id' => $templateId,
            'url' => '',
            'data' => [
                'first' => [
                    'value' => $repair->flowStyleText . '进展',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $repair->address,
                ],
                'keyword2' => [
                    'value' => $repair->content,
                ],
                'keyword3' => [
                    'value' => date('Y-m-d H:i:s', $repair->created_at),
                ],
                'keyword4' => [
                    'value' => $repair->statusText,
                    'color' => '#173177',
                ],
                'keyword5' => [
                    'value' => '已抄送管家/工程',
                ],
                'remark' => [
                    'value' => '如有疑问，请联系项目管家',
                ],
            ]
        ];

        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);
        $res = $wx_obj->sendTemplateMessage($postData);

        $this->stdout($wx_obj->errMsg . PHP_EOL);
        $this->stdout($wx_obj->errCode . PHP_EOL);

        var_export($res);
    }

    /**
     * 企业微信：发消息 param：$butler, $content(de), $messageType(de), $agentid(de)
     * @param int $butler
     * @param string $content
     * @param string $messageType
     * @param int $agentid
     */
    public function actionWQSendText(int $butler, $content='测试', $messageType='text', $agentid=53)
    {
        $butlerInfo = Butler::findOne(['id' => $butler, 'status' => Butler::STATUS_ENABLE]);

        if($butlerInfo){
            $data = [
                'touser' => $butlerInfo->wechat_user_id,
                'msgtype' => $messageType,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => $content,
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
            $res = $wechatQYSDK->sendMsg($data);

            if(!$res){
                $this->stdout($wechatQYSDK->errCode . "\n");
                $this->stdout($wechatQYSDK->errMsg . "\n");
                exit;
            }

            var_dump($res);
            die;
        }

        $this->stdout('no found butler');
    }

    public function actionGetAccessToken()
    {
        $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
        $res = $wechatQYSDK->getAccessToken();

        var_dump($res);
    }

    /**
     * 业主缴费通知管家失败，重新处理通知 $msgtype: text, $agentid: 53
     * @param string $msgtype
     * @param int $agentid
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionPmOrderToButlerErrorLog($msgtype='text', $agentid=53)
    {
        $model = PmOrderToButlerErrorLog::find()
            ->where(['status' => 0])
            ->orderBy('id DESC');

        foreach($model->each(100) as $row){
            /**
             * @var PmOrderToButlerErrorLog $row
             */
            $order = PmOrder::findOne(['id' => $row->pm_order_id]);
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
                'touser' => $row->to_user_id,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "物业缴费【{$billDate}】，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n房产【{$order->house->ancestor_name}】，\n缴费类型【{$order->billTypeText}】，\n优惠【{$pmOrderDiscountsAmount}】，\n实付金额【{$order->total_amount}】，\n商户订单号：【{$order->number}】，\n收费项目：【{$contents}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(empty($res)){
                self::redis($row->pm_order_id);
                $this->stdout("SendError：{$row->pm_order_id} \n");
            } else {
                $this->stdout("SendSuccess: {$row->pm_order_id} \n");
            }

            $row->delete();
        }

        $this->stdout("PmOrder Done \n");

        $this->actionParkingPayOrderLog();

        exit("PmOrder Exit \n");
    }

    /**
     * 输出物业缴费推送管家失败列表
     */
    public function actionPmOrderToButlerErrorLogTest()
    {
        $model = PmOrderToButlerErrorLog::find()->where(['status' => 0]);

        foreach ($model->each() as $row){
            /**
             * @var PmOrderToButlerErrorLog $row
             */
            $this->stdout("{$row->pm_order_id}  => {$row->id} \n");

        }

        $this->stdout("Running Done \n");
        exit(1);
    }

    /**
     * 停车缴费企业微信通知管家失败，重新处理通知：[$where[n], $msgType[text], $agentId[53]]
     * @param string $where
     * @param string $msgType
     * @param int $agentId
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionParkingPayOrderLog($where='n', $msgType='text', $agentId=53)
    {
        $model = ParkingPayOrderLog::find()
            ->where(['msg' => $where])
            ->orderBy('id DESC');

        foreach($model->each(100) as $row){
            /**
             * @var ParkingPayOrderLog $row
             */
            $order = ParkingOrder::findOne(['id' => $row->order_id]);
            $toUserIds = unserialize($row->response);
            if(empty($toUserIds)){
                continue;
            }

            $data = [
                'touser' => $toUserIds,
                'msgtype' => $msgType,
                'agentid' => $agentId, //中奥通讯录应用 ID
                'text' => [
                    'content' => "停车收费，\n业主【{$order->member->showName}】，\n手机【{$order->member->phone}】，\n车牌号【{$order->plate_number}】，\n实付金额【{$order->amount}】，\n商户订单号：【{$order->number}】，\n类型：【{$order->typeText}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            if(empty($res)){
                self::redis($row->pm_order_id, 'ParkingOrderPay_');
                $this->stdout("SendError：{$row->order_id} \n");
            } else {
                $this->stdout("SendSuccess: {$row->order_id} \n");
            }

            $row->delete();
        }

        $this->stdout("Parking Done \n");

        exit("Parking Exit \n");
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
        $model->ip = '-';

        $model->save();
    }

    protected static function redis($value, $key='pmOrderToButlerErrorLists')
    {
        $redis = \Yii::$app->redis;
        return $redis->rpush($key, $value);
    }

    public function actionCheckAuth()
    {
        $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQy']);
        $res = $wechatQYSDK->checkAuth(\Yii::$app->params['wechatQy']['appId'], \Yii::$app->params['wechatQy']['appSecret']);

        if(!$res){
            $this->stdout($wechatQYSDK->errMsg . "\n");
            $this->stdout($wechatQYSDK->errCode . "\n");
            exit();
        }

        var_dump($res);
    }

    /**
     * 微信电子发票模板消息：memberId,projectId,statusText
     * @author HQM
     * @param $memberId
     * @param $projectId
     * @param $statusText
     */
    public function actionElectronicInvoice($memberId=392, $projectId=236762, $statusText='')
    {
        $member = Member::findOne(['id' => $memberId]);
        $project = Project::findOne(['house_id' => $projectId]);

        //微信模板
        $res = (new WechatTemplate())->electronicInvoice($member->wechat_open_id, $project->url_key, $statusText, '-');

        var_export($res);
    }

    /**
     * 电子发票错误信息，发至邮箱：email
     * @author HQM 2018-10-08
     * @param string $email
     */
    public function actionSendErrorEmail($email='315780351@qq.com')
    {
        $subS = '获取电子发票失败，金税盘无可用发票';

        $res = (new Email())->sendToAdmin($subS, $email, $subS);
        var_dump($res);
    }

}