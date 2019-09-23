<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/18
 * Time: 14:35
 */

namespace console\controllers;


use common\models\AuthPrivateLog;
use common\models\Butler;
use common\models\ButlerRegion;
use common\models\House;
use common\models\HouseUnauthorized;
use common\models\Member;
use common\models\MemberHouse;
use common\models\MemberHouseReview;
use common\models\MemberHouseTest;
use common\models\MeterHouse;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\Project;
use common\models\ProjectButlerManageLists;
use common\models\ProjectPayConfig;
use common\models\QuestionAnswer;
use common\models\QuestionUserChose;
use components\helper\HttpRequest;
use components\newWindow\NewWindow;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class UpdatesController extends Controller
{
    /**
     * 更新项目管家管理区域,projectHouseId[0],butlerId[0]
     * @param int $projectHouseId
     * @param int $butlerId
     */
    public function actionButlerRegion($projectHouseId=0, $butlerId=0)
    {
        if(empty($projectHouseId) && !empty($butlerId)){
            $model = Butler::findOne(['id' => $butlerId]);
            if ($model) {
                $regionIds = explode(',', $model->regions);
                ButlerRegion::deleteAll(['butler_id' => $butlerId]);

                if(in_array($model->project_house_id, $regionIds)){
                    $regionIds = $model->project_house_id;
                }

                if(sizeof($regionIds)){
                    $butlerAuthId = 0;
                    if($model->butlerAuth->used_to){
                        $butlerAuthId = $model->butlerAuth->id;
                    }

                    if(ButlerRegion::saveButlerRegion($model->id, $regionIds, $butlerAuthId)){
                        $regionCount = ButlerRegion::find()->where(['butler_id' => $butlerId])->count();
                        $model->mana_number = $regionCount;
                        $model->save();

                        $this->stdout("-------Done \n");
                    }else{
                        $this->stdout("管理区域保存失败 \n");
                    }
                }
            }
        } else{
            $model = Butler::find()
                ->where(['project_house_id' => $projectHouseId])
                ->andWhere(['status' => Butler::STATUS_ENABLE, 'group' => Butler::GROUP_1]);
            foreach($model->each() as $row){
                /**
                 * @var Butler $row
                 */
                if(!empty($row->regions)){

                    $this->stdout("regions：{$row->regions} \n");

                    $regionIds = explode(',', $row->regions);
                    ButlerRegion::deleteAll(['butler_id' => $row->id]);

                    if(in_array($projectHouseId, $regionIds)){
                        $regionIds = $projectHouseId;
                    }

                    if(!empty($regionIds)){
                        $butlerAuthId = 0;
                        if(isset($row->butlerAuth->used_to)){
                            $butlerAuthId = $row->butlerAuth->id;
                        }
                        if(ButlerRegion::saveButlerRegion($row->id, $regionIds, $butlerAuthId)){
                            $this->stdout("------Done \n");
                        }else{
                            $this->stdout("------管理区域保存失败 \n");
                        }
                    }
                    $this->stdout("-------批量更新项目管家管理区域, Butler：{$row->nickname}\n");

                    $mCount = ButlerRegion::find()->where(['butler_id' => $row->id])->count();
                    $row->mana_number = $mCount;
                    $row->save();

                    sleep(1);
                }
            }
        }

        $this->stdout("exit \n");
        exit(1);
    }

    /**
     * 项目招商银行支付配置 $projectHouseId，$key，$mchId
     * @param $projectHouseId
     * @param $key
     * @param $mchId
     */
    public function actionProjectPayConfig($projectHouseId, $key, $mchId)
    {
        $model = ProjectPayConfig::findOne(['project_house_id' => $projectHouseId]);
        if(!$model){
            $model = new ProjectPayConfig();
            $model->project_house_id = $projectHouseId;
            $model->key = $key;
            $model->mch_id = $mchId;

            if($model->save()){
                $project = Project::findOne(['house_id' => $projectHouseId]);
                $project->pay_type = Project::PAY_TYPE_SW;
                $project->save();

                $this->stdout("projectHouseId：{$projectHouseId} create success \n");
                exit(1);
            }
        }

        $this->stdout("exists \n");
        exit(1);
    }

    /**
     * 为业主批量添加房产[memberId,projectId,Keyword,KeywordType(0,multiHouse(0]
     * @param $memberId
     * @param $projectId
     * @param $Keyword
     * @param int $KeywordType
     * @param int $multiHouse
     * @throws ErrorException
     * @throws \yii\db\Exception
     */
    public function actionMemberAddHouse($memberId, $projectId, $Keyword, $KeywordType=0, $multiHouse=1)
    {
        $insertData = [];
        $number = 0;

        $res = (new NewWindow())->getCustomerHouseInfo($projectId, $Keyword, $KeywordType, $multiHouse);
        if(!empty($res['Response']['Data']['Record'])){
            foreach($res['Response']['Data']['Record'] as $key => $val){
                if($val['ResKind'] == 5){
                    $group = MemberHouse::GROUP_HOUSE;
                } else {
                    $group = MemberHouse::GROUP_PARKING;
                }
                $insertData[$key] = [
                    'member_id' => $memberId,
                    'house_id' => $val['HouseID'],
                    'group' => $group,
                    'identity' => MemberHouse::IDENTITY_OWNER,
                    'status' => MemberHouse::STATUS_ACTIVE,
                    'created_at' => time(),
                    'updated_at' => time(),
                ];

                $number++;
            }

            \Yii::$app->db->createCommand()->batchInsert('member_house', ['member_id', 'house_id', 'group', 'identity', 'status', 'created_at', 'updated_at'], $insertData)->execute();
            $insertData = null;
        }

        $this->stdout("已执行完成 ：{$number}\n");
    }

    /**
     * 更新物业缴费订单房产类型 house_type 字段 param[$limit, $pmOrderId]
     * @param int $limit
     * @param null $pmOrderId
     * @param int $houseType
     */
    public function actionPmOrderHouseType($limit=1000, $pmOrderId=null, $houseType=0)
    {
        $model = PmOrder::find()
            ->where(['house_type' => $houseType])
            ->andFilterWhere(['id' => $pmOrderId])
            ->limit($limit)->orderBy('id DESC')->all();

        foreach($model as $row){
            /**
             * @var PmOrder $row
             */
            if(isset($row->house->structure->group)){
                $row->house_type = $row->house->structure->group;
                $update = $row->save();
                $this->stdout("PmOrderId：{$row->id}-》Update house_type：{$update} --- {$row->house->structure->group} \n");

            } else {
                $this->stdout("未找到：{$row->id} \n");
            }
        }

        $this->stdout("Done \n");
        exit(1);
    }

    /**
     * 更新管家管理区域日认证|缴费报表 项目ID等
     */
    public function actionProjectButlerManageLists()
    {
        $yesterday = strtotime('-1 days');
        $yesterday = date('Y-m-d', $yesterday);
        $startTime = $yesterday . " 00:00:00";
        $startTime = strtotime($startTime);
        $nowTime = time();

        $model = ProjectButlerManageLists::find()
            ->where(['project_house_id' => 0])
            ->andWhere(['BETWEEN', 'created_at', $startTime, $nowTime])
            ->orderBy('id ASC');

        $index = 0;

        foreach($model->each() as $row){
            /**
             * @var ProjectButlerManageLists $row
             */
            $index++;
            $butlerInfo = Butler::findOne(['nickname' => $row->butler_name, 'group' => Butler::GROUP_1]);

            if(isset($butlerInfo->project_house_id)){
                $row->project_house_id = $butlerInfo->project_house_id;
                $row->project_region_id = $butlerInfo->project->project_region_id;
                $row->butler_id = $butlerInfo->id;

                if($row->save()){
                    $this->stdout("已更新：{$row->id} \n");
                } else {
                    $this->stdout("更新失败：{$row->id} \n");
                }
            }

        }

        $this->stdout("Done \n");
        exit(1);
    }

    /**
     * 更新 MemberHouse 表 is_first 字段
     */
    public function actionAuthFirst()
    {
        $model = MemberHouse::find()
            ->where(['is_first' => 0, 'status' => 2])
            ->orderBy('updated_at ASC');

        $number = 0;
        $runCount = 0;
        foreach($model->each() as $row){
            /**
             * @var MemberHouse $row
             */
            $number += 1;
            $runCount += 1;
            $count = MemberHouse::find()->where(['house_id'=>$row->house_id,'status'=>2])->andWhere(['<','updated_at',$row->updated_at])->andWhere(['<','updated_at',$row->updated_at])->count();
            if($count==0){
                $is_first = 1;
            }else{
                $is_first = 2;
            }

            MemberHouse::updateAll(['is_first' => $is_first], ['house_id'=>$row->house_id,'member_id'=>$row->member_id]);
//            \Yii::$app->db->createCommand()->update('member_house', ['is_first' => $is_first], ['house_id'=>$row->house_id,'member_id'=>$row->member_id])->execute();

            $this->stdout("number：{$number} \n");

            if($number > 100){
                $number = 0;
                $this->stdout("-----sleep \n");
                sleep(1);
            }
        }

        $count = MemberHouse::find()->where(['is_first'=>0])->count();

        $this->stdout("count：{$count} \n");
        $this->stdout("runCount:{$runCount} \n");
        exit(1);
    }



    /**
     * 更新 QuestionAnswer 表 chose_ancestor_name 字段
     */
    public function actionAnswerUpdate()
    {
        $model = QuestionAnswer::find()
            ->where(['chose_ancestor_name'=>''])
            ->orderBy('updated_at ASC');

        $number = 0;
        $runCount = 0;
        foreach($model->each() as $row){
            /**
             * @var MemberHouse $row
             */
            $rowLib = QuestionUserChose::findOne(['answer_id'=>$row->id]);
            if(isset($rowLib)){
                QuestionAnswer::updateAll(['chose_ancestor_name' => $rowLib->house_name], ['id'=>$row->id]);
                echo $row->id."--------".$rowLib->house_name."----------\n";
            }
            sleep(0.1);
        }

    }




    /**
     * 更新 水表和污水表合并，以水表为主 表 type_id 字段
     * @param int $project_id
     */
    public function actionMeterTypeUpdate($project_id)
    {
        $model = MeterHouse::find()
            ->where(['like', 'uid', 'PW'])
            ->andFilterWhere(['type_id'=>0, 'project_id' => $project_id])
            ->select(['ancestor_name','meter_type','house_id','id'])
            ->orderBy('id ASC');

        foreach($model->each() as $row){
            $rowLib = MeterHouse::find()
                ->where(['house_id'=>$row->house_id,'meter_type'=>$row->meter_type])
                ->andFilterWhere(['like', 'uid', 'S'])
                ->select(['meter_id'])
                ->one();
            if(isset($rowLib)){
                MeterHouse::updateAll(['type_id' => $rowLib->meter_id], ['id'=>$row->id]);
                echo $row->ancestor_name."-的水表存在--和污水表合并-----".$rowLib->meter_id."----------\n";
            }
            sleep(0.2);
        }

    }


    /**
     * 更新订单明细状态:date,status[2]
     * @author HQM 2019/01/03
     * @param $date
     * @param int $status
     */
    public function actionOrderStatus($date, $status=2)
    {
        $whereStartDate = strtotime($date . ' 00:00:00');
        $whereEndDate = strtotime($date.' 23:59:59');
        $this->stdout("startTime: {$whereStartDate} \n");
        $this->stdout("endTime: {$whereEndDate}\n");

        $model = PmOrder::find()
            ->where(['status' => $status])
            ->andWhere(['BETWEEN', 'payed_at', $whereStartDate, $whereEndDate])
            ->orderBy('id ASC');
        foreach($model->each(100) as $row){
            /**
             * @var $row PmOrder
             */
            $this->stdout("orderId: {$row->id}\n");

            PmOrderItem::updateAll(['status' => 1000], ['pm_order_id' => $row->id]);
        }

        $this->stdout("Done\n");
        exit(0);
    }



    /**
     * 更新业主姓名：startTime,endTime,dateTime[0]
     * 2018-5-15
     * @param $startTime
     * @param $endTime
     * @param $dateTime
     */
    public function actionMemberName($startTime, $endTime, $dateTime=0)
    {
        if($dateTime > 0){
            $startTime = strtotime($dateTime . " 00:00:00");
            $endTime = strtotime($dateTime . " 23:59:59");
        }

        $model = MemberHouseReview::find()
            ->where(['status' => 2])
            ->andWhere(['BETWEEN', 'updated_at', $startTime, $endTime])
            ->orderBy('id DESC');

        $number = 0;
        foreach($model->each() as $row){
            /**
             * @var MemberHouseReview $row
             */
            $number += 1;
            $this->stdout("已执行：{$number} 次 \n");

            $member = Member::findOne(['id' => $row->member_id]);
            if(isset($member->id) && empty($member->name)){
                $this->stdout("业主ID：{$member->id} \n");
                $this->stdout("{$row->customer_name} \n");

                $selectV = Console::select('-----业主姓名：'.$row->customer_name, ['y' => '选择', 'n' => '跳过']);

                $this->stdout("-----选择：{$selectV} \n");

                if($selectV == 'y'){
                    $member->name = $row->customer_name;
                    if($member->save()){
                        $this->stdout("------更新用户姓名：{$member->name}\n");
                    }
                }
            }
        }

        $this->stdout("Done \n");
        exit(1);
    }

    /**
     * 复制房产数据到 HouseUnauthorized 表，projectId
     * 2018-05-16
     * @param $projectHouseId
     * @throws \yii\db\Exception
     */
    public function actionCopyHouseU($projectHouseId)
    {
        $model = House::find()->where(['project_house_id' => $projectHouseId])->orderBy('house_id ASC');
        $insertData = [];
        $number = 0;
        $runLine = 0;

        HouseUnauthorized::deleteAll(['project_house_id' => $projectHouseId]);

        foreach($model->each() as $row){
            /**
             * @var House $row
             */
            $number += 1;
            $runLine += 1;
            $this->stdout("number：{$number} \n");

            $insertData[] = [
                'house_id' => $row->house_id,
                'parent_id' => $row->parent_id,
                'project_house_id' => $row->project_house_id,
                'house_name' => $row->house_name,
                'ancestor_name' => $row->ancestor_name,
                'reskind' => $row->reskind,
                'level' => $row->level,
                'room_status' => $row->room_status,
                'deepest_node' => $row->deepest_node,
                'created_at' => time(),
            ];

            if($number == 10){
                \Yii::$app->db->createCommand()->batchInsert('house_unauthorized', ['house_id', 'parent_id', 'project_house_id', 'house_name', 'ancestor_name', 'reskind', 'level', 'room_status', 'deepest_node', 'created_at'], $insertData)->execute();
                $insertData = null;
                $number = 0;
                $this->stdout("-------sleep 1 \n");
            }

        }

        if(count($insertData) > 0){
            \Yii::$app->db->createCommand()->batchInsert('house_unauthorized', ['house_id', 'parent_id', 'project_house_id', 'house_name', 'ancestor_name', 'reskind', 'level', 'room_status', 'deepest_node', 'created_at'], $insertData)->execute();
            unset($insertData);
        }

        $this->stdout("runLine：{$runLine} \n");
        $this->stdout("Done \n");

        $this->actionDeleteHouseAu($projectHouseId);

        exit(1);
    }

    /**
     * 从未认证房产表数据删除已认证过的房产 $projectHouseId[null,$dateTime[null
     * @param $projectHouseId
     * @param $dateTime
     */
    public function actionDeleteHouseAu($projectHouseId=null, $dateTime=null)
    {
        $projectId = null;
        if(!empty($projectHouseId)){
            $projectId = $projectHouseId;
        }

        $model = Project::find()
            ->andFilterWhere(['house_id' => $projectId])
            ->orderBy("house_id");
        $startTime = null;
        $endTime = null;
        if($dateTime){
            $beginTime = $dateTime . ' 00:00:00';
            $startTime = strtotime($beginTime);
            $endTime = $dateTime . ' 23:59:59';
            $endTime = strtotime($endTime);
        }

        foreach($model->each() as $row){
            /**
             * @var Project $row
             */
            $this->stdout("ProjectHouseId：{$row->house_id} \n");

            $memberHouseM = MemberHouse::find()->joinWith('house')
                ->select('member_house.house_id')
                ->where(['house.project_house_id'=>$row->house_id, 'member_house.status' => 2])
                ->andFilterWhere(['BETWEEN', 'member_house.updated_at', $startTime, $endTime])
                ->groupBy('member_house.house_id')->all();
            $memberHouseMIds = ArrayHelper::getColumn($memberHouseM, 'house_id');

            foreach($memberHouseMIds as $key => $val){
                HouseUnauthorized::deleteAll(['house_id' => $val]);
                $this->stdout("-------deleteHouseId：{$val} \n");
            }

            $count = HouseUnauthorized::find()->where(['project_house_id' => $row->house_id])->count();

            $this->stdout("houseUnauthorizedCount：{$count} \n");
            $this->stdout("delete Done \n");

            sleep(1);
        }

        exit(1);
    }

    /**
     * 更新房产状态 projectHouseId
     * @param $projectHouseId
     * @throws \yii\base\ErrorException
     */
    public function actionHouseRoomStatus($projectHouseId)
    {
        $model = House::find()
            ->select('house_id')
            ->where(['deepest_node' => 1])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->orderBy('house_id ASC');
        $tempNumber = 0;
        $runNumber = 0;

        foreach($model->each() as $row){
            /**
             * @var House $row
             */
            $tempNumber +=1;
            $runNumber += 1;

            $this->stdout("run------{$runNumber} \n");

            $house = (new NewWindow())->houseStructure($row->house_id);
            $Record = $house['Response']['Data']['Record'];

            if(!empty($Record)){
                House::getDb()->close();

                $std = "houseId: {$row->house_id} - " . "roomStatus: {$Record[0]['RoomStatus']}\n";
                $this->stdout($std);

                $update = House::updateAll(['room_status' => $Record[0]['RoomStatus']], ['house_id' => $row->house_id]);

                if($update){
                    $this->stdout("----------update---{$row->house_id} \n");
                } else {
                    $this->stdout("-------updateFail---{$update} \n");
                }

                if($runNumber > 100){
                    $runNumber = 0;
                    sleep(2);
                }
            }
        }

        $this->stdout("Done \n");
        exit(0);
    }

    /**
     * 重新提交开票请求：pmOrderId
     * @param $pmOrderId
     */
    public function actionNewwindowF($pmOrderId)
    {
        $requestUrl = 'http://testmgt.51homemoney.com/member-bill-notify/newwindow-open-fp';
        $data = [
            'pmOrderFpzzId' => $pmOrderId,
        ];
        HttpRequest::post($requestUrl, $data);
    }

    /**
     * 内部认证
     * @param $userId
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionAuthPrivate($userId){

        if($userId != 1170){
            $this->stdout("------你不是内部号... \n");
            exit;
        }

        //所有项目id
        $allProjectId = Project::find()->select('house_id,house_name')
            ->where(['not in', 'house_id', [
                53751,
                308671,
                84244,
                65981,
                121731,
                414686,
                501909,
                42503,
                164557,
                7116,
                296140,
                502542,
                351273,
                268516,
                1,
                269383,
                258430,
                201069,
                296144,
                296152,
                295954,
                296143,
                7110,
                489944,
                182520,
                280560,
                296191,
                165260,
                296149,
                254825,
                286993,
                417899,
                432593,
                33842,
                117847,
                294852,
                371080
                ]])
            ->all();

        //每个项目20个
        $num = 10;

        $memberData = $logData = [];

        foreach ($allProjectId as $k => $v){

            $this->stdout("-------查找{$v->house_name}下10个未认证的房产 \n");

            //查找项目没有认证的房产，上限10
            $notAuthHouses = House::find()->select("house_id")
                ->where(['reskind' => 5, "project_house_id" => $v->house_id])
                ->andFilterWhere(['not in', 'house_id',
                      (new Query())->select('house_id')
                          ->from("member_house")
                          ->where(["project_house_id" => $v->house_id])])
                ->limit($num)
                ->all();

            $this->stdout("-------正在组装数据... \n");

            foreach (ArrayHelper::getColumn($notAuthHouses, "house_id") as $key => $val){

                //随机一年内的时间
                $time = rand(strtotime(date("20170101")), strtotime(date("20180630")));

                $memberData[] = [$userId, $val, 1, 2, 1, 2, $time, $time];

                $logData[] = [$userId, $val, $v->house_id, time()];
            }

            if(count($memberData) > 0){
                \Yii::$app->db->createCommand()->batchInsert(
                    'member_house',
                    ['member_id', 'house_id', 'group', 'identity', 'is_first', 'status', 'created_at', 'updated_at'],
                    $memberData
                )->execute();

                $memberData = null;
            }

            $this->stdout("-------写入{$v->house_name}完成，Sleep 2s \n");

            sleep(2);

            if(count($logData) > 0){
                \Yii::$app->logDb->createCommand()->batchInsert(
                    'auth_private_log',
                    ['member_id', 'house_id', 'project_id', 'created_at'],
                    $logData
                )->execute();

                $logData = null;
            }

            $this->stdout("-------写入{$v->house_name}记录日志完成，Sleep 2s \n");

            sleep(2);
        }

        $this->stdout("-------Finish \n");
        exit;
    }
}