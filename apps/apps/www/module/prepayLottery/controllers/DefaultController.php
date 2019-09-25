<?php
namespace apps\www\module\prepayLottery\controllers;

use apps\mgt\models\FpzzLog;
use apps\www\controllers\Controller;
use apps\www\models\WechatRedPackLog;
use apps\www\module\prepayLottery\assets\AppAsset;
use apps\www\module\prepayLottery\models\PrepayLotteryGift;
use apps\www\module\prepayLottery\models\PrepayLotteryResult;
use apps\www\module\prepayLottery\models\ProjectRedEnvelope;
use apps\www\service\RedPackService;
use common\models\AuthHouseNotificationMember;
use common\models\House;
use common\models\MemberHouse;
use common\models\MemberPromotionCode;
use common\models\PmOrder;
use common\models\PrepayPmOrder;
use common\models\SysSwitch;
use common\models\WechatRedPack;
use components\wechatSDK\WechatSDK;
use dosamigos\qrcode\QrCode;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;


/**
 * Created by
 * Author: zhao
 * Description:
 */
class DefaultController extends Controller
{
    public $layout = 'main';

    public function actionIndex($projectId)
    {
        $times = $this->getTimes($projectId);
        return $this->render('index', ['times' => $times,'projectId'=>$projectId]);
    }

    private function getOrders($projectId)
    {
        return PmOrder::find()
            ->joinWith('house')
            ->where([
                'house.project_house_id'=>$projectId,
                'member_id' => $this->user->id,
                'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]
            ])
            ->andWhere(['>=','payed_at',1484496000])
            ->all();
    }

    private function getTimes($projectId)
    {
        /**
         * @var $order PrepayPmOrder
         */
        $orders = $this->getOrders($projectId);
        $num = 0;

        $res = 0;
        $timesMap = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        if ($orders) {
            foreach ($orders as $row) {
                foreach($row->items as $item)
                    if($item->charge_item_id==1)
                        $num++;
            }
            foreach ($timesMap as $key => $v)
                if ($num >= $key)
                    $res = $v;
        }
        $res -= PrepayLotteryResult::find()
            ->where(['member_id' => $this->user->id])
            ->andWhere(['>','created_at',1484496000])
            ->count();
        return $res;
    }

    public function actionGift($projectId)
    {
        if ($this->getTimes($projectId) < 1) {
            return $this->renderJsonFail("抽奖机会已经用完");
        }
        return PrepayLotteryGift::getDb()->transaction(function($db) use ($projectId) {


            $giftRs = PrepayLotteryGift::find()
                ->where(['even_name'=>'2017','project_house_id'=>$projectId])
                ->asArray()
                ->all();
//            $_all=[];
//            foreach($all as $row){
//                $_all[$row['id']]=$row;
//            }
//            if($_all[1]['stock']+$_all[2]['stock']<500)
//                $ids[]=3;
//            if($_all[1]['stock']+$_all[2]['stock']+$_all[3]['stock']<500)
//                $ids[]=4;
//            if($_all[1]['stock']+$_all[2]['stock']+$_all[3]['stock']+$_all[4]['stock']<250)
//                $ids[]=5;
//            if($_all[1]['stock']+$_all[2]['stock']+$_all[3]['stock']+$_all[4]['stock']+$_all[5]['stock']<100)
//                $ids[]=6;
//            $giftRs = PrepayLotteryGift::find()->where(['id'=>$ids])->orderBy('id ASC')->asArray()->all();
//            unset($_all);
            $gifts = [];
            foreach ($giftRs as $row) {
                $gifts[$row['id']] = $row;
            }
            ksort($gifts);
            $rand_array=[];
            $i=0;
            foreach($gifts as $_gift){
                $rand_array = array_merge($rand_array,array_fill($i,$_gift['stock'],$_gift['id']));
            }

            if (count($rand_array) == 0) {
                $result = new PrepayLotteryResult();
                $result->member_id = $this->user->id;
                $result->gift_id = 11;
                $result->even_name = '2017';
                $result->project_house_id = $projectId;
                if ($result->save()) {
                    return $this->renderJsonSuccess([
                        'giftId' => 1,
                        'id' => $result->id
                    ]);
                }
            } else {
                $rand = array_rand($rand_array);
                $showGiftId = $rand_array[$rand];
                $model = PrepayLotteryGift::findOne($showGiftId);
                $model->stock--;
                if($model->stock<0){
                    $result = new PrepayLotteryResult();
                    $result->gift_id = 1;
                    $result->even_name = '2017';
                    $result->project_house_id = $projectId;
                    if ($result->save()) {
                        return $this->renderJsonSuccess([
                            'giftId' => $model->git_key,//纸巾
                            'id' => $result->id
                        ]);
                    }
                }
                if ($model->save()) {
                    $result = new PrepayLotteryResult();
                    $result->member_id = $this->user->id;;
                    $result->even_name = '2017';
                    $result->project_house_id = $projectId;
                    $result->gift_id = $model->id;
                    if ($result->save()) {
                        if($model->name=='微信红包'){
                            RedPackService::sendBy2017($result->id,$result->member_id);
                        }
                        return $this->renderJsonSuccess([
                            'giftId' => $model->git_key,
                            'id' => $result->id
                        ]);
                    }
                }
            }
            return $this->renderJsonFail("获取奖品失败");
        }, Transaction::SERIALIZABLE);
    }

