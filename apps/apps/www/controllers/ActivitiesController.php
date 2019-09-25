<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/22
 * Time: 10:04
 */

namespace apps\www\controllers;


use apps\www\models\MemberVote;
use common\models\ActivitiesCollectOrder;
use common\models\ButlerElectionActivity;
use common\models\Project;

use components\cryptographic\AuthCode;
use components\wechatSDK\WechatSDK;

use yii\data\ActiveDataProvider;
use common\models\MemberHouse;
use yii\helpers\ArrayHelper;

class ActivitiesController extends Controller
{
    public function actionIndex()
    {
        $projectHouseId = isset($this->project->house_id) ? $this->project->house_id : 0;

        return $this->render('index', ['projectHouseId' => $projectHouseId]);
    }

    public function actionDragon()
    {
        $projectHouseId = $this->project->house_id;
        $projectUrlKey = $this->project->url_key;

        return $this->render('dragon', ['projectHouseId' => $projectHouseId, 'projectUrlKey' => $projectUrlKey]);
    }

    public function actionCheckReceive()
    {
        return $this->render('error', ['type' => 'end']);
    }

    public function actionFillAddress()
    {
        $activityOrder = ActivitiesCollectOrder::findOne(['member_id' => $this->user->id]);
        if ($activityOrder) {
            return $this->render('error');
        }

        $houseId = '';

        $project = $this->project->house_id;
        $model = new ActivitiesCollectOrder();
        $user = $this->user;

        return $this->render('fill-address', ['model' => $model, 'project' => $project, 'user' => $user, 'house_id' => $houseId]);
    }

    public function actionSaveAddress()
    {
        if ($this->isPost) {

            return $this->renderJsonFail('error');
        }

        return $this->renderJsonFail('error');
    }

    public function actionResult()
    {

        return $this->render('result');
    }

