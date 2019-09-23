<?php
/**
 * Created by
 * Author: zhao
 * Time: 2017/1/6 16:54
 * Description:
 */

namespace apps\business\controllers;


use apps\business\models\Member;
use common\models\AuthHouseNotificationMember;
use common\models\House;
use common\models\HouseUnauthorized;
use common\models\MemberHouse;
use common\models\MemberHouseReview;
use common\models\MemberHouseWList;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class AuthController extends Controller
{
    public function actionIndex($status=null, $group=null, $address=null)
    {
        $phone = trim($this->get('phone', null));

        $projectHouseId = $this->get('house_id');
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $query = MemberHouse::find()->joinWith('house');

        if($address){
            $query->andFilterWhere(['LIKE', 'house.ancestor_name', $address]);
        }

        if(!empty($phone)){
            $member = Member::findOne(['phone' => $phone]);
            if($member){
                $query->andFilterWhere(['member_house.member_id' => $member->id]);
            }
        } else {
            $query->andFilterWhere(['member_house.status' => $status])
                ->andFilterWhere(['member_house.group' => $group])
                ->andFilterWhere(['BETWEEN','member_house.updated_at',$dateTime->getStartTime(),$dateTime->getEndTime()]);
        }

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $query->orderBy('member_house.updated_at DESC');

        $data = [
            'dataProvider' => $dataProvider,
            'dateTime' => $dateTime,
            'status' => $status,
            'group' => $group,
            'projectId' => $projectHouseId,
            'phone' => $phone,
            'address' => $address,
        ];

        return $this->render('index', $data);
    }

    public function actionReview($group=null)
    {
        $projectHouseId = $this->get('house_id');

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = MemberHouseReview::find()->joinWith('house')
            ->andFilterWhere(['member_house_review.status' => MemberHouseReview::STATUS_WAIT_REVIEW])
            ->andFilterWhere(['member_house_review.group' => $group])
            ->andFilterWhere(['BETWEEN','member_house_review.updated_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->orderBy('member_house_review.updated_at DESC');

        $data = [
            'dataProvider' => $dataProvider,
            'dateTime' => $dateTime,
            'group' => $group,
            'projectId' => $projectHouseId,
        ];

        return $this->render('review', $data);
    }

    /**
     * 业主白名单
     * @author dtfeng
     * @Date: 2019/4/15
     * @Time: 9:47
     * @description
     */
    public function actionWhiteList(){
        $projectHouseId = $this->get('house_id');


        $kw= trim($this->get('kw', null));

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $dataProvider = new ActiveDataProvider();

        if(!empty($kw)){
            $_where = array(
                'member.nickname' => array('like'=>$kw),
            );

            $dataProvider->query = MemberHouseWList::find()->joinWith('member')
                ->where($_where);

        }else{
            $dataProvider->query = MemberHouseWList::find()->joinWith('member')
                ->andFilterWhere(['BETWEEN','member_house_wlist.updated_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
                ->orderBy('member_house_wlist.type ASC,member_house_wlist.updated_at DESC')
            ->andFilterWhere(['BETWEEN','member_house_wlist.created_at',$dateTime->getStartTime(),$dateTime->getEndTime()]);
        }


        $data = [
            'dataProvider' => $dataProvider,
            'dateTime' => $dateTime,
            'projectId' => $projectHouseId,
            'kw' => $kw,
        ];

        return $this->render('wlist', $data);
    }


    /**
     * 添加业主白名单
     * @author dtfeng
     * @Date: 2019/4/15
     * @Time: 10:44
     * @description
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate($id=0){
        $model = new MemberHouseWList();


        if ($this->isPost) {
            $postData = $this->post('MemberHouseWList');

            $Id       = $postData['id'];


            $type = $postData['type'];

            if($type != MemberHouseWList:: TYPE_DEFINE){
                $memberid = $postData['member_id'];
                $member = Member::findOne(['id'=>$memberid]);

                if($member == null){
                    return $this->setFlashErrors(null);
                }
                $_model = MemberHouseWList::findOne(['member_id'=>$memberid]);
            }else{
                $_model = null;
            }

            if($_model == null){
                $_model = MemberHouseWList::findOne(['id' => $Id]);
            }

            if ($_model != null) {
                $_model->auth_count = $postData['auth_count'];
                $_model->remark = $postData['remark'];
                $_model->updated_at      = time();
                $res                  = $_model->save();
            } else {
                $_model = new MemberHouseWList();
                $_model->load($this->post());
                $_model->status          = 0;
                $_model->created_at      = time();
                $_model->updated_at      = time();

                $res = $_model->save();
            }

            if ($res) {
                $this->setFlashSuccess();
                $this->backRedirect("/auth/white-list");
            } else {
                return $this->setFlashErrors($model->getErrors());
            }
        } else {

            $model = MemberHouseWList::findOne(['id'=>$id]);
            if ($model == null) {
                $model = new MemberHouseWList();
                $model->type = MemberHouseWList::TYPE_NORMAL;
            }

        }

        return $this->render('create', ['model' => $model ]);
    }

    public function actionShowOtherHouses($member_id = null){

        $this->layout = false;
        $data = MemberHouse::find()->alias('mh')
            ->select('house.house_id,house.ancestor_name,mh.updated_at')
            ->leftJoin('house', 'house.house_id = mh.house_id')
            ->where(['mh.status' => MemberHouse::STATUS_ACTIVE])
            ->andFilterWhere(['mh.member_id' => $member_id])
            ->orderBy("mh.updated_at DESC")->asArray()->all();

        $csrf = \Yii::$app->request->getCsrfToken();

        return $this->render('show-other-houses', get_defined_vars());
    }

    public function actionSearchMember()
    {
        $id = trim($this->get('id'));
        if(!isset($id)){
            return $this->renderJsonFail('请输入需要查询的业主ID');
        }



        $_member = Member::findOne(['id'=>$id]);
        if($_member){
            $initArray = [];
            $initArray[] = $_member->nickname;

                return $this->renderJsonSuccess($initArray);

        }else{
            return $this->renderJsonFail('无数据');
        }



    }

    /**
     * @author HQM 2018/12/27
     * @param $member_id
     * @param $house_id
     * @return \yii\web\Response
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($member_id,$house_id)
    {
        $DelType=0;
        $res = MemberHouse::findOne(['member_id'=>$member_id,'house_id'=>$house_id]);
        if(isset($res)){
            if($res->is_first==1){
                $HouseCount = MemberHouse::find()->where(['house_id'=>$house_id])->andFilterWhere(['<>','member_id',$member_id])->count();
                if($HouseCount>1){
                    $HouseFirstCount = MemberHouse::find()->where(['house_id'=>$house_id,'is_first'=>1])->andFilterWhere(['<>','member_id',$member_id])->count();
                    if($HouseFirstCount==0){
                        echo '删除前先做数据处理';
                        $HouseFirst= MemberHouse::find()->where(['house_id'=>$house_id])
                            ->andFilterWhere(['<>','member_id',$member_id])
                            ->orderBy('updated_at ASC')
                            ->one();
                        $HouseFirst->is_first =1;
                        if ($HouseFirst->save()) {
                            $DelType =1;
                            $this->setFlashSuccess();
                        }else{
                            $this->setFlashErrors($HouseFirst->getErrors());
                        }

                    }else{
                        $DelType =1;
                    }
                }else{
                    $DelType =1;
                }
            }else{
                $DelType =1;
            }
            if($DelType ==1){
                if($res->delete()){
                    $memberHouseReview = MemberHouseReview::findOne(['member_id'=>$member_id,'house_id'=>$house_id]);
                    if(isset($memberHouseReview)) {
                        $memberHouseReview->delete();
                    }

                    $this->copy2HouseUnauthorized($house_id);
                }
            }
        }

        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionExport($status=null, $group=null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $rs = (new \Yii\db\Query())->from('member_house AS mh')
            ->select("
                `m`.`nickname`,`m`.`phone`,`h`.`ancestor_name`,`mh`.`updated_at`,
                `mh`.`status`,`mh`.`identity`")
            ->leftJoin('house AS h','`mh`.`house_id` = `h`.`house_id`')
            ->leftJoin('member AS m','`mh`.`member_id` = `m`.`id`')
            ->andFilterWhere(['mh.status' => $status ?? MemberHouse::STATUS_ACTIVE])
            ->andFilterWhere(['mh.group' => $group ?? MemberHouse::GROUP_HOUSE])
            ->andFilterWhere(['BETWEEN','mh.updated_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->orderBy('mh.updated_at DESC')
            ->all();

        $identityArr = MemberHouse::identityMap();
        $statusArr = MemberHouse::statusMap();

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' .mb_convert_encoding($dateTime->getStartDate() . "至" . $dateTime->getEndDate() . '认证列表.csv','GBK','UTF8'));
        $str="所属项目,地址,微信昵称,联系手机,身份,状态,认证时间\n";

        echo mb_convert_encoding($str,'GBK','UTF8');

        foreach($rs as $v){

            $str = implode(',',[
                    substr($v['ancestor_name'], 0, strpos($v['ancestor_name'], ">") - 1),//避免项目有"-"字眼
                    $v['ancestor_name'],
                    $v['nickname'],
                    $v['phone'],
                    $identityArr[$v['identity']],
                    $statusArr[$v['status']],
                    date('Y-m-d H:i:s', $v['updated_at'])])."\n";

            echo mb_convert_encoding($str,'GBK','UTF8');
        }

        die();
    }

    public function actionExportStatistics($status=null, $group=null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $rs = MemberHouse::find()
            ->select('member_house.*, house.parent_id, house.house_name, COUNT(house.house_id) as count')
            ->joinWith('house')
            ->where(['house.project_house_id'=>$this->projectId])
            ->andFilterWhere(['member_house.status' => $status])
            ->andFilterWhere(['member_house.group' => $group])
            ->andFilterWhere(['BETWEEN','member_house.updated_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->groupBy('house.parent_id')
            ->asArray()->all();

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' .mb_convert_encoding($this->project->house_name . '认证数据统计报表.csv','GBK','UTF8'));
        $str="地址,总数\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        foreach ($rs as $key => $val){
            $houseName = House::findOne(['house_id' => $val['parent_id']]);
            $str = implode(',',[
                        $houseName->showName,
                        $val['count'],
                    ]
                )."\n";
            echo mb_convert_encoding($str,'GBK','UTF8');
        }

        die();
    }

    public function actionAuthMemberHouse()
    {
        if($this->isPost){
            $houseId = $this->post('house_id');
            $memberId = $this->post('member_id');

            $modelCount = MemberHouse::find()->where(['house_id' => $houseId,'status' =>2,'is_first' =>1])->andWhere(['<','updated_at',(time()-1)])->count();
            if($modelCount==0){
                $is_first=1;
            }else{
                $is_first=2;
            }
            $model = MemberHouse::findOne(['house_id' => $houseId, 'member_id' => $memberId, 'status' => MemberHouse::STATUS_WAIT_REVIEW]);
            if($model){
                $model->status = MemberHouse::STATUS_ACTIVE;
                $model->is_first = $is_first;
                $model->save();

                //begin 红包活动（2017-12-24 ~ 2018-02-21）
                $authActivities = \Yii::$app->params['christmas_activities'];
                $existsAuthHouse = $this->existsHouseAuth($houseId, $authActivities);
                $houseName = $model->house->showName;
                $templateCode = 'SMS_117130027';
                $smsParam = "{\"hName\":\"{$houseName}\"}";

                if($model->status == MemberHouse::STATUS_ACTIVE){
                    if($model->updated_at >= $authActivities['startTime'] && $model->updated_at <= $authActivities['endTime']){
                        if(!$existsAuthHouse){
                            $authHouseToMember = AuthHouseNotificationMember::findOrCreate($model->member_id, $model->house_id);

                            $authHouseToMember->save();

                            //橘子游活动短信提醒
                            $projectRegionIdList = [1,2,3];
                            $pgId = $model->house->project->project_region_id;
                            if(in_array($pgId, $projectRegionIdList)){
                                $templateCode = 'SMS_144145749';
                                $houseName = '（'.$houseName.'）';
                                $smsParam = "{\"houseAddress\":\"{$houseName}\"}";
                            }
                        }
                    }

                    $this->notifyMember($model->member_id, $templateCode, $smsParam);

                    //从未认证房产删除
                    HouseUnauthorized::deleteAll(['house_id' => $houseId]);
                }
                //end

                $memberHouseReview = MemberHouseReview::findOne(['member_id' => $memberId, 'house_id' => $houseId]);
                $memberHouseReview->status = MemberHouseReview::STATUS_ACTIVE;
                $memberHouseReview->save();

                return $this->renderJsonSuccess([]);
            }
        }

        return $this->renderJsonFail('error');
    }

    /**
     * 有效期（2017-12-24 ~ 2018-02-21），历史认证过的房产不在活动范围
     * @param $houseId
     * @param $authActivities
     * @return bool
     */
    private function existsHouseAuth($houseId, $authActivities)
    {
        //排除海南分公司项目
        $houseInfo = House::find()->select('project_house_id')->where(['house_id' => $houseId])->asArray()->one();
        $projectHouseId = isset($houseInfo['project_house_id']) ? $houseInfo['project_house_id'] : 0;
        if(in_array($projectHouseId, [156819,220812,222949,467387,467751,501909])){
            return true;
        }

        $house = MemberHouse::find()
            ->where(['house_id' => $houseId, 'status' => MemberHouse::STATUS_ACTIVE])
            ->andWhere(['<', 'updated_at', $authActivities['startTime']])
            ->asArray()
            ->all();
        if($house){
            return true;
        }

        return false;
    }

    /**
     * 活动期内（2017-12-24 ~ 2018-02-21）， 房产审核成功短信通知业主
     * @param $memberId
     * @param $templateCode
     * @param $smsParam
     */
    private function notifyMember($memberId, $templateCode, $smsParam)
    {
        require(\Yii::getAlias('@components/alidayuSDK/TopSdk.php'));

        $memberModel = Member::findOne(['id' => $memberId]);

        if(isset($memberModel->phone)){
            $c = new \TopClient(\Yii::$app->params['alidayu.app'], \Yii::$app->params['alidayu.secret']);
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("财到家");
            $req->setSmsParam($smsParam);
            $req->setRecNum($memberModel->phone);
            $req->setSmsTemplateCode($templateCode);
            $c->execute($req);
        }

    }

    /**
     * 复制房产信息到未认证房产表中
     * @param $houseId
     * @throws \yii\db\Exception
     */
    private function copy2HouseUnauthorized($houseId)
    {
        $memberHouseCount = MemberHouse::find()->where(['house_id' => $houseId, 'status' => 2])->count();
        if(empty($memberHouseCount)){
            HouseUnauthorized::deleteAll(['house_id' => $houseId]);
            $house = House::findOne(['house_id' => $houseId]);

            $insertData[] = [
                'house_id' => $house->house_id,
                'parent_id' => $house->parent_id,
                'project_house_id' => $house->project_house_id,
                'house_name' => $house->house_name,
                'ancestor_name' => $house->ancestor_name,
                'reskind' => $house->reskind,
                'level' => $house->level,
                'room_status' => $house->room_status,
                'deepest_node' => $house->deepest_node,
                'created_at' => time(),
            ];

            \Yii::$app->db->createCommand()->batchInsert('house_unauthorized', ['house_id', 'parent_id', 'project_house_id', 'house_name', 'ancestor_name', 'reskind', 'level', 'room_status', 'deepest_node', 'created_at'], $insertData)->execute();
        }

    }

}