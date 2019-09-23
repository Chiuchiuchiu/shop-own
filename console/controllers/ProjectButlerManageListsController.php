<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/15
 * Time: 14:22
 */

namespace console\controllers;


use apps\butler\models\Butler;
use common\models\ButlerRegion;
use common\models\House;
use common\models\HouseBillOutline;
use common\models\MemberHouse;
use common\models\PmOrder;
use common\models\Project;
use common\models\ProjectButlerManageLists;
use common\models\PropertyManagementareaReportExt;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class ProjectButlerManageListsController extends Controller
{
    protected const MAX_LIMIT = 100;

    public function actionProjectButlerManageLists()
    {
        $yesterday = strtotime('-1 days');
        $yesterday = date('Y-m-d', $yesterday);
        $startTime = $yesterday . " 00:00:00";
        $startTime = strtotime($startTime);
        $yesterdayEndTime = $yesterday . " 23:59:59";
        $yesterdayEndTime = strtotime($yesterdayEndTime);

        $projectData = Project::find()
            ->select('house_id, house_name, project_region_id')
            ->asArray()
            ->all();

        $insertData = [];
        $guard = 0;
        foreach ($projectData as $key => $projectId){

            $this->stdout("projectId：{$projectId['house_id']} \n");

            $projectButlerLists = Butler::find()->select('id, nickname, regions')
                ->where(['project_house_id' => $projectId['house_id'], 'status' => Butler::STATUS_ENABLE])
                ->andWhere(['group' => Butler::GROUP_1])
                ->asArray()->all();
            foreach ($projectButlerLists as $bKey => $bValue){

                $this->stdout("---|butlerId：{$bValue['id']} \n");

                if(isset($bValue['regions']) && !in_array($bValue['id'], \Yii::$app->params['test_butler_id'])){
                    $houseIds = explode(',', $bValue['regions']);
                    foreach ($houseIds as $k){
                        $guard++;
                        $houseRes = House::findOne($k);
                        if($houseRes){

                            $houseName = $houseRes->showName;
                            $projectName = House::find()->select('house_name')->where(['house_id' => $projectId['house_id'], 'parent_id' => 0])->asArray()->one();

                            $ids = self::getRegionHouseId($k);

                            //['日收缴户数', '总收缴户数']
                            $bill = [
                                'done' => PmOrder::find()
                                    ->where(['house_id' => $ids, 'status' => PmOrder::STATUS_PAYED])
                                    ->andFilterWhere(['BETWEEN', 'payed_at', $startTime, $yesterdayEndTime])
                                    ->count(),
                                'bill_amount' => PmOrder::find()
                                    ->where(['house_id' => $ids, 'status' => PmOrder::STATUS_PAYED])
                                    ->count(),
                            ];

                            //['日缴费额', '总缴费额']
                            $cost = [
                                'bill_day_amount' => PmOrder::find()
                                    ->where(['house_id' => $ids, 'status' => PmOrder::STATUS_PAYED])
                                    ->andFilterWhere(['BETWEEN', 'payed_at', $startTime, $yesterdayEndTime])
                                    ->sum('total_amount'),
                                'bill_total_amount' => PmOrder::find()
                                    ->where(['house_id' => $ids, 'status' => PmOrder::STATUS_PAYED])
                                    ->sum('total_amount'),
                            ];

                            //['日认证户数', '总认证户数']
                            $auth = [
                                'done' => MemberHouse::find()
                                    ->select("COUNT(DISTINCT(house_id)) AS count")
                                    ->where(['house_id' => $ids, 'status' => MemberHouse::STATUS_ACTIVE])
                                    ->andFilterWhere(['BETWEEN', 'updated_at', $startTime, $yesterdayEndTime])
                                    ->asArray()->all(),
                                'auth_amount' => MemberHouse::find()
                                    ->select("COUNT(DISTINCT(house_id)) AS count")
                                    ->where(['house_id' => $ids, 'status' => MemberHouse::STATUS_ACTIVE])
                                    ->asArray()->all(),
                            ];

                            $insertData[$guard] = [
                                'project_house_id' => $projectId['house_id'],
                                'project_region_id' => $projectId['project_region_id'],
                                'house_parent_id' => $k,
                                'butler_id' => $bValue['id'],
                                'project_name' => $projectName['house_name'],
                                'butler_name' => $bValue['nickname'],
                                'area_name' => $houseName,
                                'bill_house_day_count' => $bill['done'],
                                'bill_house_total_count' => $bill['bill_amount'],
                                'auth_count' => $auth['done'][0]['count'],
                                'auth_amount' => $auth['auth_amount'][0]['count'],
                                'bill_day_amount' => $cost['bill_day_amount'],
                                'bill_total_amount' => $cost['bill_total_amount'],
                                'house_amount' => count($ids),
                                'created_at' => $yesterdayEndTime-3600,
                            ];

                        }
                    }

                    if(is_array($insertData) && $insertData){
                        \Yii::$app->db->createCommand()->batchInsert('project_butler_manage_lists', ['project_house_id', 'project_region_id', 'house_parent_id', 'butler_id', 'project_name', 'butler_name', 'area_name', 'bill_house_day_count', 'bill_house_total_count', 'auth_count', 'auth_amount', 'bill_day_amount', 'bill_total_amount', 'house_amount', 'created_at'], $insertData)->execute();
                        $insertData = null;
                        $this->stdout("已执行：{$guard} \n");
                    }

                    $this->stdout("no data \n");
                }
            }
        }

        $this->stdout("Done \n");
        exit(1);
    }

    /**
     * 管家所管区域日数据（认证、缴费）报表扩展
     * project_butler_manage_lists 表扩展
     * @throws \yii\db\Exception
     */
    public function actionPropertyManagementareaReportExt()
    {
        $yesterday = strtotime('-1 days');
        $yesterday = date('Y-m-d', $yesterday);
        $startTime = $yesterday . " 00:00:00";
        $startTime = strtotime($startTime);
        $yesterdayEndTime = $yesterday . " 23:59:59";
        $yesterdayEndTime = strtotime($yesterdayEndTime);
        $nowTime = time();

        $model = ProjectButlerManageLists::find()
            ->where(['BETWEEN', 'created_at', $startTime, $nowTime])
            ->andWhere(['status' => 1])
            ->orderBy('id ASC');

        $index = 0;
        $insertData = [];
        foreach($model->each() as $row){
            /**
             * @var ProjectButlerManageLists $row
             */
            //house_parent_id 小于1路过
            if($row->house_parent_id < 1){
                continue;
            }

            $index++;
            $ids = self::getRegionHouseId($row->house_parent_id);

            //一条语句查出日认证房产|车位数：house_id去重
            $yesterdayMemberHouse = MemberHouse::find()
                ->select("COUNT(DISTINCT(house_id)) AS count, group")
                ->where(['house_id' => $ids, 'status' => MemberHouse::STATUS_ACTIVE])
                ->andFilterWhere(['BETWEEN', 'updated_at', $startTime, $yesterdayEndTime])
                ->groupBy('group')->asArray()->all();
            if(!empty($yesterdayMemberHouse)){
                $yesterdayAuth = ArrayHelper::map($yesterdayMemberHouse, 'group', 'count');
                $authHouseDayCount = isset($yesterdayAuth[1]) ? $yesterdayAuth[1] : 0;
                $authParkingDayCount = isset($yesterdayAuth[2]) ? $yesterdayAuth[2] : 0;
            } else {
                $authHouseDayCount = 0; //日认证房产数
                $authParkingDayCount = 0;   //日认证车位数
            }

            //所有认证房产|车位数：house_id 去重
            $allAuthData = MemberHouse::find()
                ->select("COUNT(DISTINCT(house_id)) AS count, group")
                ->where(['house_id' => $ids, 'status' => MemberHouse::STATUS_ACTIVE])
                ->groupBy('group')->asArray()->all();
            if(!empty($allAuthData)){
                $allAuth = ArrayHelper::map($allAuthData, 'group', 'count');
                $authAllHouseSum = isset($allAuth[1]) ? $allAuth[1] : 0;
                $authAllParkingSum = isset($allAuth[2]) ? $allAuth[2] : 0;
            } else {
                $authAllHouseSum = 0; //日认证房产数
                $authAllParkingSum = 0;   //日认证车位数
            }

            //日房产|车位管理费
            $yesterdayPmOrder = PmOrder::find()
                ->select("house_type, SUM(total_amount) AS total_amount")
                ->where(['house_id' => $ids, 'status' => PmOrder::STATUS_PAYED])
                ->andWhere(['BETWEEN', 'payed_at', $startTime, $yesterdayEndTime])
                ->groupBy("house_type")
                ->asArray()->all();
            if(!empty($yesterdayPmOrder)){
                $ytAmount = ArrayHelper::map($yesterdayPmOrder, 'house_type', 'total_amount');
                $billHouseDayAmount = isset($ytAmount[PmOrder::HOUSE_TYPE_HOUSE]) ? $ytAmount[PmOrder::HOUSE_TYPE_HOUSE] : 0;
                $billParkingDayAmount = isset($ytAmount[PmOrder::HOUSE_TYPE_PARKING]) ? $ytAmount[PmOrder::HOUSE_TYPE_PARKING] : 0;
            } else {
                $billHouseDayAmount = 0;
                $billParkingDayAmount = 0;
            }

            //所有房产|车位管理费
            $allPmOrder = PmOrder::find()
                ->select("house_type, SUM(total_amount) AS total_amount")
                ->where(['house_id' => $ids, 'status' => PmOrder::STATUS_PAYED])
                ->groupBy("house_type")
                ->asArray()->all();
            if(!empty($yesterdayPmOrder)){
                $allAmount = ArrayHelper::map($allPmOrder, 'house_type', 'total_amount');
                //房产
                $billAllHouseAmount = isset($allAmount[PmOrder::HOUSE_TYPE_HOUSE]) ? $allAmount[PmOrder::HOUSE_TYPE_HOUSE] : 0;
                //车位
                $billAllParkingAmount = isset($allAmount[PmOrder::HOUSE_TYPE_PARKING]) ? $allAmount[PmOrder::HOUSE_TYPE_PARKING] : 0;
            } else {
                $billAllHouseAmount = 0;
                $billAllParkingAmount = 0;
            }

            $insertData[$index] = [
                'pbml_id' => $row->id,
                'auth_house_day_count' => $authHouseDayCount,    //日认证房产数
                'auth_parking_day_count' => $authParkingDayCount,    //日认证车位数
                'auth_all_house_sum' => $authAllHouseSum, //所有认证房产数
                'auth_all_parking_sum' => $authAllParkingSum,    //所有认证车位数
                'bill_house_day_amount' => $billHouseDayAmount,   //日房产缴费额
                'bill_parking_day_amount' => $billParkingDayAmount, //日车位管理缴费额
                'bill_all_house_amount' => $billAllHouseAmount,   //所有房产缴费额
                'bill_all_parking_amount' => $billAllParkingAmount, //所有车位管理缴费额
                'created_at' => time(),
            ];

            $this->stdout("输出Index 值：{$index} \n");

            if($index > 10){
                \Yii::$app->db->createCommand()->batchInsert('property_managementarea_report_ext', ['pbml_id', 'auth_house_day_count', 'auth_parking_day_count', 'auth_all_house_sum', 'auth_all_parking_sum', 'bill_house_day_amount', 'bill_parking_day_amount', 'bill_all_house_amount', 'bill_all_parking_amount', 'created_at'], $insertData)->execute();

                $this->stdout("已达到插入数据Index 值：{$index}\n");

                $insertData = null;
                $index = 0;
            }

        }

        $this->stdout("循环层已退出，此时在最外层：{$index} \n");

        if(count($insertData) > 0){
            \Yii::$app->db->createCommand()->batchInsert('property_managementarea_report_ext', ['pbml_id', 'auth_house_day_count', 'auth_parking_day_count', 'auth_all_house_sum', 'auth_all_parking_sum', 'bill_house_day_amount', 'bill_parking_day_amount', 'bill_all_house_amount', 'bill_all_parking_amount', 'created_at'], $insertData)->execute();

            unset($insertData);
            $index = 0;
        }

        $this->stdout("Done \n");

        sleep(5);

        $this->actionPmarDiff();

        exit(1);
    }

    /**
     * 源表与扩展表认证字段统计差
     */
    public function actionPmarDiff()
    {
        $yesterday = strtotime('-1 days');
        $yesterday = date('Y-m-d', $yesterday);
        $startTime = $yesterday . " 00:00:00";
        $startTime = strtotime($startTime);
        $yesterdayEndTime = $yesterday . " 23:59:59";
        $yesterdayEndTime = strtotime($yesterdayEndTime);
        $nowTime = time();

        $model = ProjectButlerManageLists::find()
            ->where(['BETWEEN', 'created_at', $startTime, $nowTime])
            ->andWhere(['status' => 1])
            ->orderBy('id ASC');

        $index = 0;
        foreach($model->each() as $row){
            /**
             * @var ProjectButlerManageLists $row
             */
            //house_parent_id 小于1路过
            if(isset($row->propertyManagementareaReportExt)){
                $auth = $row->propertyManagementareaReportExt->auth_house_day_count + $row->propertyManagementareaReportExt->auth_parking_day_count;
                if($auth < $row->auth_count){
                    $index++;

                    $this->stdout("------Index：{$index} \n");

                    $ids = self::getRegionHouseId($row->house_parent_id);

                    //一条语句查出日认证房产|车位数：house_id去重
                    $yesterdayMemberHouse = MemberHouse::find()
                        ->select("COUNT(DISTINCT house_id) AS count, group")
                        ->where(['house_id' => $ids, 'status' => MemberHouse::STATUS_ACTIVE])
                        ->andFilterWhere(['BETWEEN', 'updated_at', $startTime, $yesterdayEndTime])
                        ->groupBy('group')->asArray()->all();
                    if(!empty($yesterdayMemberHouse)){
                        $yesterdayAuth = ArrayHelper::map($yesterdayMemberHouse, 'group', 'count');
                        $authHouseDayCount = isset($yesterdayAuth[1]) ? $yesterdayAuth[1] : 0;
                        $authParkingDayCount = isset($yesterdayAuth[2]) ? $yesterdayAuth[2] : 0;
                    } else {
                        $authHouseDayCount = 0; //日认证房产数
                        $authParkingDayCount = 0;   //日认证车位数
                    }

                    $cAuthCount = MemberHouse::find()
                        ->select("COUNT(DISTINCT(house_id)) AS count")
                        ->where(['house_id' => $ids, 'status' => MemberHouse::STATUS_ACTIVE])
                        ->andFilterWhere(['BETWEEN', 'updated_at', $startTime, $yesterdayEndTime])
                        ->asArray()->all();

                    $this->stdout("PbmlId：{$row->id} \n");
                    $this->stdout("---->{$row->auth_count} > {$auth} \n");
                    $this->stdout("---->$authHouseDayCount \n");
                    $this->stdout("---->$authParkingDayCount \n");
                    $this->stdout("---->{$cAuthCount[0]['count']} \n");

                    /*$ids = implode(',', $ids);
                    $this->stdout("{$ids} \n");
                    exit(1);*/

                    $row->auth_count = $cAuthCount[0]['count'];
                    $row->save();

                    $model = PropertyManagementareaReportExt::findOne(['id' => $row->propertyManagementareaReportExt->id]);
                    $model->auth_house_day_count = $authHouseDayCount;
                    $model->auth_parking_day_count = $authParkingDayCount;
                    $model->save();
                } else {
                    continue;
                }
            }
        }

        $this->stdout("输出Index 值：{$index} \n");

        $this->stdout("Done \n");
        exit(1);
    }

    protected static function getRegionHouseId($house_ids)
    {
        if(empty($house_ids)) return false;
        if(!is_array($house_ids)) $house_ids = [$house_ids];
        $houses=[];
        foreach($house_ids as $house_id){
            $houses[] = House::findOne($house_id);
            if(! end($houses) instanceof House){
                return false;
            }
        }

        $house_ids = [];
        foreach ($houses as $house){
            self::getAllHouseId($house,$house_ids);
        }
        $house_ids =array_unique($house_ids);
        if(sizeof($house_ids)==0) return false;

        return $house_ids;
    }

    private static function getAllHouseId(House &$house, &$house_ids)
    {
        if($house->reskind==5 || $house->reskind==11 || $house->reskind == 9) //只保存"单元"
            $house_ids[] = $house->house_id;
        if(sizeof($house->child)){
            foreach ($house->child as $child){
                self::getAllHouseId($child,$house_ids);
            }
        }
    }

    protected function butlerRegion($butlerId)
    {
        return ButlerRegion::find()->select('house_id')->where(['butler_id' => $butlerId]);
    }

}