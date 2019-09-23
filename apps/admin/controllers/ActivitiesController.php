<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/26
 * Time: 10:11
 */

namespace apps\admin\controllers;


use apps\admin\models\Member;
use common\models\ActivitiesCollectOrder;
use common\models\ActivitiesLog;
use common\models\ButlerAuth;
use common\models\ButlerElectionActivity;
use common\models\ButlerRegion;
use common\models\House;
use common\models\MemberHouse;
use common\models\Project;
use common\models\Vote;
use common\models\WechatRedPack;
use common\models\XgRequestLog;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

class ActivitiesController extends Controller
{
    public function actionDragonOrderLists($projectHouseId = null, $status = null)
    {
        $house_id = $this->get('house_id', null);
        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $identificationStatus = null;
        $payStatusWhere = null;

        if (isset($status)) {
            switch ($status) {
                case 0:
                    $identificationStatus = 1;
                    $payStatusWhere = 0;
                    break;
                case 1:
                    $payStatusWhere = 1;
                    break;
                default:
                    $identificationStatus = null;
                    $payStatusWhere = null;
                    break;
            }
        }

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ActivitiesCollectOrder::find()
            ->joinWith('activitiesLog')
            ->andFilterWhere(['activities_collect_order.project_house_id' => $house_id])
            ->andFilterWhere(['activities_log.identification_status' => $identificationStatus])
            ->andFilterWhere(['activities_log.pay_status' => $payStatusWhere])
            ->orderBy('id DESC');

        $dataProvider->setSort(false);

        return $this->render('dragon-order-lists', [
            'dataProvider' => $dataProvider,
            'house_id' => $house_id,
            'projectsArray' => $projectsArray,
            'status' => $status,
        ]);
    }

