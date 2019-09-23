<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use apps\api\models\Project;
use apps\api\models\House;
use apps\api\models\Member;
use apps\www\models\WechatRedPackLog;
use common\models\AuthHouseNotificationMember;
use common\models\MemberHouse;
use common\models\MemberPromotionCode;
use common\models\WechatRedPack;
use common\models\ThirdpartyViewHistory;

class MemberController extends Controller
{
    public $modelClass = 'apps\api\models\Member';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['save']);

        return $actions;
    }

    /**
     * 业主中心
     * @author zhaowenxi
     */
    public function actionIndex(){

        $res = ['member' => [], 'houses' => [], 'cars' => []];

        $res['member'] = [
            'img' => $this->headImg,
            'nickname' => $this->nickname,
        ];

        $memberHouse = MemberHouse::find()
            ->select("h.ancestor_name,h.house_id,h.project_house_id,member_house.identity,member_house.group,member_house.status")
            ->where(['member_id' => $this->userId, 'status' => MemberHouse::STATUS_ACTIVE])
            ->leftJoin("`house` AS h ON `h`.`house_id` = `member_house`.`house_id`")
            ->orderBy("created_at DESC")->asArray()->all();

        $h = $c = 0;

        if($memberHouse){
            foreach ($memberHouse as $v){

                switch ($v['group']){
                    case 1: //房子
                        $res['houses'][$h] = [
                            'id' => $v['house_id'],
                            'identityText' => MemberHouse::identityMap()[$v['identity']],
                            'identity' => $v['identity'],
                            'logo' => \Yii::getAlias(Project::findOne($v['project_house_id'])->logo),
                            'showName' => str_replace('->', '', $v['ancestor_name']),
                            'projectId' => $v['project_house_id'],
                            'ancestorName' => $v['ancestor_name'],
                            'group' => $v['group'],
                            'status' => $v['status'],
                        ];
                        $h++;
                        break;
                    case 2: //车位
                        $res['cars'][$c] = [
                            'id' => $v['house_id'],
                            'identityText' => MemberHouse::identityMap()[$v['identity']],
                            'identity' => $v['identity'],
                            'logo' => \Yii::getAlias(Project::findOne($v['project_house_id'])->logo),
                            'showName' => str_replace('->', '', $v['ancestor_name']),
                            'ancestorName' => $v['ancestor_name'],
                            'group' => $v['group'],
                            'status' => $v['status'],
                        ];
                        $c++;
                        break;
                }

            }
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 房产管理列表
     * @author HQM
     */
    public function actionHouse()
    {
        $group = $this->get('group', 0);
        if(!is_numeric($group)){
            return $this->renderJsonFail(40010, $group);
        } else {
            $group = $group > 0 ? $group : null;
        }

        $res = [];
        $memberHouse = MemberHouse::find()
            ->select("h.ancestor_name,h.house_id,h.project_house_id,member_house.identity,member_house.group")
            ->where(['member_house.member_id' => $this->userId, 'member_house.status' => MemberHouse::STATUS_ACTIVE])
            ->andFilterWhere(['member_house.group' => $group])
            ->leftJoin("`house` AS h ON `h`.`house_id` = `member_house`.`house_id`")
            ->orderBy("created_at DESC")->asArray()->all();

        if($memberHouse){
            foreach ($memberHouse as &$v){
                $res[] = [
                    'id' => $v['house_id'],
                    'logo' => \Yii::getAlias(Project::findOne($v['project_house_id'])->logo),
                    'showName' => str_replace('->', '', $v['ancestor_name']),
                    'ancestorName' => $v['ancestor_name'],
                    'houseInfo' => $v['house_id'] . '-' . $v['group'] . '-' . $v['project_house_id'],
                ];
            }
        }

        $response = [
            'list' => $res,
            'chargeItemProject' => ["468497", "117847", '230975']
        ];

        return $this->renderJsonSuccess(200, $response);
    }

    /**
     * 获取用户详细信息
     * @author zhaowenxi
     */
    public function actionDetail(){

        $member = Member::findOne($this->userId);

        $res = [
            'nickname' => $member->nickname ?? '',
            'headImg' => $member->headimg,
            'name' => $member->name ?? '',
            'phone' => $member->phone,
        ];

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 家庭成员
     * @author zhaowenxi
     */
    public function actionFamily(){

        $res = ['houses' => []];

        //只有业主才能查看家庭成员
        $MemberHouse = MemberHouse::find()
            ->select("h.ancestor_name,h.house_id,h.project_house_id")
            ->where(['member_id' => $this->userId, 'status' => MemberHouse::STATUS_ACTIVE, 'identity' => MemberHouse::IDENTITY_OWNER])
            ->leftJoin("`house` AS h ON `h`.`house_id` = `member_house`.`house_id`")
            ->orderBy("created_at DESC")->asArray()->all();

        $houseIds = [];

        if($MemberHouse){

            foreach ($MemberHouse as &$v){
                //查找家庭成员
                $houseIds[] = $v['house_id'];
                //查找项目logo
                $res['houses'][] = [
                    'logo' => \Yii::getAlias(Project::findOne($v['project_house_id'])->logo),
                    'showName' => str_replace('->', '', $v['ancestor_name']),
                    'ancestorName' => $v['ancestor_name'],
                    'houseId' => $v['house_id'],
                    'family' => [],
                ];

            }

            $family = MemberHouse::find()
                ->select('member_house.house_id,m.nickname,,m.phone,member_house.identity,m.id')
                ->where(['status' => MemberHouse::STATUS_ACTIVE])
                ->andFilterWhere(['house_id' => $houseIds])
                ->leftJoin("`member` AS m ON `m`.`id` = `member_house`.`member_id`")->asArray()->all();

            if($family){
                foreach ($res['houses'] as &$val){
                    foreach ($family as $fval){
                        $fval['identityText'] = MemberHouse::identityMap()[$fval['identity']];

                        unset($fval['id']);

                        if($fval['house_id'] == $val['houseId']){
                            unset($fval['house_id']);
                            $val['family'][] = $fval;
                        }
                    }
                    unset($val['houseId']);
                }
            }

        }

        return $this->renderJsonSuccess(200, $res);
    }

    public function actionCoupon(){

        $res = ['date' => [], 'list' => []];

        $authActivities = \Yii::$app->params['christmas_activities'];

        //认证房产红包最大使用时间
        $cDate = date('Y-m-d', $authActivities['startTime']). ' ~ ';
        $cDate .= date('Y-m-d', $authActivities['allowedMaxTime']);
        $res['cDate'] = $cDate;

        $memberPromotionCode = MemberPromotionCode::find()
            ->where(['member_id' => $this->userId, 'promotion_name' => 'auth'])
            ->asArray()->all();

        foreach ($memberPromotionCode as $v){
            $res['list'][] = [
                'amount' => $v['amount'],
                'status' => $v['status'],
                'showName' => House::findOne($v['house_id'])->showName,
            ];
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 红包
     * @author HQM 2018/11/21
     * @return string
     */
    public function actionAuthRedpack()
    {
        //begin 活动缴费红包活动（2017-12-24 ~ 2018-02-21）
        $houseId = false;
        $authActivities = \Yii::$app->params['christmas_activities'];
        if (time() <= $authActivities['endTime']) {
            $memberAuthRedPack = AuthHouseNotificationMember::findOne(['member_id' => $this->userId, 'status' => 0]);
            $houseId = $memberAuthRedPack ? $memberAuthRedPack->house_id : false;
        }
        $res['houseId'] = $houseId;
        $res['projectId'] = $this->projectId;
        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 第三方跳转记录
     * @author dtfeng
     * @Date: 2019/4/29
     * @Time: 14:46
     * @description
     */
    public function actionThirdPartyViewHistory(){
        $_type    = $this->post('type');
        $_modelv  = $this->post('modelv');
        $_houseId = $this->post('houseId');

        $HOUSE = House::findOne(['house_id' => $_houseId]);

        if ($HOUSE) {
            $_projectId = $HOUSE['project_house_id'];
        } else {
            $_projectId = 0;
        }

        $model             = new ThirdpartyViewHistory();
        $model->member_id  = $this->userId;
        $model->house_id   = $_houseId;
        $model->project_id = $_projectId;
        $model->type       = $_type;
        $model->model      = $_modelv;
        $model->status     = ThirdpartyViewHistory::STATUS_FAVORITES;
        $model->created_at = time();

        $res = $model->save();
        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 2017-12-24 ~ 2018-12-31 房产认证领红包
     * @author HQM 2018/11/22
     * @param $houseId
     * @return string
     */
    public function actionHandOutEnvelopes($houseId=null)
    {
        $eveName = '201812';

        $authActivities = \Yii::$app->params['christmas_activities'];

        if(time() > $authActivities['endTime']){
            return $this->renderJsonFail(41010);
        }

        if(empty($houseId)){
            return $this->renderJsonFail(41011);
        }

        $memberAuthHouseNotify = AuthHouseNotificationMember::find()
            ->where(['member_id' => $this->userId, 'status' => AuthHouseNotificationMember::STATUS_DEFAULT])
            ->andWhere(['house_id' => $houseId])
            ->asArray()
            ->all();

        if(empty($memberAuthHouseNotify)){
            return $this->renderJsonFail(41007);
        }

        $house = House::findOne(['house_id' => $houseId]);
        $houseProjectId = $house->project_house_id;
        $isMemberHouse = AuthHouseNotificationMember::findOne(['member_id' => $this->userId, 'status' => AuthHouseNotificationMember::STATUS_DEFAULT, 'house_id' => $houseId]);
        if(empty($isMemberHouse)){
            return $this->renderJsonFail(41007);
        }

        //排除海南分公司
        if(in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
            return $this->renderJsonFail(41007);
        }

        $houseRedPack = WechatRedPack::findOne(['house_id' => $houseId, 'even_name' => $eveName]);
        if($houseRedPack){
            $isMemberHouse->status = AuthHouseNotificationMember::STATUS_ACTIVE;
            $isMemberHouse->save();

            return $this->renderJsonFail(41008);
        }

        $redPackAmount = $this->buildRedPackArray();
        if(!$redPackAmount){
            $isMemberHouse->status = AuthHouseNotificationMember::STATUS_ACTIVE;
            $isMemberHouse->save();

            return $this->renderJsonFail(41009);
        }

        $redPackNumber = WechatRedPack::createNumber();

        $memberPromotion = new MemberPromotionCode();
        $memberPromotion->member_id = $this->userId;
        $memberPromotion->promotion_name = 'auth';
        $memberPromotion->amount = $redPackAmount;
        $memberPromotion->promotion_code = $houseId;
        $memberPromotion->house_id = $houseId;

        if($memberPromotion->save()){
            $wechatRedPackModel = new WechatRedPack();
            $wechatRedPackModel->number = $redPackNumber;
            $wechatRedPackModel->member_id = $this->userId;
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

                return $this->renderJsonSuccess(200, ['cash' => $redPackAmount, 'message' => '红包已存入“我的优惠券”']);
            }
        }

        $this->writeWechatRedLog($this->userId, $redPackAmount, $redPackNumber, $memberPromotion->getErrors());

        return $this->renderJsonFail(41009);
    }

    private function buildRedPackArray()
    {
        $redis = \Yii::$app->redis;
        $redis->select(0);

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