    /**
     * 活动管家列表
     * @param null $projectId
     * @param int $group
     * @return string
     * @author zhaowenxi
     * 注：为了满足分享功能，没有使用$this->>projectId
     */
    public function actionVoteLists($projectId = null, $group = 1)
    {
        $hasVoted = MemberVote::findOne(['member_id' => $this->user->id, 'group' => $group]);
        $voteId = !empty($hasVoted->bsa_id) ? $hasVoted->bsa_id : null;
        $projectId = isset($projectId) ? $projectId : 0;

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ButlerElectionActivity::find()
            ->where(['status' => ButlerElectionActivity::STATUS_ACTIVATE, 'group' => $group,])
            ->andFilterWhere(['project_house_id' => $projectId]);
//            ->orderBy('number DESC');

        $authUrl = Project::findOne($projectId)->url_key . '.' . \Yii::$app->params['domain.p'];
        $_wechatJs = $this->getShareSign();

        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('vote-lists-cell', ['dataProvider' => $dataProvider, 'group' => $group, 'voteId' => $voteId, 'projectId' => $projectId, 'authUrl' => $authUrl]);
        } else {
            return $this->render('vote-lists', ['dataProvider' => $dataProvider, 'group' => $group, 'voteId' => $voteId, 'projectId' => $projectId, 'authUrl' => $authUrl,'_wechatJs'=>$_wechatJs]);
        }

    }

    /**
     * 投票ajax
     * @return false|string
     * @author zhaowenxi
     * 注：为了满足分享功能，没有使用$this->>projectId
     */
    public function actionVote()
    {
        if (!$this->isPost) {
            return $this->renderJsonFail('error');
        }

        if(time() > 1559318400){
return $this->renderJsonFail('活动已结束！', 2, ['img' => '../static/images/vote/faile.jpg', 'class' => 'faile']);
        }

        $putData = $this->post('data');

        if(!$putData['projectId'] || !Project::findOne($putData['projectId'])){
            return $this->renderJsonFail('参数缺失！');
        }

        //只开放最美管家
        if($putData['group'] != 1){
            return $this->renderJsonFail('请参与最美管家活动', -3);
        }

        $checkHasHouse = MemberHouse::find()->join('LEFT JOIN', 'house',"member_house.house_id = house.house_id")
            ->where([
                'member_house.member_id' => $this->user->id,
                'member_house.group' => MemberHouse::GROUP_HOUSE,
                'member_house.status' => MemberHouse::STATUS_ACTIVE,
                'house.project_house_id' => $putData['projectId']])
            ->all();

        if(!$checkHasHouse){
            return $this->renderJsonFail('未认证业主', -2);
        }

        $group = $putData['group'];

        $authCodeObj = new AuthCode();

        $id = intval($authCodeObj->authCode($putData['id'], 'DECODE')) ;

        $butlerE = ButlerElectionActivity::findOne(['id' => $id]);

        if(!$id || !$butlerE){
            return $this->renderJsonFail('提交数据出错', -3);
        }

        $memberVote = MemberVote::find()->where(['member_id' => $this->user->id, 'group' => $group])->all();

        if(count($memberVote) >= count($checkHasHouse)){
            return $this->renderJsonFail(
                '您已投过票',
                2,
                ['img' => '../static/images/vote/10@2x.png', 'class' =>'faile']
            );
        }

        $insertHouseId = 0;

        foreach ($checkHasHouse AS &$v){

            if($memberVote){
                $hadVoteHouse = ArrayHelper::getColumn($memberVote, 'house_id');

                if(!in_array($v->house_id, $hadVoteHouse)){
                    $insertHouseId = $v->house_id;
                }

            }else{
                //获取本项目的第一个house_id
                $insertHouseId = $v->house_id;
            }
        }

        $model = MemberVote::findOrCreate($this->user->id, time(), $group);
        $model->bsa_id = $id;
        $model->house_id = $insertHouseId;
        $butlerE->number += 1;

        if ($model->save() && $butlerE->save()) {
            return $this->renderJsonSuccess(['img' => '../static/images/vote/11@2x.png', 'class' => 'success', 'number' => $butlerE->number, 'nClass' => 'li_number_' . $id]);
        }

        return $this->renderJsonFail('error');
    }

    public function actionShowImg()
    {

        return $this->render('show-img');
    }

    //最美管家/卫士获奖人员名单
    public function actionWinnerList()
    {
        $memberLists = [];
        $butlers = ButlerElectionActivity::find()->where(['status' => ButlerElectionActivity::STATUS_ACTIVATE, 'group' => ButlerElectionActivity::GROUP_BUTLER])->orderBy('number DESC')->limit(20)->all();
        $security = ButlerElectionActivity::find()->where(['status' => ButlerElectionActivity::STATUS_ACTIVATE, 'group' => ButlerElectionActivity::GROUP_SECURITY])->orderBy('number DESC')->limit(15)->all();

        foreach ($butlers as $key => $row){
            $memberLists[$row->id] = $row;
        }

        foreach ($security as $key => $row){
            $memberLists[$row->id] = $row;
        }

        ksort($memberLists, SORT_NUMERIC);

        return $this->render('/vote/winner-list', ['model' => $memberLists]);
    }

    public function actionPastWinners()
    {
        $memberLists = [];
        $butlers = ButlerElectionActivity::find()->where(['status' => ButlerElectionActivity::STATUS_ACTIVATE, 'group' => ButlerElectionActivity::GROUP_BUTLER])->orderBy('number DESC')->limit(20)->all();
        $security = ButlerElectionActivity::find()->where(['status' => ButlerElectionActivity::STATUS_ACTIVATE, 'group' => ButlerElectionActivity::GROUP_SECURITY])->orderBy('number DESC')->limit(15)->all();

        foreach ($butlers as $key => $row){
            $memberLists[$row->id] = $row;
        }

        foreach ($security as $key => $row){
            $memberLists[$row->id] = $row;
        }

        ksort($memberLists, SORT_NUMERIC);

        return $this->render('/vote/past-winners', ['model' => $memberLists]);
    }

    /**
     * @param null $houseId
     * @return string
     */
    public function actionRedPack($houseId=null)
    {
        //橘子游抽奖提示语
        $showTips = false;
        $projectRegionIdList = [1,2,3];
        $projectInfo = $this->getProjectId(true);
        if($projectInfo){
            if(in_array($projectInfo['projectRegionId'], $projectRegionIdList)){
                $showTips = true;
            }
        }

        return $this->render('red-pack', [
            'houseId' => $houseId,
            'showTips' => $showTips,
        ]);
    }

    /**
     * 节假日、活动页面
     * @param null $houseId
     * @author zhaownexi
     * @return string
     */
    public function actionRedPackActivity($houseId=null)
    {
        //橘子游抽奖提示语
        $show = false;
//        $projectRegionIdList = [1,2,3];
//        $projectInfo = $this->getProjectId(true);
//        if($projectInfo){
//            if(in_array($projectInfo['projectRegionId'], $projectRegionIdList) && $projectInfo['projectId'] != 139830 && time() < strtotime(date("20190102"))){
//                $show = true;
//            }
//        }

        return $this->render('red-pack-activity', [
            'houseId' => $houseId,
            'show' => $show,
        ]);
    }


    public function actionQixiRedPack($houseId=null)
    {
        $this->layout = 'mini';

        $Str = $this->randomQinghua();
        return $this->render('qixi-red-pack', [
            'houseId' => $houseId,
            'Str'=>$Str
        ]);

        /*
        return $this->render('red-pack', [
            'houseId' => $houseId,
        ]);
        */
    }

    public function actionChristmas()
    {

        return $this->render('christmas');
    }
    private function randomQinghua()
    {
        $Arr =[
            '我对你的爱就像拖拉机上山<br>轰轰烈烈',
            '从今以后只能称呼你为「您」<br>因为你在我的心上',
            '近朱者赤，近你着甜',
            '最近有谣言说我喜欢你<br>我要澄清一下，这是真的',
            '我想在你那里买一块地<br>买什么地<br>买你的死心塌地',
            '我是九你是三<br>我除了你还是你',
            '这是我的手背<br>这是我的脚背<br>你是我的宝贝',
            '我对你的爱就像拖拉机上山<br>轰轰烈烈',
            '近朱者赤，近你着甜',
            '最近有谣言说我喜欢你<br>我要澄清一下，这是真的',
            '我想在你那里买一块地<br>买什么地<br>买你的死心塌地',
            '我是九你是三<br>我除了你还是你',
            '这是我的手背<br>这是我的脚背<br>你是我的宝贝',
            '这是校服<br>这是水手服<br>你是我的小幸福',
            '我喜欢你<br>像你妈打你<br>不讲道理',
            '把你的名字写在烟上，吸进肺里<br>这样就能离的我心最近了',
            '你知道我最近最喜欢喝什么吗？<br>什么？<br>呵护你',
            '我最近学会了一门新技能「算命」<br>我掐指一算，你命里缺我',
            '你猜的我的心在哪边？<br>左边？<br>不对，在你那边',
            '你知道我的缺点是什么吗？<br>缺点你',
            '你知道情人眼里出什么吗？<br>西施？<br>不对，是出现你',
            '天台挤不下了<br>往我心里挤挤',
            '不想赢球 不想赢钱 <br>只想赢你的心',
        ];

        $Number = rand(0, 16);
       return $Arr[$Number];
    }

    /**
     * 节假日、活动页面
     * @param null $houseId
     * @author zhaownexi
     * @return string
     */
    public function actionRedPackQuestion($houseId=null)
    {
        return $this->render('red-pack-question', [
            'houseId' => $houseId,
        ]);
    }

    /**
     * 微信签名
     * @author dtfeng
     * @Date: 2019/4/19
     * @Time: 11:02
     * @description
     * @return array|bool
     */
    public function getShareSign(){
        $_url =\Yii::$app->request->hostInfo . \Yii::$app->request->url;
        $_wechatJs = (new WechatSDK(\Yii::$app->params['wechat']))->getJsSign($_url);

        return $_wechatJs;
    }
}