    public function actionDragonLogLists()
    {
        $typeLists = [
            'all' => '全部',
            'co_time' => '领取时间',
            'id_time' => '认证时间',
            'bill_time' => '缴费时间',
            'ca_time' => '访问时间',
        ];

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id', null);
        $type = $this->get('type', null);
        $search = trim($this->get('search', null));
        $onlyCa = $this->get('onlyCa', null);
        $IdTrue = $this->get('IdTrue', null);
        $payed = $this->get('payed', null);
        $collType = $this->get('collType', 2);
        $collectStartTime = $collectEndTime = null;
        $payStartTime = $payEndTime = null;
        $createStartTime = $createEndTime = null;
        $identificationStartTime = $identificationEndTime = null;
        $collectStatus = $payStatus = $identificationStatus = null;

        switch ($type) {
            case 'co_time':
                $collectStartTime = $dateTime->getStartTime();
                $collectEndTime = $dateTime->getEndTime();
                break;
            case 'id_time':
                $identificationStartTime = $dateTime->getStartTime();
                $identificationEndTime = $dateTime->getEndTime();
                break;
            case 'bill_time':
                $payStartTime = $dateTime->getStartTime();
                $payEndTime = $dateTime->getEndTime();
                break;
            default:
                $createStartTime = $dateTime->getStartTime();
                $createEndTime = $dateTime->getEndTime();
                break;
        }

        if (!empty($onlyCa)) {
            $identificationStatus = 0;
            $payStatus = 0;
        }

        if (!empty($IdTrue)) {
            $identificationStatus = 1;
        }

        if (!empty($payed)) {
            $payStatus = 1;
        }

        switch ($collType) {
            case 0:
                $collectStatus = 0;
                break;
            case 1:
                $collectStatus = 1;
                break;
            default:
                $collectStatus = $payStatus = $identificationStatus = null;
                break;
        }

        $dataProvider = new ActiveDataProvider();
        $query = ActivitiesLog::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'collect_time', $collectStartTime, $collectEndTime])
            ->andFilterWhere(['BETWEEN', 'identification_time', $identificationStartTime, $identificationEndTime])
            ->andFilterWhere(['BETWEEN', 'pay_time', $payStartTime, $payEndTime])
            ->andFilterWhere(['BETWEEN', 'created_at', $createStartTime, $createEndTime])
            ->andFilterWhere(['identification_status' => $identificationStatus])
            ->andFilterWhere(['pay_status' => $payStatus])
            ->andFilterWhere(['collect_status' => $collectStatus])
            ->orderBy('id DESC');

        if ($search) {
            $query->andFilterWhere(['like', 'nick_name', $search])
                ->orFilterWhere(['like', 'name', $search])
                ->orFilterWhere(['like', 'phone', $search]);
        }

        $dataProvider->query = $query;

        $dataProvider->setSort(false);

        return $this->render('dragon-log-lists', get_defined_vars());
    }

    public function actionDragonExport($house_id = null, $status = null)
    {
        /*$rs = ActivitiesCollectOrder::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->orderBy('id DESC');*/

        $identificationStatus = null;
        $payStatusWhere = null;
        $typeText = '';

        if (!empty($status)) {
            switch ($status) {
                case 0:
                    $identificationStatus = 1;
                    $payStatusWhere = 0;
                    $typeText = '认证';
                    break;
                case 1:
                    $payStatusWhere = 1;
                    $typeText = '缴费';
                    break;
                default:
                    $identificationStatus = null;
                    $payStatusWhere = null;
                    break;
            }
        }

        $rs = ActivitiesCollectOrder::find()
            ->joinWith('activitiesLog')
            ->andFilterWhere(['activities_collect_order.project_house_id' => $house_id])
            ->andFilterWhere(['activities_log.identification_status' => $identificationStatus])
            ->andFilterWhere(['activities_log.pay_status' => $payStatusWhere])
            ->orderBy('id DESC');

        $projectName = '';
        if (empty($house_id)) {
            $projectName = '所有项目';
        } else {
            $projectModel = Project::findOne(['house_id' => $house_id]);
            $projectName = $projectModel->house_name;
        }

        $projectName .= '端午活动-' . $typeText;
        $fileName = $projectName . '-领取人员报表.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str = "收货人,手机号,认证/缴费,领取状态,用户收货地址,用户备注,领取时间\n";
        echo mb_convert_encoding($str, 'GBK', 'UTF8');

        foreach ($rs->each() as $row) {
            /**
             * @var $row ActivitiesCollectOrder
             */
            $str = implode(',', [
                    $row->user_name,
                    $row->tel,
                    $row->house->ancestor_name,
                    $row->statusText,
                    $row->userHouse->ancestor_name,
                    $row->comment,
                    date('Y-m-d H:i:s', $row->created_at)]) . "\n";

            echo mb_convert_encoding($str, 'GBK', 'UTF8');
        }
        die();

    }

    public function actionVoteLists()
    {

        return $this->render('vote-lists');
    }

    public function actionVoteCreateOrUpdate($id = null)
    {
        $model = empty($id) ? new Vote() : Vote::findOne($id);

        if ($this->isPost && $model->load($this->post())) {
            $model->start_time = strtotime($this->post('start_time'));
            $model->end_time = strtotime($this->post('end_time'));

            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['vote-lists']);
            } else {
                $this->setFlashError($model->getErrors());
            }
        }

        return $this->render('vote-create', ['model' => $model]);
    }

    public function actionVoteDetail(int $group = 1)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $house_id   = $this->get('house_id', null);
        $status     = $this->get('status', null);
        $butlerName = $this->get('name', null);

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ButlerElectionActivity::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['group' => $group])
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['LIKE', "name", $butlerName])
            ->orderBy('number DESC');
        $dataProvider->setSort(false);

        return $this->render('vote-detail', [
            'projectsArray' => $projectsArray,
            'dateTime' => $dateTime,
            'name' => $butlerName,
            'house_id' => $house_id,
            'dataProvider' => $dataProvider,
            'group' => $group,
            'status' => $status,
            'timeArr' => [$dateTime->getStartTime(), $dateTime->getEndTime()]
        ]);
    }

    public function actionVoteExport(int $group = 1)
    {
        $house_id = $this->get('house_id', null);
        $status = $this->get('status', null);
        $timeArr = $this->get('timeArr', null);

        $data = ButlerElectionActivity::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['group' => $group])
            ->andFilterWhere(['status' => $status ?? ButlerElectionActivity::STATUS_ACTIVATE])
            ->orderBy('number DESC')->all();

        if (empty($house_id)) {
            $projectName = '所有项目';
        } else {
            $projectModel = Project::findOne(['house_id' => $house_id]);
            $projectName = $projectModel->house_name;
        }

        $fileName = $projectName . '-参与投票人员列表.csv';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str = "姓名,手机号,分公司,项目,管辖区域,总票数,管辖区得票数,管辖区户数\n";
        echo mb_convert_encoding($str, 'GBK', 'UTF8');

        /** @var ButlerElectionActivity $row */
        foreach ($data as $row){

            $voteSql = "SELECT count(1) AS count FROM `member_vote` WHERE bsa_id = {$row->id}";

            $areaSql = "SELECT count(1) AS count FROM `member_vote` WHERE house_id IN (SELECT house_id FROM butler_region WHERE 
            butler_id = " . $row->butler_id . ") AND `bsa_id` = " . $row->id;

            if($timeArr){
                $voteSql .= " AND vote_time BETWEEN {$timeArr[0]} AND {$timeArr[1]}";
                $areaSql .= " AND vote_time BETWEEN {$timeArr[0]} AND {$timeArr[1]}";
            }

            $voteCount = \Yii::$app->db->createCommand($voteSql)->queryOne();
            $uniqCount = \Yii::$app->db->createCommand($areaSql)->queryOne();

            $region = "无";

            $manaCount = 0;

            $reskind = [2, 3, 4, 5, 6, 7, 8];

            in_array($row->project_house_id, [296148, 563917]) && $reskind = [2, 3, 4, 5, 6, 7];  //南通湖滨华庭、岁金时代去除储藏室

            if($row->butler_id != 0){
                $region = implode("、", $row->regionHouse);

                $manaCount = ButlerRegion::find()->alias('br')->leftJoin('house', 'house.house_id = br.house_id')
                    ->where(['IN', 'house.reskind', $reskind])
                    ->andWhere(['br.butler_id' => $row->butler_id])
                    ->andWhere(['house.deepest_node' => 1])
                    ->count();
            }

            /* @var $row ButlerElectionActivity */
            $str = implode(',', [
                    $row->name,
                    $row->phone,
                    $row->project->projectRegionName,
                    $row->project->house_name,
                    $region,
                    $voteCount['count'],
                    $uniqCount['count'],    //辖区得票
                    $manaCount ?? '-',    //辖区户数
                ]) . "\n";

            echo mb_convert_encoding($str, 'GBK', 'UTF8');
        }

        die();
    }

    public function actionVoteAccountUpdate(int $id = 0, int $group = 1)
    {
        $model = ButlerElectionActivity::findOne($id);

        if ($this->isPost) {
            if ($model->load($this->post())) {

                $model->butler_id = ButlerAuth::findOne(['account' => $model->phone])->used_to ?? 0;

                $model->save();

                $this->setFlashSuccess();

                return $this->backRedirect(['/activities/vote-detail?group=' . $group]);
            } else {
                $this->setFlashError('操作失败');
            }
        }

        return $this->render('vote-account-update', ['model' => $model, 'group' => $group]);
    }

    public function actionXgRequestLog()
    {
        $house_id = $this->get('house_id', null);
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = XgRequestLog::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('created_at DESC');
        $dataProvider->setSort(false);

        return $this->render('xg-request-log', [
                'dataProvider' => $dataProvider, 'house_id' => $house_id, 'projectsArray' => $projectsArray,
                'dateTime' => $dateTime
            ]
        );
    }

    public function actionXgRequestExport($house_id = null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $rs = XgRequestLog::find()
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('id DESC');

        $projectName = '';
        if (empty($house_id)) {
            $projectName = '所有项目';
        } else {
            $projectModel = Project::findOne(['house_id' => $house_id]);
            $projectName = $projectModel->house_name;
        }

        $fileName = $projectName . '-小狗电器访问记录.csv';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str = "产品名,对应码,项目,点击来源,请求时间\n";
        echo mb_convert_encoding($str, 'GBK', 'UTF8');

        foreach ($rs->each() as $row) {
            /**
             * @var $row XgRequestLog
             */
            $str = implode(',', [
                    $row->title,
                    $row->type_code,
                    $row->project->house_name,
                    $row->typeText,
                    date('Y-m-d H:i:s', $row->created_at)
                ]) . "\n";

            echo mb_convert_encoding($str, 'GBK', 'UTF8');
        }

        die();
    }

    public function actionPropertyPay()
    {
        $house_id = $this->get('house_id', null);
        $even_name = $this->get('even_name', '20170810');
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');


        $redPackAmount = WechatRedPack::find()
            ->andFilterWhere(['even_name' => $even_name, 'project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->sum('amount');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = WechatRedPack::find()
            ->andFilterWhere(['even_name' => $even_name, 'project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('created_at DESC');
        $dataProvider->setSort(false);

        return $this->render('property-pay', [
                'dataProvider' => $dataProvider,
                'house_id' => $house_id,
                'projectsArray' => $projectsArray,
                'dateTime' => $dateTime,
                'redPackAmount' => $redPackAmount,
            ]
        );
    }

    public function actionAuthRedPack()
    {
        $house_id = $this->get('house_id', null);
        $even_name = $this->get('even_name', '201812');
        $phone = trim($this->get('phone'));
        $memberId = null;
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        if(!empty($phone)){
            $member = Member::findOne(['phone' => $phone]);
            if($member){
                $memberId = $member->id;
            }
        }


        $redPackAmount = WechatRedPack::find()
            ->andFilterWhere(['even_name' => $even_name, 'project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->sum('amount');

        $RedPackNumber = WechatRedPack::find()
            ->select('amount,COUNT(1) as count')
            ->andFilterWhere(['even_name' => $even_name, 'project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->groupBy('amount')
            ->asArray()->all();

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = WechatRedPack::find()
            ->andFilterWhere(['even_name' => $even_name, 'project_house_id' => $house_id])
            ->andFilterWhere(['member_id' => $memberId])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('created_at DESC');
        $dataProvider->setSort(false);

        return $this->render('auth-red-pack', [
                'dataProvider' => $dataProvider,
                'house_id' => $house_id,
                'projectsArray' => $projectsArray,
                'dateTime' => $dateTime,
                'redPackAmount' => $redPackAmount,
                'phone' => $phone,
                'even_name' => $even_name,
                'RedPackNumber' => $RedPackNumber,
            ]
        );
    }

    public function actionChristmasRedExport()
    {
        $even_name = $this->get('even_name', '201812');
        $projectHouseId = $this->get('house_id');
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $total = WechatRedPack::find()
            ->where(
                [
                    'even_name' => $even_name
                ]
            )
            ->andFilterWhere(['BETWEEN','created_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->count();

        $defaultLimit = 500;
        $pageCount = ceil($total / $defaultLimit);
        $offset = 0;

        $projectName = '业主认证送红包当前数据报表--';
        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str = "项目,用户,手机号,房产,活动,金额,时间,总额\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        $model = WechatRedPack::find()
            ->where(
                [
                    'even_name' => $even_name
                ]
            )
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->orderBy("id ASC");

        foreach($model->each() as $row){
            /**
             * @var $row WechatRedPack
             */

            $createdAt = date("Y-m-d H:i:s", $row->created_at);
            $houseName = str_replace(',', '-', $row->house->ancestor_name);
            $memberName = str_replace([',', '"'], 're-', $row->member->showName);

            $str = <<<STR
{$row->project->house_name},$memberName,{$row->member->phone},$houseName,{$row->remark},{$row->amount},$createdAt,-,\n
STR;

            echo mb_convert_encoding($str,'GBK','UTF8');
        }

        die();
    }


}