<?php
namespace apps\api\controllers;

use apps\api\models\House;
use apps\api\models\Member;
use apps\api\models\MemberHouse;
use common\models\AuthHouseNotificationMember;
use common\models\ButlerRegion;
use common\models\HouseUnauthorized;
use common\models\MemberPhoneAuthLog;
use yii\helpers\ArrayHelper;

class AuthController extends Controller
{
    public $modelClass = 'apps\api\models\Member';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    /**
     * 更换手机号
     * @author zhaowenxi
     */
    public function actionChangePhone(){

        $phone = $this->post('phone');

        $code = $this->post('code');

        $_code = \Yii::$app->cache->get('verifyPhone_' . $phone);

        $match = preg_match("/^1[345678]\d{9}$/", $phone);
        if(!$match){
            return $this->renderJsonFail(50001);
        }

        if (empty($code) || $code != $_code['code']) {

            $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '-';

            MemberPhoneAuthLog::writesLog(['phone_auth' => true, 'code' => $code], ['mes' => '更换绑定手机号'], $ip);

            return $this->renderJsonFail(50006);
        }

        $member = Member::findOne($this->userId);

        $member->phone = $phone;

        if($member->save()){

            //重置redis
            $redis = \Yii::$app->redis;
            $redis->select(1);

            $redis->del($this->accessToken);

            //写入redis
            $userJson = json_encode([
                'id' => $member->id,
                'headImg' => $member->headimg,
                'name' => $member->nickname,
                'phone' => $phone,
                'projectId' => $this->projectId
            ]);

            $redis->set($this->accessToken, $userJson);

            $redis->expire($this->accessToken, 7200);

            return $this->renderJsonSuccess();
        }

        return $this->renderJsonFail(41001);
    }

    /**
     * 一次认证多房产
     * @author HQM 2018/11/16
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionHouse()
    {
        $customerName = $this->post('customerName');
        $houseIds = $this->post('houseIds');
        $identity = $this->post('identity', 1);
        if(empty($houseIds) || empty($customerName)){
            return $this->renderJsonFail(41001);
        }

        $houseIds = explode(',', $houseIds);
        $insertData = [];
        $createTime = time();
        foreach($houseIds as $key => $value){
            $tempHouse = explode('-', $value);
            $insertData[$key] = [
                'member_id' => $this->userId,
                'house_id' => $tempHouse[0],
                'group' => $tempHouse[1],
                'identity' => $identity,
                'is_first' => 1,
                'status' => MemberHouse::STATUS_ACTIVE,
                'created_at' => $createTime,
                'updated_at' => $createTime,
            ];

            $this->existsHouseAuth($tempHouse[0], $this->userId);

            //从未认证房产删除
            HouseUnauthorized::deleteAll(['house_id' => $tempHouse[0]]);
        }

        if($insertData){
            \Yii::$app->db->createCommand()->batchInsert('member_house', ['member_id', 'house_id', 'group', 'identity', 'is_first', 'status', 'created_at', 'updated_at'], $insertData)->execute();
            unset($insertData);
        }

        $member = Member::findOne(['id' => $this->userId]);
        //保存用户名
        if(empty($member->name)){
            $member->name = $customerName;
            $member->save();
        }

        return $this->renderJsonSuccess();
    }

    /**
     * 短信通知相关管家
     * @param $houseId
     */
    private function notifyButler($houseId)
    {
        require(\Yii::getAlias('@components/alidayuSDK/TopSdk.php'));

        $houseModel = House::findOne(['house_id' => $houseId]);
        $projectName = $houseModel->project->house_name;
        foreach (ButlerRegion::find()->where(['house_id' => $houseId])->each() as $row) {
            /* @var $row ButlerRegion */
            $co = 0;
            $lists = [];

            $butlerPhone = isset($row->butlerAuth->account) ? $row->butlerAuth->account : null;
            $butlerStatus = isset($row->butlerAuth->butler->status) ? $row->butlerAuth->butler->status : null;

            if (!empty($butlerPhone) && isset($butlerStatus)) {
                if($butlerStatus == 1){
                    $memberHouse = MemberHouse::find()
                        ->select('house_id, member_id')
                        ->where([
                            'status' => MemberHouse::STATUS_WAIT_REVIEW,
                            'house_id' => ButlerRegion::find()->select('house_id')->where(['butler_id' => $row->butler_id])
                        ])->distinct()->orderBy('created_at DESC')->asArray()->all();

                    $memberHouseIds = ArrayHelper::getColumn($memberHouse, 'house_id');
                    $memberIds = ArrayHelper::getColumn($memberHouse, 'member_id');

                    $memberIds = array_unique($memberIds);

                    $co = MemberHouse::find()
                        ->where([
                            'status' => MemberHouse::STATUS_WAIT_REVIEW,
                            'house_id' => $memberHouseIds
                        ])
                        ->count();

                    foreach ($memberIds as $mId){
                        $memberModel = Member::findOne(['id' => $mId]);
                        $lists[] = $memberModel->showName;
                    }
                    $lists = implode(',', $lists);
                    $lists = mb_substr($lists, 0, 14) . '...';

                    $c = new \TopClient(\Yii::$app->params['alidayu.app'], \Yii::$app->params['alidayu.secret']);
                    $req = new \AlibabaAliqinFcSmsNumSendRequest;
                    $req->setSmsType("normal");
                    $req->setSmsFreeSignName("财到家");
                    $req->setSmsParam("{\"pro\":\"{$projectName}\",\"co\":\"{$co}\",\"list3\":\"{$lists}\"}");
                    $req->setRecNum($butlerPhone);
                    $req->setSmsTemplateCode("SMS_68035079");
                    $c->execute($req);
                }
            }
        }
    }

    /**
     * 检查房产在活动期内是否已有认证，有效期（2017-12-24 ~ ）
     * @author HQM
     * @param $houseId
     * @param $memberId
     * @return bool
     */
    private function existsHouseAuth($houseId, $memberId)
    {
        $authActivities = \Yii::$app->params['christmas_activities'];

        if(time() > $authActivities['endTime']){
            return false;
        }

        //排除海南分公司项目
        $houseInfo = House::find()->select('project_house_id')->where(['house_id' => $houseId])->asArray()->one();
        $projectHouseId = isset($houseInfo['project_house_id']) ? $houseInfo['project_house_id'] : 0;
        if(in_array($projectHouseId, [156819,220812,222949,467387,467751,501909])){
            return false;
        }

        $house = MemberHouse::find()
            ->where(['house_id' => $houseId, 'status' => MemberHouse::STATUS_ACTIVE])
            ->andWhere(['<', 'updated_at', $authActivities['startTime']])
            ->asArray()
            ->all();
        if($house){
            return false;
        }

        $authHouseToMember = AuthHouseNotificationMember::findOrCreate($memberId, $houseId);
        return $authHouseToMember->save();
    }

}