    public function actionMyGift($id)
    {
        /**
         * @var PrepayLotteryResult $model
         */
        $model = PrepayLotteryResult::find()->where(['member_id' => $this->user->id, 'id' => $id])->one();
        return $this->renderJsonSuccess([
            'qrcodeId' => $model->id,
            'name' => $model->gift->name,
            'id' => $model->gift->id,
            'bg' => \Yii::getAlias($model->gift->icon),
            'gave_at' => $model->gave_at
        ]);
    }

    public function actionMyGiftList()
    {
        $rs = PrepayLotteryResult::find()
            ->where(['member_id' => $this->user->id])
            ->andWhere(['!=', 'gift_id', 1])
            ->with('gift')
            ->all();
        $list = [];
        foreach ($rs as $row) {
            $list[] = [
                'giftId' => $row->gift->id,
                'id' => $row->id,
                'bg'=>\Yii::getAlias($row->gift->icon),
                'name' => $row->gift->name,
                'gave_at' => $row->gave_at
            ];
        }
        return $this->renderJsonSuccess([
            'list' => $list
        ]);
    }

    public function actionQrcode($id)
    {
        $url = \Yii::$app->request->hostInfo . '/prepay-lottery/cash/?id=' . $id . '&memberId=' . $this->user->id;
        QrCode::png($url);
        die();
    }

