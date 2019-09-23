<?php

namespace apps\business\controllers;

use common\models\OperationLog;
use common\models\ThirdpartyViewHistory;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class DataCountController extends Controller
{
    /**
     * @param int $clickPlace
     * @param string $pic
     * @return string
     * @author zhaowenxi
     */
    public function actionIndex($clickPlace = 1, $pic = null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = $this->projectCache();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ThirdpartyViewHistory::find()->select("COUNT(1) AS picGroupBy,pic")
            ->where(['click_place' => $clickPlace])
            ->andWhere(['<>', 'pic', ''])
            ->andFilterWhere(['pic' => $pic])
            ->andFilterWhere(['BETWEEN','created_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->groupBy('pic')
            ->orderBy('picGroupBy DESC');
        $dataProvider->setSort(false);

        return $this->render('index', get_defined_vars());
    }

    public function actionProjects($pic){

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ThirdpartyViewHistory::find()->select("COUNT(DISTINCT (`member_id`)) AS `memberCount`,, COUNT(1) AS `projectCount`,project_id")
            ->where(['LIKE', 'pic', $pic])
            ->andFilterWhere(['BETWEEN','created_at',$dateTime->getStartTime(),$dateTime->getEndTime()])
            ->groupBy('project_id')
            ->orderBy('projectCount DESC');
        $dataProvider->setSort(false);

        return $this->render('projects', get_defined_vars());

    }

    /**
     * 统计登录数据
     * @param null $house_id
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionMember($house_id = null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = $this->projectCache();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $startTime = strtotime($dateTime->startDate);
        $endTime = strtotime($dateTime->endDate);

        $num = (date('Y',$endTime)-date('Y',$startTime)-1)*12+(12-date('m',$startTime)+1)+date('m',$endTime);

        $model = OperationLog::find()->select("created_at, count(`member_id`) AS `totalCount`, count(DISTINCT(`member_id`)) AS `memberCount`")
            ->where(['BETWEEN','created_at',$startTime,$endTime])
            ->andWhere(['referrer' => '-'])
            ->andFilterWhere(['project_id' => $house_id]);

        $months = 0;
        for($i=0; $i<$num; $i++){
            $months++;
            $d = mktime(0,0,0,date('m',$startTime)+$i,date('d',$startTime),date('Y',$startTime));
            $iDate = date('Ym', $d);
            $isExistTable = \Yii::$app->logDb->createCommand("show tables LIKE '%operation_log_{$iDate}%'")->queryAll();
            if($isExistTable){
                $sql = "SELECT `created_at`, count(`member_id`) AS `totalCount`, count(DISTINCT(`member_id`)) AS `memberCount` FROM `operation_log_{$iDate}`
                        WHERE `referrer` = '-' AND `created_at` BETWEEN {$startTime} AND {$endTime}";
                $house_id && $sql .= " AND `project_id` = {$house_id}";
                $model = $model->union($sql);
            }
        }

        if($months > 12){
            $this->setFlashError('数据只能查询 1 年内数据');
            return $this->redirect('/data-count/member');
        }

        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = $model;
        $dataProvider->pagination = false;
        $dataProvider->sort = [
            'attributes' => [
                'created_at' => SORT_DESC,
            ]
        ];

        return $this->render('member', get_defined_vars());
    }

    public function actionLogProjects($date = null){

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $model = new OperationLog();
        $date && ($model::$table = "operation_log_" . $date);

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $model::find()
            ->select('count(1) as totalCount, count(DISTINCT(`member_id`)) AS memberCount, project_id')
            ->filterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->groupBy('project_id')
            ->orderBy('totalCount DESC');

        $dataProvider->setSort(false);
        return $this->render('log_projects', get_defined_vars());
    }

    public function actionExport($date = null){
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $model = new OperationLog();
        $date && ($model::$table = "operation_log_" . $date);

        $data = $model::find()
            ->select('count(1) as totalCount, count(DISTINCT(`member_id`)) AS memberCount, project_id')
            ->filterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->groupBy('project_id')
            ->orderBy('totalCount DESC')
            ->all();

        if($data){

            $objPHPExcel = new \PHPExcel();

            $objActSheet = $objPHPExcel->getActiveSheet();

            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objActSheet->setCellValue('A1', '所属项目');
            $objActSheet->setCellValue('B1', '点击总数');
            $objActSheet->setCellValue('C1', '点击人数');

            // 设置个表格宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);

            // 垂直居中
            $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $k = 2;

            foreach ($data as $val){
                /* @var $val OperationLog */
                $objActSheet->setCellValue('A' . $k, isset($val->project->house_name) ? $val->project->house_name : "游客（GUEST）");
                $objActSheet->setCellValue('B' . $k, $val->totalCount);
                $objActSheet->setCellValue('C' . $k, $val->memberCount);

                $k++;
            }

            $fileName = "{$date}各项目登录统计表（" . $dateTime->getStartDate() . '至' . $dateTime->getEndDate() . "）.xls";
            $fileName = mb_convert_encoding($fileName, 'GBK', 'UTF8');
            //重命名表
            $objPHPExcel->getActiveSheet()->setTitle($date);

            //设置活动单指数到第一个表,所以Excel打开这是第一个表
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output'); //文件通过浏览器下载
        }

        exit;
    }
}
