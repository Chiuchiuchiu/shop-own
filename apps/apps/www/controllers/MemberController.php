<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/15 11:16
 * Description:
 */

namespace apps\www\controllers;


use apps\api\models\House;
use apps\www\models\Member;
use common\models\AuthHouseNotificationMember;
use common\models\HouseUnauthorized;
use common\models\MemberHouse;
use common\models\MemberPhoneAuthLog;
use common\models\SysSwitch;
use components\newWindow\NewWindow;
use components\zsy\ZSYSDK;
use yii\helpers\ArrayHelper;

class MemberController extends Controller
{
    public function actionIndex()
    {
        $zsyData=[];
        if (SysSwitch::getValue('zsyModule') && !YII_DEBUG) {
            $zsy = new ZSYSDK();
            $zsyData = $zsy->generalGet(crc32(md5($this->user->id . 'zsy-cdj-good')));
        }
        return $this->render('index', [
            'zsy' => $zsyData
        ]);
    }

    public function actionManage()
    {

        return $this->render('manage');
    }

    public function actionSearchHouses()
    {
        if(empty($this->user->phone)){
            return $this->redirect('/auth/mobile');
        }

        $memberPhone = $this->user->phone;
        $memberHouse = MemberHouse::find()
            ->select('house_id')
            ->where(['member_id' => $this->user->id])
            ->asArray()->all();
        $memberHouse = ArrayHelper::getColumn($memberHouse, 'house_id');

        $houseList = [];
        $houseIds = [];
        $number = 0;
        $customerName = '';
        if(isset($this->project->house_id)){
            $memberHouseInfo = (new NewWindow())->getCustomerHouseInfo($this->project->house_id, $memberPhone);
            if(!empty($memberHouseInfo['Response']['Data']['Record'])){
                $customerName = $memberHouseInfo['Response']['Data']['Record'][0]['CustomerName'];
                foreach($memberHouseInfo['Response']['Data']['Record'] as $key => $val){
                    if(in_array($val['ResKind'], [5, 9])){
                        $exists = '-';
                        if(in_array($val['HouseID'], $memberHouse)) {
                            $exists = '已存在';
                        } else {
                            $number += 1;
                            switch ($val['ResKind']){
                                case 5:
                                    $group = MemberHouse::GROUP_HOUSE;
                                    break;
                                default:
                                    $group = MemberHouse::GROUP_PARKING;
                                    break;
                            }

                            $houseIds[$key] = $val['HouseID'].'-'.$group;
                        }
                        $houseList[] = [
                            'houseName' => $val['HouseName'],
                            'ex' => $exists,
                        ];
                    }
                }
            }
        }

        $houseIds = implode(',', $houseIds);
        return $this->render('search-houses', [
            'houseList' => $houseList,
            'houseIds' => $houseIds,
            'number' => $number,
            'customerName' => $customerName,
            'memberPhone' => $memberPhone,
        ]);
    }

    /**
     * 批量保存业主房产
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionSaveHouses()
    {
        if($this->isPost){
            $houseIds = $this->post('houseIds');
            $memberName = $this->post('memberName');
            $identity = $this->post('identity', 1);
            if(empty($houseIds) || empty($memberName)){
                return $this->renderJsonFail('无新房产可添加！');
            }

            $houseIds = explode(',', $houseIds);
            $insertData = [];
            $createTime = time();
            foreach($houseIds as $key => $value){
                $tempHouse = explode('-', $value);
                $insertData[$key] = [
                    'member_id' => $this->user->id,
                    'house_id' => $tempHouse[0],
                    'group' => $tempHouse[1],
                    'identity' => $identity,
                    'is_first' => 1,
                    'status' => MemberHouse::STATUS_ACTIVE,
                    'created_at' => $createTime,
                    'updated_at' => $createTime,
                ];

                $this->existsHouseAuth($tempHouse[0], $this->user->id);

                //从未认证房产删除
                HouseUnauthorized::deleteAll(['house_id' => $tempHouse[0]]);
            }

            if($insertData){
                \Yii::$app->db->createCommand()->batchInsert('member_house', ['member_id', 'house_id', 'group', 'identity', 'is_first', 'status', 'created_at', 'updated_at'], $insertData)->execute();
                unset($insertData);
            }

            //保存用户名
            if(empty($this->user->name)){
                $this->user->name = $memberName;
                $this->user->save();
            }

            return $this->renderJsonSuccess(['goUrl' => '/house/']);
        }

        return $this->renderJsonFail('提交房产失败');
    }

    public function actionMobile()
    {
        $model = Member::findOne(['id' => $this->user->id]);
        return $this->render('mobile', ['model' => $model]);
    }

    public function actionChangeMobile()
    {
        if($this->isPost){
            $phone = $this->post('new-phone');
            $code = $this->post('code');
            $_code = \Yii::$app->cache->get('verifyPhone_' . $phone);
            if(empty($phone)){
                return $this->renderJsonFail('请填写手机号');
            }

            if (empty($code) || $code != $_code['code']) {

                $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '-';
                MemberPhoneAuthLog::writesLog(['phone_auth' => true, 'code' => $code], ['mes' => '更换绑定手机号'], $ip);

                return $this->renderJsonFail("验证码错误");
            }

            $this->user->phone = $phone;
            if($this->user->save()){
                return $this->renderJsonSuccess(['goUrl' => '/member']);
            }
        }

        return $this->render('change-mobile');
    }

    public function actionVerifyPhone()
    {
        if($this->isPost){
            $phone = $this->post('phone');
            $code = $this->post('code');

            if (empty($phone) || $phone != $this->user->phone) {

                $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '-';
                $mes = ['mes' => '绑定新手机前，原手机号'];
                MemberPhoneAuthLog::writesLog(['phone_auth' => true, 'phone' => $phone], $mes, $ip);

                return $this->renderJsonFail("无法配对原手机号");
            }

            if($this->user->save()){
                return $this->renderJsonSuccess(['goUrl' => '/member/change-mobile']);
            }
        }

        return $this->render('verify-phone');
    }

    public function actionAuthCode()
    {
        $phone = $this->get('phone', null);
        $newPhone = $this->get('new-phone', false);
        $code = false && YII_ENV == YII_ENV_DEV ? 1111 : rand(1000, 9999);
        $codePhone = '';

        if(!empty($newPhone)){
            if($newPhone == $this->user->phone){
                return $this->renderJsonFail('请使用新手机号');
            }
            $codePhone = $newPhone;
        } else {
            $codePhone = $this->user->phone;
        }

        \Yii::$app->cache->set('verifyPhone_' . $codePhone,
            [
                'code' => $code,
                'time' => time(),
            ]
            , 60 * 10);
        if (false && YII_ENV == YII_ENV_DEV) {
            $resp['result']['err_code']=0;
        } else {
            require(\Yii::getAlias('@components/alidayuSDK/TopSdk.php'));
            $c = new \TopClient(\Yii::$app->params['alidayu.app'], \Yii::$app->params['alidayu.secret']);
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("财到家");
            $req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"财到家\"}");
            $req->setRecNum($codePhone);
            $req->setSmsTemplateCode("SMS_25460346");
            $resp = $c->execute($req);
            $resp = json_encode($resp);
            $resp = json_decode($resp, true);
        }

        if (isset($resp['result']['err_code']) && $resp['result']['err_code'] == 0)
            return $this->renderJsonSuccess([]);
        else {
            return $this->renderJsonFail($resp['msg']);
        }
    }

    /**
     * 有效期（2017-12-24 ~ ）
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