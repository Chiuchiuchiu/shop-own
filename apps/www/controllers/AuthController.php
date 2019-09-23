<?php
namespace apps\www\controllers;


use apps\www\models\Member;
use common\models\AuthHouseNotificationMember;
use common\models\ButlerRegion;
use common\models\House;
use common\models\HouseUnauthorized;
use common\models\MemberHouse;
use common\models\MemberHousePostLog;
use common\models\MemberHouseReview;
use common\models\MemberHouseWList;
use common\models\MemberPhoneAuthLog;
use common\models\Project;
use components\newWindow\NewWindow;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class AuthController extends Controller
{
    public function actionMobile()
    {
        $getUrl = $this->get('goUrl', '/');

        if($this->isPost){
            $phone = $this->post('phone');
            $code = $this->post('code');
            $member = Member::findOne(['phone' => $phone]);
            $insertData = [];
            $resKind = [];
            $_code = \Yii::$app->cache->get('authCode' . $phone);
            $active = \Yii::$app->params['christmas_activities'];

            if(empty($phone)){
                return $this->renderJsonFail("请填写手机号");
            }

            if($member){
                return $this->renderJsonFail("手机号已注册");
            }

            if (empty($code) ||$code != $_code['code']) {
//                if($code !== '6168'){
                    $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '-';
                    MemberPhoneAuthLog::writesLog(['phone_auth' => true, 'code' => $code], [], $ip);

                    return $this->renderJsonFail("验证码错误");
//                }
            }

            $this->user->phone = trim($phone);
            if($this->user->save()){
//                $locationUrl = !empty($getUrl) ? $getUrl : '/auth';
                $locationUrl = '/auth';

                return $this->renderJsonSuccess(['goUrl' => $locationUrl]);
            }
        }

        return $this->render('mobile',['locationUrl' => $getUrl]);
    }

    public function actionIndex($group=1,$project_id=0)
    {
        if($project_id){
            $projectList = [$project_id => Project::findOne($project_id)->house_name ?? '-'];
        }else{
            $projectId = $this->getProjectId();
            if(!$projectId){
                $projectList = $this->projectListCache();
            } else {
                $projectList = [$projectId => $this->project->house_name];
            }
        }

        if(empty($this->user->phone)){
            return $this->redirect('/auth/mobile');
        }
        if ($this->isPost) {
            $_default_count = 0;
            $_wlist = MemberHouseWList::findOne(['type'=>0]);
            if(!$_wlist){
                $_default_count = MemberHouseWList::DEFAULT_COUNT;
            }else{
                $_default_count = $_wlist->auth_count;
            }
            $res = $this->chkHouseCount($_default_count);
            if(!$res){
                return $this->renderJsonFail('您认证的房产数已达到最大限制：' . $_default_count ."，请联系管家!"); // ><<<
            }

            $houseId = $this->post('houseId');

            if(empty($houseId)){
                return $this->render('empty');
            }

            $house = House::findOne(['house_id'=>$houseId]);
            if(!$house){
                return $this->renderJsonFail("无房产信息");
            }

            if($house->level < 3){
                return $this->renderJsonFail("无子房产，暂不可提交");
            }

            $memberHouse = MemberHouse::findOne(['member_id' => $this->user->id, 'house_id' => $houseId]);
            if($memberHouse){
                return $this->renderJsonFail('您已认证过该房产，无需重复认证！');
            }

            $identity = $this->post('identity');
            if (empty($houseId) || empty($identity)) {
                return $this->renderJsonFail("身份和住址不能为空");
            }
            MemberHousePostLog::log(
                $this->user->id,
                $house?$house->project_house_id:null,
                $this->post(),
                '[点击下一步]提交认证 [house_id:'.$houseId.']'.$house->ancestor_name,
                $this->user
            );

            /** begin 简化认证流程，直接通过认证 20190802 zhaowenxi */

            $modelCount = MemberHouse::find()
                ->where(['house_id' => $houseId, 'status' =>2, 'is_first' =>1])
                ->andWhere(['<', 'updated_at', (time()-1)])
                ->count();

            $MhModel            = new MemberHouse();
            $MhModel->house_id  = $houseId;
            $MhModel->identity  = $identity;
            $MhModel->member_id = $this->user->id;
            $MhModel->group     = $house->structure->group;
            $MhModel->is_first  = $modelCount == 0 ? 1 : 2;
            $MhModel->member_id = $this->user->id;
            $MhModel->status    = MemberHouse::STATUS_ACTIVE;

            if($MhModel->save()){
                //从未认证房产删除
                HouseUnauthorized::deleteAll(['house_id' => $houseId]);

                //排除海南分公司项目
                $houseProjectId = $house->project_house_id;
                if(in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
                    return $this->renderJsonSuccess(['house_id' => $houseId]);
                }

                //begin（2017-12-24 ~ 2018-02-21）活动缴费有效期
                if($this->existsHouseAuth($houseId, $this->user->id)){
                    $url = '/activities/red-pack-activity?houseId=' . $houseId;
                    return $this->renderJsonSuccess(['house_id' => $houseId, 'goUrl' => $url]);
                }
                //end

                return $this->renderJsonSuccess(['house_id' => $houseId]);
            }

            return $this->renderJsonFail("认证错误，请联系管理员");

            /** end 简化认证流程，直接通过认证 20190802 zhaowenxi */
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionStep2($houseId, $identity)
    {
        $house = House::findOne($houseId);
        if (!$house) {
            throw new NotFoundHttpException();
        }
        $model = MemberHouse::findOne(['house_id'=>$houseId,'member_id'=>$this->user->id])??new MemberHouse();
        $model->house_id=$houseId;
        $model->identity = $identity;
        $model->member_id = $this->user->id;
        $model->group = $house->structure->group;

        $newWindow = (new NewWindow())->getHouse($houseId);
        $newWindow = isset($newWindow[0]) ? $newWindow[0] : 0;

        MemberHousePostLog::log(
            $this->user->id,
            $house->project_house_id,
            [
                'newWindow'=>$newWindow
            ],
            '[点击下一步之后，GET：]认证查询 [house_id:'.$houseId.']'.$house->ancestor_name.
            ' Phone:'.(isset($newWindow['MobilePhone'])?$newWindow['MobilePhone']:'NULL').
            ' IDNumber'.(isset($newWindow['IDNumber'])?$newWindow['IDNumber']:'NULL')
        );

        $phonesRex = preg_replace("/[[:punct:]]/i","|", $newWindow['MobilePhone']);
        $idRex = preg_replace("/[[:punct:]]/i","|", $newWindow['IDNumber']);

        $phones = explode("|", trim($phonesRex, '|'));
        $idNumbers = explode("|", trim($idRex, '|'));

        $checkPhone = $phones[0];
        $checkId = $idNumbers[0];

        //done
        if (!isset($newWindow['CustomerName']) || empty($checkPhone) || empty($checkId)) {
            //跳转审核
            return $this->redirect('/auth/house-review?houseId='.$houseId.'&identity='.$identity);
        }

        if($this->isPost){

            MemberHousePostLog::log(
                $this->user->id,
                $house->project_house_id,
                [
                    'newWindow'=>$newWindow,
                    'post'=>$this->post()
                ],
                '[点击下一步之后，POST：]认证提交 [house_id:'.$houseId.']'.$house->ancestor_name.
                ' Phone:'.(isset($newWindow['MobilePhone'])?$newWindow['MobilePhone']:'NULL').
                ' IDNumber'.(isset($newWindow['IDNumber'])?$newWindow['IDNumber']:'NULL')
            );

            $phoneSubstr = substr($checkPhone,-4);
            $idNumberSubstr = substr($checkId,-4);
            $postPhone = $this->post('phone');
            $postIdNumber = $this->post('idNumber');

            //针对港澳台身份证不作判断 zhaowenxi
            if(($phoneSubstr == $postPhone && $idNumberSubstr == $postIdNumber) || strlen($newWindow['IDNumber']) < 15){

                $memberEm = MemberHouse::findOne(['member_id' => $this->user->id, 'house_id' => $houseId]);
                if($memberEm){
                    return $this->renderJsonFail('您已认证过该房产！');
                }

                $modelCount = MemberHouse::find()
                    ->where(['house_id' => $houseId, 'status' =>2, 'is_first' =>1])
                    ->andWhere(['<', 'updated_at', (time()-1)])
                    ->count();

                $model->is_first = $modelCount == 0 ? 1 : 2;
                $model->member_id=$this->user->id;
                $model->status=MemberHouse::STATUS_ACTIVE;
                if($model->save()){
                    //从未认证房产删除
                    HouseUnauthorized::deleteAll(['house_id' => $houseId]);

                    //排除海南分公司项目
                    $houseProjectId = $house->project_house_id;
                    if(in_array($houseProjectId, [156819,220812,222949,467387,467751,501909])){
                        return $this->renderJsonSuccess(['house_id' => $houseId]);
                    }

                    //begin（2017-12-24 ~ 2018-02-21）活动缴费有效期
                    if($this->existsHouseAuth($houseId, $this->user->id)){
                        $url = '/activities/red-pack?houseId=' . $houseId;
                        return $this->renderJsonSuccess(['house_id' => $houseId, 'goUrl' => $url]);
                    }
                    //end

                    return $this->renderJsonSuccess(['house_id' => $houseId]);
                }
            }

            //$goUrl = '/auth/house-review?houseId='.$houseId.'&identity='.$identity;
            // -----feng----------0416--------
            //$goUrl = '/auth/step2?houseId='.$houseId.'&identity='.$identity;
            return $this->renderJsonFail('验证错误');
        }

        return $this->render('step2', [
            'newWindow' => $newWindow,
            'checkPhone' => $checkPhone,
            'checkId' => $checkId,
            'model'=>$model,
        ]);
    }

    public function actionSave()
    {
        if ($this->isPost) {
            $model = new MemberHouse();
            $model->load($this->post());
            $model = MemberHouse::findOrCreate($this->user->id,$model->house_id);
            if($model->status == MemberHouse::STATUS_ACTIVE){
                return $this->renderJsonFail("您已认证该房产");
            }else{
                $model->status = MemberHouse::STATUS_WAIT_REVIEW;
            }
            if ($model->save()) {
                return $this->renderJsonSuccess([]);
            }
        }
        return $this->renderJsonFail("提交信息有误");
    }

    public function actionResult($type,$houseId=null,$identity=null)
    {
        return $this->render('result', ['type' => $type,'model'=>new MemberHouse(['house_id'=>$houseId,'identity'=>$identity])]);
    }

    public function actionResult2($houseId=null)
    {
        $userId = $this->user->id;
        $memberHouse = MemberHouse::find()
            ->where(['status' => MemberHouse::STATUS_ACTIVE, 'member_id' => $userId, 'house_id' => $houseId])
            ->andWhere([
                'BETWEEN',
                'updated_at',
                strtotime(\Yii::$app->params['dragon_boat_activities']['time_start']),
                strtotime(\Yii::$app->params['dragon_boat_activities']['time_end'])
            ])
            ->one();

        $memberHouse = 'test';

        return $this->render('success', ['model' => $memberHouse, 'house_id' => $houseId]);
    }

    /**
     * @param int $id
     * @return string
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelHouse(int $id)
    {
        $memberHouse = MemberHouse::findOne([
           'member_id' => $this->user->id,
            'house_id' => $id
        ]);

        if($memberHouse){
            $memberHouse->delete();
        }

        return $this->renderJsonSuccess([]);
    }

    /**
     * 不知道为什么要加
     * @return string|\yii\web\Response
     */
    public function actionUndefined()
    {
        return $this->redirect('/');
    }

    public function actionHouseReview(int $houseId, int $identity)
    {
        $house = House::findOne(['house_id' => $houseId]);
        if(!$house){
            return $this->render('empty');
        }

        if($this->isPost){

            return $this->renderJsonFail('暂无法提交数据！');
        }

        return $this->render('review', ['houseId' => $houseId, 'identity' => $identity, 'model' => $house]);
    }

    //------------feng----0418--------
    private function  chkHouseCount($_default_count){
        $res = false;

        $res = $this->chkMemberHouseCount($_default_count);
        return $res;
    }
    /**
     * 检测业主房产认证数 是否超额
     * @author dtfeng
     * @Date: 2019/4/16
     * @Time: 10:28
     * @description
     * @param $_default_count 系统默认数
     * @return bool
     */
    private function chkMemberHouseCount($_default_count){
        $res = false;
        $memberHouseCount = MemberHouse::find()->where(['member_id' => $this->user->id, 'group' => MemberHouse::GROUP_HOUSE])->count();
        if(!$memberHouseCount){
            $memberHouseCount = 0;
        }
        if($memberHouseCount >= $_default_count){
            // 读白名单
            $_white_po = MemberHouseWList::findOne(['member_id'=>$this->user->id]);
            if($_white_po){
                $_auth_count = $_white_po->auth_count;
                if($_auth_count > $memberHouseCount){
                    $res = true;
                }
            }
        }else{
            $res = true;
        }
        return $res;
    }


    /**
     * 异步检测房产认证数
     * @author dtfeng
     * @Date: 2019/4/18
     * @Time: 11:27
     * @description
     * @return string
     */
    public function actionAjaxHouseCount(){
        $_default_count = 0;
        $_wlist = MemberHouseWList::findOne(['type'=>0]);
        if(!$_wlist){
            $_default_count = MemberHouseWList::DEFAULT_COUNT;
        }else{
            $_default_count = $_wlist->auth_count;
        }
        $res = $this->chkMemberHouseCount($_default_count);
        if($res){
            return $this->renderJsonSuccess(array("code" => 1, 'message' => '访问成功'));
        }else{
            return $this->renderJsonSuccess(array("code" => 0, 'message' => '您认证的房产数已达到最大限制：' . $_default_count ."，请联系管家!"));
        }
    }

    //------------end-----------

    public function actionHouseReviewSave()
    {
        if($this->isPost){
            $customerName = trim($this->post('customer-name', null));
            $houseId = $this->post('houseId');
            $identity = $this->post('identity', 2);

            $projectHouseId = $this->getProjectId();

            $_wlist = MemberHouseWList::findOne(['type'=>0]);
            $_default_count = 0;
            if(!$_wlist){
                $_default_count = MemberHouseWList::DEFAULT_COUNT;
            }else{
                $_default_count = $_wlist->auth_count;
            }
            $res = $this->chkMemberHouseCount($_default_count);
            if(!$res){
                return $this->renderJsonFail('您认证的房产数已达到最大限制：' . $_default_count);
            }

//            $memberHouseCount = MemberHouse::find()->where(['member_id' => $this->user->id])->count();
//            if($memberHouseCount > 4){
//
//                return $this->renderJsonFail('您认证的房产数已达到最大限制：5');
//            }

            $house = House::findOne(['house_id' => $houseId]);
            if(!$house){
                return $this->renderJsonFail('无该房产数据！');
            }

            $MemberHouseReview = MemberHouseReview::findOne(['member_id' => $this->user->id, 'house_id' => $houseId]);
            if($MemberHouseReview){
                $existsMemberHouse = MemberHouse::findOne(['member_id' => $this->user->id, 'house_id' => $houseId]);
                if($existsMemberHouse){
                    return $this->renderJsonFail('您已提交审核，无需重复提交');
                }

                $MemberHouseReview->delete();
            }

            $model = MemberHouseReview::createOrUpdate($this->user->id, $houseId);
            $model->identity = $identity;
            $model->customer_name = $customerName;
            $model->group = $house->structure->group;

            if($model->save()){
                $memberHouse = MemberHouse::findOrCreate($model->member_id, $model->house_id);
                $memberHouse->group = $model->group;
                $memberHouse->identity = $model->identity;
                if($memberHouse->save()){
                    //下面是短信通知
                    $this->notifyButler($model->house_id);

                    return $this->renderJsonSuccess(['goUrl' => '/house']);
                }
            }
        }

        return $this->renderJsonFail('暂无法提交数据！');
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
     * 有效期（2017-12-24 ~ 2018-02-21）
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
