<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/14
 * Time: 9:52
 */

namespace apps\admin\controllers;


use common\models\ButlerRegion;
use common\models\House;
use common\models\ProjectButlerManageLists;
use common\valueObject\RangDateTime;

class ButlerExportController extends Controller
{
    protected const MAX_FORKS = 10;
    protected const MAX_LIMIT = 100;

    public function actionIndex()
    {
        $dateTime = $dateTime = (new RangDateTime())->autoLoad($this->get());

        return $this->render('index', get_defined_vars());
    }

    public function actionExport()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projectData = ProjectButlerManageLists::find()
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('project_name ASC,bill_house_day_count DESC, auth_count DESC');

        $fileName = $dateTime->getStartDate() . '至'.$dateTime->getEndDate() . '管家分属区域统计.csv';
        $str = "项目名,时间,管家名,管理区域,日缴费户数,总缴费户数,日缴房产费,日缴车位管理费,日缴费额,总缴费额,日认证户数,日认证房产数,日认证车位数,已认证房产总数,已认证车位总数,已认证总户数,区域总户数\n";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . iconv ( 'utf-8', 'gbk//IGNORE', $fileName ));
        echo iconv ( 'utf-8', 'gbk//IGNORE', $str );

        foreach ($projectData->each() as $row){
            /**
             * @var $row ProjectButlerManageLists
             */
            $billHouseDayAmount = '-';
            $billParkingDayAmount = '-';
            $authHouseDayCount = '-';
            $authParkingDayCount = '-';
            $authAllHouseSum = '-';
            $authAllParkingSum = '-';

            if(isset($row->propertyManagementareaReportExt->bill_house_day_amount)){
                $billHouseDayAmount = $row->propertyManagementareaReportExt->bill_house_day_amount;
            }
            //
            if(isset($row->propertyManagementareaReportExt->bill_parking_day_amount)){
                $billParkingDayAmount = $row->propertyManagementareaReportExt->bill_parking_day_amount;
            }
            //日认证房产数
            if(isset($row->propertyManagementareaReportExt->auth_house_day_count)){
                $authHouseDayCount = $row->propertyManagementareaReportExt->auth_house_day_count;
            }
            //日认证车位数
            if(isset($row->propertyManagementareaReportExt->auth_parking_day_count)){
                $authParkingDayCount = $row->propertyManagementareaReportExt->auth_parking_day_count;
            }
            //所有已认证的房产总数
            if(isset($row->propertyManagementareaReportExt->auth_all_house_sum)){
                $authAllHouseSum = $row->propertyManagementareaReportExt->auth_all_house_sum;
            }
            //所有已认证的车位总数
            if(isset($row->propertyManagementareaReportExt->auth_all_parking_sum)){
                $authAllParkingSum = $row->propertyManagementareaReportExt->auth_all_parking_sum;
            }

            $str = implode(',', [
                $row->project_name,
                date('Y-m-d',$row->created_at),
                $row->butler_name, $row->area_name,
                $row->bill_house_day_count,
                $row->bill_house_total_count,
                $billHouseDayAmount,
                $billParkingDayAmount,
                $row->bill_day_amount ? $row->bill_day_amount : 0.00,
                $row->bill_total_amount ? $row->bill_total_amount : 0.00,
                $row->auth_count,
                $authHouseDayCount,
                $authParkingDayCount,
                $authAllHouseSum,
                $authAllParkingSum,
                $row->auth_amount,
                $row->house_amount
                    ]
                )."\n";

            echo iconv ( 'utf-8', 'gbk//IGNORE', $str );
        }

        die();
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
        if($house->reskind==5 || $house->reskind==11) //只保存"单元"
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