    /**
     * @param integer $projectId
     * @param integer $pmOrderId
     * @param string $eveName
     * @return string
     */
    public function actionHandOutEnvelopes(int $projectId, int $pmOrderId, string $eveName='20170810')
    {
        return $this->renderJsonFail('红包被抢光了！');

        $projectReadEnvelopeModel = ProjectRedEnvelope::findOne(['project_house_id' => $projectId]);
        $projectReadEnvelopeModel->stock--;

        if($projectReadEnvelopeModel->stock < 0){
            return $this->renderJsonFail('红包被抢光了！');
        }

        $pmOrderModel = PmOrder::findOne(['member_id' => $this->user->id, 'id' => $pmOrderId, 'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]]);
        if(!$pmOrderModel){
            return $this->renderJsonFail('无法领取红包');
        }

        $wechatRedPackModel = WechatRedPack::findOne(['even_name' => $eveName, 'house_id' => $pmOrderModel->house_id]);
        if($wechatRedPackModel){
            return $this->renderJsonFail('红包已被领取过了！');
        }
        $wechatRedPackModel = null;

        $redPackNumber = WechatRedPack::createNumber();
        $res = WechatSDK::sendRedPack($pmOrderModel->member->wechat_open_id, $redPackNumber, 1,1,'物业缴费送红包','物业缴费送红包');

        if($res['return_code'] == 'SUCCESS'){
            $wechatRedPackModel = new WechatRedPack();
            $wechatRedPackModel->number = $redPackNumber;
            $wechatRedPackModel->member_id = $pmOrderModel->member_id;
            $wechatRedPackModel->amount = 1;
            $wechatRedPackModel->pm_order_id = (string) $pmOrderId;
            $wechatRedPackModel->house_id = $pmOrderModel->house_id;
            $wechatRedPackModel->even_name = $eveName;
            $wechatRedPackModel->even_key = 1;
            $wechatRedPackModel->remark = '物业缴费送红包';
            $wechatRedPackModel->completed_at = time();
            $wechatRedPackModel->status= WechatRedPack::STATUS_SEND;
            $wechatRedPackModel->result= serialize($res);

            if($projectReadEnvelopeModel->save() && $wechatRedPackModel->save()){
                return $this->renderJsonSuccess(['cash' => 1]);
            }
        }

        $this->writeWechatRedLog($this->user->id, 1, $redPackNumber, $res);

        return $this->renderJsonFail('红包被抢光啦！！');
    }

    /**
     * 2017-12-24 ~ 2018-02-21 房产认证领红包
     * @param $houseId
     * @return string
     */
    public function actionReceiveRedEnvelope($houseId=null)
    {
        $eveName = '201812';
        $houseIds = null;

        $authActivities = \Yii::$app->params['christmas_activities'];

        if(time() > $authActivities['endTime']){
            return $this->renderJsonFail('活动已结束！');
        }

        if(empty($houseId)){
            return $this->renderJsonFail('谢谢参与！无法找到房产信息');
        }

        $memberAuthHouseNotify = AuthHouseNotificationMember::find()
            ->where(['member_id' => $this->user->id, 'status' => AuthHouseNotificationMember::STATUS_DEFAULT])
            ->andWhere(['house_id' => $houseId])
            ->asArray()
            ->all();

        if(empty($memberAuthHouseNotify)){
            return $this->renderJsonFail('谢谢参与！');
        }

        $house = House::findOne(['house_id' => $houseId]);
        $isMemberHouse = AuthHouseNotificationMember::findOne(['member_id' => $this->user->id, 'status' => AuthHouseNotificationMember::STATUS_DEFAULT, 'house_id' => $houseId]);
        if(empty($isMemberHouse)){
            return $this->renderJsonFail('谢谢参与！');
        }

        $houseRedPack = WechatRedPack::findOne(['house_id' => $houseId, 'even_name' => $eveName]);
        if($houseRedPack){
            $isMemberHouse->status = AuthHouseNotificationMember::STATUS_ACTIVE;
            $isMemberHouse->save();

            return $this->renderJsonFail('谢谢参与！房产已领取过红包');
        }

        //排除海南分公司项目
        $houseProjectId = $house->project_house_id;
        if(in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
            $isMemberHouse->status = AuthHouseNotificationMember::STATUS_ACTIVE;
            $isMemberHouse->save();

            return $this->renderJsonFail('谢谢参与！');
        }

        $redPackAmount = $this->buildRedPackArray();
        if(!$redPackAmount){
            $isMemberHouse->status = AuthHouseNotificationMember::STATUS_ACTIVE;
            $isMemberHouse->save();

            return $this->renderJsonFail('红包被抢光啦！！');
        }

        $redPackNumber = WechatRedPack::createNumber();

        $memberPromotion = new MemberPromotionCode();
        $memberPromotion->member_id = $this->user->id;
        $memberPromotion->promotion_name = 'auth';
        $memberPromotion->amount = $redPackAmount;
        $memberPromotion->promotion_code = $houseId;
        $memberPromotion->house_id = $houseId;

        if($memberPromotion->save()){
            $wechatRedPackModel = new WechatRedPack();
            $wechatRedPackModel->number = $redPackNumber;
            $wechatRedPackModel->member_id = $this->user->id;
            $wechatRedPackModel->amount = $redPackAmount;
            $wechatRedPackModel->house_id = $houseId;
            $wechatRedPackModel->project_house_id = $house->project_house_id;
            $wechatRedPackModel->pm_order_id = '1';
            $wechatRedPackModel->even_name = $eveName;
            $wechatRedPackModel->even_key = 1;
            $wechatRedPackModel->remark = '业主认证送红包';
            $wechatRedPackModel->completed_at = time();
            $wechatRedPackModel->status= WechatRedPack::STATUS_SEND;

            if($wechatRedPackModel->save()){
                $isMemberHouse->status = AuthHouseNotificationMember::STATUS_ACTIVE;
                $isMemberHouse->save();

                return $this->renderJsonSuccess(['cash' => $redPackAmount, 'message' => '红包已存入“我的优惠券”']);
            }
        }

        $this->writeWechatRedLog($this->user->id, $redPackAmount, $redPackNumber, $memberPromotion->getErrors());

        return $this->renderJsonFail('红包被抢光啦！！');
    }

    private function buildRedPackArray()
    {
        $redis = \Yii::$app->redis;

        $redArray = [];
        $tenYuanNumber = $redis->get('tenYuanNumber');  //34000
        $twentyYuanNumber = $redis->get('twentyYuanNumber');    //4000
        $fiftyYuanNumber = $redis->get('fiftyYuanNumber');  //1000
        $oneHundredYuanNumber = $redis->get('oneHundredYuanNumber');    //100
        $twoHundredYuanNumber = $redis->get('twoHundredYuanNumber');    //100

        $tenYuan = 10;
        $twentyYuan = 20;
        $fiftyYuan = 50;
        $oneHundredYuan = 100;
        $twoHundredYuan = 200;

        $arrayLength = $oneHundredYuanNumber + $twoHundredYuanNumber;
        $arrayLength = $arrayLength < 100 ? 100 : $arrayLength;

        if($twoHundredYuanNumber > 0){
            array_push($redArray, $twoHundredYuan);
        }

        if($oneHundredYuanNumber > 0){
            array_push($redArray, $oneHundredYuan);
        }

        if(count($redArray) === 1){
            switch($redArray[0]){
                case 100:
                    array_push($redArray, $oneHundredYuan);
                    break;
                case 200:
                    array_push($redArray, $twoHundredYuan);
                    break;
            }
        }

        if(empty($redArray)){
            $arrayLength = 15;
        }

        if($fiftyYuanNumber > 0){
            $fiftyPadSize = round(($fiftyYuanNumber / $tenYuanNumber) * 100);
            $fiftyPadSize = round($fiftyPadSize/0.13);
            $redArrayLength = count($redArray);

            $padSize = empty($redArray) ? 3 : $redArrayLength + $fiftyPadSize;
            $redArray = array_pad($redArray, $padSize, $fiftyYuan);
        }

        if($twentyYuanNumber > 0){
            $twentyPadSize = round(($twentyYuanNumber / $tenYuanNumber) * 100);
            $twentyPadSize = round($twentyPadSize/0.2);
            $redArrayLength = count($redArray);

            $padSize = empty($redArray) ? 5 : $redArrayLength + $twentyPadSize;
            $redArray = array_pad($redArray, $padSize, $twentyYuan);
        }


        if($tenYuanNumber > 0){
            $redArrayLength = count($redArray);
            $padSize = empty($redArray) ? 10 : $redArrayLength + 10;
            $redArray = array_pad($redArray, $padSize, $tenYuan);
        }

        $count = count($redArray);
        if(empty($count)){
            return false;
        }

        for($i=0; $i < $arrayLength - $count; $i++){
            array_push($redArray, $tenYuan);
        }
        shuffle($redArray);

        $arrayValue = $redArray[array_rand($redArray)];
        switch($arrayValue){
            case 10:
                $redis->decr('tenYuanNumber');
                break;
            case 20:
                $redis->decr('twentyYuanNumber');
                break;
            case 50:
                $redis->decr('fiftyYuanNumber');
                break;
            case 100:
                $redis->decr('oneHundredYuanNumber');
                break;
            case 200:
                $redis->decr('twoHundredYuanNumber');
                break;
        }
        $redis->incr('sendRedPackNumber');

        return $arrayValue;
    }

    /**
     * @param $memberId
     * @param $amount
     * @param $number
     * @param $result
     * @return bool
     */
    protected function writeWechatRedLog($memberId, $amount, $number, $result)
    {
        return WechatRedPackLog::writeLog($memberId, $amount, $number, $result);
    }

}
