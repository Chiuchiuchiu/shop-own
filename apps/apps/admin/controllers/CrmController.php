<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/30
 * Time: 13:56
 */

namespace apps\admin\controllers;


use apps\butler\models\Butler;
use apps\admin\valueObject\FileCache;
use common\models\ButlerRegion;
use common\models\ButlerVisitIndicators;
use common\models\House;
use common\models\Project;
use common\models\ProjectRegion;
use common\models\UploadExcelFileLog;
use common\models\VisitHouseOwner;
use common\valueObject\RangDateTime;
use components\redis\Redis;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;

class CrmController extends Controller
{
    public function actionIndex()
    {
        $butlerId = $this->get('butler_id');
        $projectId = $this->get('searchProjectId');
        $years = $this->get('years', date('Y', time()));

        $dateTime = new RangDateTime();
        $project = $this->projectCache();
        $projectList = ArrayHelper::map($project, 'house_id', 'house_name');

        $searchProjectList = [];
        $searchProjectList[''] = '全部';
        $searchProjectList['项目列表'] = $projectList;

        $butler = [
            '' => '管家',
        ];

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ButlerVisitIndicators::find()
            ->andFilterWhere(['butler_id' => $butlerId, 'project_house_id' => $projectId])
            ->andFilterWhere(['years' => $years])
            ->orderBy('id DESC');
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dateTime' => $dateTime,
            'years' => $years,
            'dataProvider' => $dataProvider,
            'butler' => $butler,
            'butlerId' => $butlerId,
            'projectList' => $projectList,
            'searchProjectList' => $searchProjectList,
            'searchProjectId' => $projectId,
        ]);
    }

    public function actionList()
    {
        $dateTime = new RangDateTime();
        $quarter = $this->get('quarter');
        $years = $this->get('years', date('Y', time()));
        $projectId = $this->get('projectId');
        $projectRegionId = $this->get('projectRegionId');

        if(empty($quarter)){
            $quarter = $this->getNowQuarter();
        }
        $searchTimes = $this->getSearchTimes($years, $quarter);

        $project = $this->projectCache();
        $projectList = ArrayHelper::map($project, 'house_id', 'house_name');

        $searchProjectList = [];
        $searchProjectList[''] = '全部';
        $searchProjectList['项目列表'] = $projectList;

        $projectRegion = $this->projectRegionCache();
        $projectRegionList = ArrayHelper::map($projectRegion, 'id', 'name');

        $searchProjectRegionList = [];
        $searchProjectRegionList[''] = '全部';
        $searchProjectRegionList['公司列表'] = $projectRegionList;

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = VisitHouseOwner::find()
            ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
            ->andFilterWhere(['BETWEEN', 'created_at', $searchTimes['startDateTime'], $searchTimes['endDateTime']])
            ->andFilterWhere(['quarter' => $quarter, 'project_house_id' => $projectId])
            ->andFilterWhere(['project_region_id' => $projectRegionId])
            ->orderBy('id DESC');
        $dataProvider->setSort(false);

        return $this->render('list', [
            'dateTime' => $dateTime,
            'quarter' => $quarter,
            'years' => $years,
            'dataProvider' => $dataProvider,
            'projectList' => $searchProjectList,
            'projectId' => $projectId,
            'projectRegionList' => $searchProjectRegionList,
            'projectRegionId' => $projectRegionId,
        ]);
    }

    public function actionCreate()
    {
        $dateTime = new RangDateTime();
        $years = $this->get('years', date('Y', time()));

        $project = $this->projectCache();
        $project = ArrayHelper::map($project, 'house_id', 'house_name');
        $projectList = [
            '' => '请选择项目',
        ];
        $projectList['项目列表'] = $project;

        $model = new ButlerVisitIndicators();
        $butler = ['选择管家'];

        if($this->isPost){
            if($model->load($this->post())){
                $exists = ButlerVisitIndicators::findOne(['butler_id' => $model->butler_id, 'years' => $model->years]);
                if($exists){
                    $this->setFlashError('该管家已设置'. $model->years . '年指标');
                } else {
                    $model->pm_manager_id = $this->user->id;
                    $model->identification = $model->years . $model->butler_id;
                    if($model->save()){
                        $this->setFlashSuccess();
                    }
                }

                return $this->backRedirect('/crm/index');
            }
        }


        return $this->render('create', [
            'projectList' => $projectList,
            'dateTime' => $dateTime,
            'years' => $years,
            'model' => $model,
            'butler' => $butler,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = ButlerVisitIndicators::findOne(['id' => $id]);

        if ($this->isPost) {
            if ($model->load($this->post()) && $model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect();
            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $res = ButlerVisitIndicators::findOne(['id' => $id]);
        if($res){
            $res->delete();
        }

        return $this->backRedirect(['index']);
    }

    //导出走访记录报表
    public function actionExportReport($years=null, $quarter=1)
    {
        $searchTime = $this->getSearchTimes($years, $quarter);
        $projectId = $this->get('projectId');
        $projectRegionId = $this->get('projectRegionId');
        $quarterFieldName = '';
        switch ($quarter){
            case 1:
                $quarterFieldName = 'the_first_quarter';
                break;
            case 2:
                $quarterFieldName = 'second_quarter';
                break;
            case 3:
                $quarterFieldName = 'third_quarter';
                break;
            default:
                $quarterFieldName = 'fourth_quarter';
                break;
        }

        $model = VisitHouseOwner::find()
            ->leftJoin('butler_visit_indicators', 'visit_house_owner.butler_id=butler_visit_indicators.butler_id')
            ->select("visit_house_owner.project_region_id,visit_house_owner.project_house_id,visit_house_owner.butler_id,visit_house_owner.quarter,butler_visit_indicators.management_number AS status,butler_visit_indicators.reside_number AS phone,butler_visit_indicators.{$quarterFieldName} AS content,COUNT(visit_house_owner.butler_id) AS created_at")
            ->where(['visit_house_owner.status' => VisitHouseOwner::STATUS_ACTIVE])
            ->andWhere(['between', 'visit_house_owner.created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
            ->andWhere(['butler_visit_indicators.years' => $years])
            ->andFilterWhere(['visit_house_owner.project_house_id' => $projectId])
            ->andFilterWhere(['visit_house_owner.project_region_id' => $projectRegionId])
            ->groupBy('visit_house_owner.butler_id,visit_house_owner.project_region_id')
            ->orderBy('visit_house_owner.project_house_id')
            ->all();

        $projectName = '所有项目';
        if(!empty($projectId)){
            $project = Project::find()->select('house_name')->where(['house_id' => $projectId])->asArray()->one();
            $projectName = $project['house_name'];
        }

        $quarterName = "{$years}年-第{$quarter}季度";
        $fileName = $projectName . "-{$quarterName}-各管家走访记录报表.xlsx";

        $objPHPExcel = new \PHPExcel();
        $objActSheet = $objPHPExcel->getActiveSheet();

        // 水平居中（位置很重要，建议在最初始位置）
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('M')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('N')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('O')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objActSheet->setCellValue('A1', '公司');
        $objActSheet->setCellValue('B1', '项目');
        $objActSheet->setCellValue('C1', '管家名');
        $objActSheet->setCellValue('D1', '管理户数');
        $objActSheet->setCellValue('E1', '入住户数');
        $objActSheet->setCellValue('F1', '季度');
        $objActSheet->setCellValue('G1', '指标');
        $objActSheet->setCellValue('H1', '以完成走访数');
        $objActSheet->setCellValue('I1', '完成率');
        $objActSheet->setCellValue('J1', '综合评价满意度');
        $objActSheet->setCellValue('K1', '报事报修满意度');
        $objActSheet->setCellValue('L1', '清洁绿化满意度');
        $objActSheet->setCellValue('M1', '管家服务满意度');
        $objActSheet->setCellValue('N1', '安全管理满意度');
        $objActSheet->setCellValue('O1', '公共设施维护管理满意度');

        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

        // 垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('M')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('N')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('O')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $k = 1;
        foreach($model as $key => $row){
            /**
             * @var $row VisitHouseOwner
             */
            $k += 1;

            //综合评价满意度
            $satisfaction = VisitHouseOwner::find()
                ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
                ->andWhere(['between', 'created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
                ->andWhere(['butler_id' => $row->butler_id])
                ->andWhere(['>', 'satisfaction', VisitHouseOwner::SATISFACTION_THIRD])
                ->count();

            //报事报修满意度
            $raSatisfaction = VisitHouseOwner::find()
                ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
                ->andWhere(['between', 'created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
                ->andWhere(['butler_id' => $row->butler_id])
                ->andWhere(['>', 'ra_satisfaction', VisitHouseOwner::SATISFACTION_THIRD])
                ->count();

            //清洁绿化满意度
            $cgSatisfaction = VisitHouseOwner::find()
                ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
                ->andWhere(['between', 'created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
                ->andWhere(['butler_id' => $row->butler_id])
                ->andWhere(['>', 'cg_satisfaction', VisitHouseOwner::SATISFACTION_THIRD])
                ->count();

            //管家服务满意度
            $bsSatisfaction = VisitHouseOwner::find()
                ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
                ->andWhere(['between', 'created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
                ->andWhere(['butler_id' => $row->butler_id])
                ->andWhere(['>', 'bs_satisfaction', VisitHouseOwner::SATISFACTION_THIRD])
                ->count();

            //安全管理满意度
            $smSatisfaction = VisitHouseOwner::find()
                ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
                ->andWhere(['between', 'created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
                ->andWhere(['butler_id' => $row->butler_id])
                ->andWhere(['>', 'sm_satisfaction', VisitHouseOwner::SATISFACTION_THIRD])
                ->count();

            //公共设施维护管理满意度
            $puSatisfaction = VisitHouseOwner::find()
                ->where(['status' => VisitHouseOwner::STATUS_ACTIVE])
                ->andWhere(['between', 'created_at', $searchTime['startDateTime'], $searchTime['endDateTime']])
                ->andWhere(['butler_id' => $row->butler_id])
                ->andWhere(['>', 'pu_satisfaction', VisitHouseOwner::SATISFACTION_THIRD])
                ->count();

            $completionRates = !empty($row->content) ? round(intval($row->created_at)/intval($row->content), 2) * 100 : 100;

            $completionRates =  round($completionRates, 2);

            //综合评价满意度占比（4、5）
            if($satisfaction > 0){
                $satisfaction = (int)(round($satisfaction/$row->created_at, 2) * 100);
                $satisfaction .= '%';
            }
            //报事报修满意度占比（4、5）
            if($raSatisfaction > 0){
                $raSatisfaction = (int)(round($raSatisfaction/$row->created_at, 2) * 100);
                $raSatisfaction .= '%';
            }
            //清洁绿化满意度占比（4、5）
            if($cgSatisfaction > 0){
                $cgSatisfaction = (int)(round($cgSatisfaction/$row->created_at, 2) * 100);
                $cgSatisfaction .= '%';
            }
            //管家服务满意度占比
            if($bsSatisfaction > 0){
                $bsSatisfaction = (int)(round($bsSatisfaction/$row->created_at, 2) * 100);
                $bsSatisfaction .= '%';
            }
            //安全管理满意度占比
            if($smSatisfaction > 0){
                $smSatisfaction = (int)(round($smSatisfaction/$row->created_at, 2) * 100);
                $smSatisfaction .= '%';
            }
            //公共设施维护管理满意度占比
            if($puSatisfaction > 0){
                $puSatisfaction = (int)(round($puSatisfaction/$row->created_at, 2) * 100);
                $puSatisfaction .= '%';
            }

            // 表格内容\单元格背景颜色
            $objActSheet->setCellValue('A' . $k, $row->projectRegionName);
            $objActSheet->setCellValue('B' . $k, $row->project->house_name);
            $objActSheet->setCellValue('C' . $k, $row->butler->nickname ?? "已删除管家（id:{$row->butler_id}）");
            $objActSheet->setCellValue('D' . $k, $row->status);
            $objActSheet->setCellValue('E' . $k, $row->phone);
            $objActSheet->setCellValue('F' . $k, $row->quarterText);

            $objActSheet->getStyle("G{$k}:G{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F79646');
            $objActSheet->setCellValue('G' . $k, $row->content);

            $objActSheet->setCellValue('H' . $k, $row->created_at);

            $objActSheet->getStyle("I{$k}:I{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            $objActSheet->setCellValue('I' . $k, $completionRates . '%');

            $objActSheet->getStyle("J{$k}:J{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C5D9F1');
            $objActSheet->setCellValue('J' . $k, $satisfaction);

            $objActSheet->getStyle("K{$k}:K{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C5D9F1');
            $objActSheet->setCellValue('K' . $k, $raSatisfaction);

            $objActSheet->getStyle("L{$k}:L{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F2DCDB');
            $objActSheet->setCellValue('L' . $k, $cgSatisfaction);

            $objActSheet->getStyle("M{$k}:M{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F2DCDB');
            $objActSheet->setCellValue('M' . $k, $bsSatisfaction);

            $objActSheet->getStyle("N{$k}:N{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F2DCDB');
            $objActSheet->setCellValue('N' . $k, $smSatisfaction);

            $objActSheet->getStyle("O{$k}:O{$k}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C5D9F1');
            $objActSheet->setCellValue('O' . $k, $puSatisfaction);

            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(35);
        }

        $fileName = mb_convert_encoding($fileName, 'GBK', 'UTF8');
        //重命名表
        $objPHPExcel->getActiveSheet()->setTitle('列表');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        unset($objPHPExcel);
        unset($objActSheet);

        die;
    }

    //导出走访记录明细
    public function actionExportSubsidiary($years=2018, $quarter=1)
    {
        $dateTime = $this->getSearchTimes($years, $quarter);
        $projectId = $this->get('projectId');
        $projectRegionId = $this->get('projectRegionId');

        $pmOrderTotal = VisitHouseOwner::find()
            ->where(
                [
                    'status' => VisitHouseOwner::STATUS_ACTIVE,
                    'quarter' => $quarter,
                ]
            )
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime['startDateTime'], $dateTime['endDateTime']])
            ->andFilterWhere(['project_house_id' => $projectId])
            ->andFilterWhere(['project_region_id' => $projectRegionId])
            ->count();

        $defaultLimit = 100;
        $pageCount = ceil($pmOrderTotal / $defaultLimit);
        $offset = 0;

        $projectName = '';
        if(!empty($projectId)){
            $project = Project::find()->select('house_name')->where(['house_id' => $projectId])->asArray()->one();
            $projectName = $project['house_name'];
        }

        $quarterName = "第{$quarter}季度";

        $fileName = $projectName . "-{$quarterName}-各管家走访记录明细.csv";

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="分公司,项目,管家,业主,业主手机号,房产,季度,综合评价满意度,报事报修满意度,清洁绿化满意度,管家服务满意度,安全管理满意度,公共设施维护管理满意度,业主意见,提交时间\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        do{
            $defaultOffset = $defaultLimit * $offset;
            $rs = VisitHouseOwner::find()
                ->where(
                    [
                        'status' => VisitHouseOwner::STATUS_ACTIVE,
                        'quarter' => $quarter,
                    ]
                )
                ->andFilterWhere(['BETWEEN', 'created_at', $dateTime['startDateTime'], $dateTime['endDateTime']])
                ->andFilterWhere(['project_house_id' => $projectId])
                ->limit($defaultLimit)
                ->offset($defaultOffset)
                ->orderBy('butler_id DESC,satisfaction DESC')->all();

            $k = 1;

            foreach($rs as $row){
                $k += 1;
                /**
                 * @var $row VisitHouseOwner
                 */
                $customerName = strtr($row->member->showName, ',', '，');
                $ancestorName = strtr($row->house->ancestor_name, ',', '，');

                $str = implode(',',[
                        $row->project->projectRegionName,
                        $row->project->house_name,
                        $row->butler->nickname,
                        $customerName,
                        $row->phone,
                        $ancestorName,
                        $quarterName,
                        $row->satisfaction . '星',
                        $row->ra_satisfaction . '星',
                        $row->cg_satisfaction . '星',
                        $row->bs_satisfaction . '星',
                        $row->sm_satisfaction . '星',
                        $row->pu_satisfaction . '星',
                        HtmlPurifier::process($row->content),
                        date('Y-m-d H:i:s', $row->created_at),
                    ])."\n";

                echo mb_convert_encoding($str,'GBK','UTF8');

            }

            ob_flush();
            flush();
            unset($objPHPExcel);
            unset($objActSheet);
            $offset++;
            $pageCount--;

        }while($pageCount >= 0);


        die();
    }

    //导出所有还没有走访业主记录的项目
    public function actionExportNoVisitToProject()
    {
        $nowYears = date('Y', time());
        $nowYears = $this->get('years', $nowYears);
        $nowQuarter = $this->getNowQuarter();
        $nowQuarter = $this->get('quarter', $nowQuarter);

        //查找所有已录入指标数据的项目
        $allProject = Project::find()->select('house_id, house_name')
            ->where(['status' => Project::STATUS_ACTIVE])
            ->asArray()
            ->all();
        $allProjectList = ArrayHelper::getColumn($allProject, 'house_id');
        $projectInfo = ArrayHelper::map($allProject, 'house_id', 'house_name');

        //查找所有已经走访记录的项目
        $visitHouseOwner = VisitHouseOwner::find()
            ->select('project_house_id')
            ->where(['years' => $nowYears, 'quarter' => $nowQuarter])
            ->groupBy('project_house_id')
            ->asArray()
            ->all();
        $visitHouseOwnerProject = ArrayHelper::getColumn($visitHouseOwner, 'project_house_id');

        //差集：没有走访记录的项目
        $diffProject = array_diff($allProjectList, $visitHouseOwnerProject);

        $fileName = "没有走访记录的项目列表.csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="项目名称,\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        foreach($diffProject as $key => $value){
            $str = implode(',',[
                    $projectInfo[$value],
                ])."\n";

            echo mb_convert_encoding($str,'GBK','UTF8');
        }

        die;
    }

    //导出已经录入指标数据但没有走访业主的项目管家列表
    public function actionExportButlerNotVisit()
    {
        $nowYears = date('Y', time());
        $nowYears = $this->get('years', $nowYears);
        $quarter = $this->getNowQuarter();
        $quarter = $this->get('quarter', $quarter);
        $projectHouseId = $this->get('projectId');

        $quarterFieldName = '';
        switch ($quarter){
            case 1:
                $quarterFieldName = 'the_first_quarter';
                break;
            case 2:
                $quarterFieldName = 'second_quarter';
                break;
            case 3:
                $quarterFieldName = 'third_quarter';
                break;
            default:
                $quarterFieldName = 'fourth_quarter';
                break;
        }

        //获取已录入指标数据的管家列表
        $butlerVisitIndicators = ButlerVisitIndicators::find()
            ->select('butler_id')
            ->where(['years' => $nowYears])
            ->andWhere(['>', $quarterFieldName, 0])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->asArray()
            ->all();
        $butlerVisitIndicatorsIds = ArrayHelper::getColumn($butlerVisitIndicators, 'butler_id');

        //获取已存在走访业主记录的管家列表
        $visitHouseOwner = VisitHouseOwner::find()
            ->select('butler_id')
            ->where(['status' => VisitHouseOwner::STATUS_ACTIVE, 'years' => $nowYears])
            ->andWhere(['quarter' => $quarter])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->groupBy('butler_id')
            ->asArray()
            ->all();
        $visitHouseOwnerIds = ArrayHelper::getColumn($visitHouseOwner, 'butler_id');
        $diffButlerId = array_diff($butlerVisitIndicatorsIds, $visitHouseOwnerIds);

        $fileName = "有指标数据但没有走访记录管家列表.csv";
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="分公司,项目名称,管家名\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        if(!empty($diffButlerId)){

            $butlerInfo = Butler::find()->where(['id' => $diffButlerId])->all();

            foreach($butlerInfo as $row){
                /**
                 * @var Butler $row
                 */
                $str = implode(',',[
                        $row->project->projectRegionName,
                        $row->project->house_name,
                        $row->nickname,
                    ])."\n";

                echo mb_convert_encoding($str,'GBK','UTF8');
            }
        }


        die;
    }

    //导出管家走访指标数据明细
    public function actionExportIndicators($searchProjectId=null, $butlerId=null)
    {
        $total = ButlerVisitIndicators::find()
            ->andFilterWhere(['butler_id' => $butlerId, 'project_house_id' => $searchProjectId])
            ->count();

        $defaultLimit = 100;
        $pageCount = ceil($total / $defaultLimit);
        $offset = 0;

        $projectName = '';
        if(!empty($projectId)){
            $project = Project::find()->select('house_name')->where(['house_id' => $projectId])->asArray()->one();
            $projectName = $project['house_name'];
        }

        $fileName = $projectName . "-各项目管家走访指标明细.csv";

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="分公司,项目,管家,管理户数,入住户数,年份,第一季度指标,第二季度指标,第三季度指标,第四季度指标\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        do{
            $defaultOffset = $defaultLimit * $offset;
            $rs = ButlerVisitIndicators::find()
                ->andFilterWhere(['butler_id' => $butlerId, 'project_house_id' => $searchProjectId])
                ->limit($defaultLimit)
                ->offset($defaultOffset)
                ->orderBy('id DESC')->all();

            $k = 1;

            foreach($rs as $row){
                $k += 1;
                /**
                 * @var $row ButlerVisitIndicators
                 */
                $str = implode(',',[
                        $row->butler->project->projectRegionName,
                        $row->butler->projectName,
                        empty($row->butlerNickName) ? '错误数据' : $row->butlerNickName,
                        $row->management_number,
                        $row->reside_number,
                        $row->years,
                        $row->the_first_quarter,
                        $row->second_quarter,
                        $row->third_quarter,
                        $row->fourth_quarter,
                    ])."\n";

                echo mb_convert_encoding($str,'GBK','UTF8');

            }

            ob_flush();
            flush();
            unset($objPHPExcel);
            unset($objActSheet);
            $offset++;
            $pageCount--;

        }while($pageCount >= 0);


        die();
    }

    /**
     * 下载导入数据模板
     * @param $projectId
     * @param $projectName
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function actionDownloadTemplate($projectId, $projectName)
    {
        $model = Butler::find()
            ->andFilterWhere(['project_house_id' => $projectId])
            ->andFilterWhere(['status' => Butler::STATUS_ENABLE, 'group' => Butler::GROUP_1])
            ->orderBy('id DESC');

        $objPHPExcel = new \PHPExcel();
        $objActSheet = $objPHPExcel->getActiveSheet();

        // 水平居中（位置很重要，建议在最初始位置）
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('I')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('J')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('K')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('L')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objActSheet->setCellValue('A1', '项目标识（不可删除）');
        $objActSheet->setCellValue('B1', '管家标识（不可更改）');
        $objActSheet->setCellValue('C1', '项目');
        $objActSheet->setCellValue('D1', '姓名（不可更改）');
        $objActSheet->setCellValue('E1', '唯一识别值（不可更改）');
        $objActSheet->setCellValue('F1', '管理户数（根据实际情况修改）');
        $objActSheet->setCellValue('G1', '入住户数');
        $objActSheet->setCellValue('H1', '年份');
        $objActSheet->setCellValue('I1', '第一季度（数值）');
        $objActSheet->setCellValue('J1', '第二季度（数值）');
        $objActSheet->setCellValue('K1', '第三季度（数值）');
        $objActSheet->setCellValue('L1', '第四季度（数值）');

        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);

        // 垂直居中
        $objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('I')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('J')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('K')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('L')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $k = 1;
        $years = date('Y', time());
        foreach($model->each() as $key => $val){
            /* @var Butler $val */
            $k += 1;
            $projectName = isset($val->project->house_name) ? $val->project->house_name : '-';
            $butlerManagementNumber = ButlerRegion::find()->where(['butler_id' => $val->id])->count();
            $butlerVist = ButlerVisitIndicators::findOne(['butler_id' => $val->id, 'years' => $years]);

            $third_quarter = '';
            $secondQuarter = '';
            $thirdQuarter = '';
            $fourthQuarter = '';

            if($butlerVist){
                $third_quarter = $butlerVist->the_first_quarter;
                $secondQuarter = $butlerVist->second_quarter;
                $thirdQuarter = $butlerVist->third_quarter;
                $fourthQuarter = $butlerVist->fourth_quarter;
            }

            // 表格内容
            $objActSheet->setCellValue('A' . $k, $projectId);
            $objActSheet->setCellValue('B' . $k, $val->id);
            $objActSheet->setCellValue('C' . $k, $projectName);
            $objActSheet->setCellValue('D' . $k, $val->nickname);
            $objActSheet->setCellValue('E' . $k, $val->wechat_user_id);
            $objActSheet->setCellValue('F' . $k, $butlerManagementNumber);
            $objActSheet->setCellValue('G' . $k, '');
            $objActSheet->setCellValue('H' . $k, $years);
            $objActSheet->setCellValue('I' . $k, $third_quarter);
            $objActSheet->setCellValue('J' . $k, $secondQuarter);
            $objActSheet->setCellValue('K' . $k, $thirdQuarter);
            $objActSheet->setCellValue('L' . $k, $fourthQuarter);

            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(35);

        }

        $fileName = "{$projectName}客户关系（走访）管家季度指标值导入模板.xlsx";
        $fileName = mb_convert_encoding($fileName, 'GBK', 'UTF8');
        //重命名表
        $objPHPExcel->getActiveSheet()->setTitle('列表');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output'); //文件通过浏览器下载

        unset($objWriter);
        unset($objPHPExcel);
        unset($objActSheet);

        die();
    }

    //导入模板数据
    public function actionImportExcel()
    {
        $filePath = $this->get('filePath');
        $projectId = $this->get('projectId');
        $file = '.' . str_replace('@cdnUrl', '/attached', $filePath);
        $quarter = $this->getNowQuarter();
        $nYears = date('Y', time());

        UploadExcelFileLog::savePath($filePath, $projectId);
        $fileType = \PHPExcel_IOFactory::identify($file);
        $excelReader = \PHPExcel_IOFactory::createReader($fileType);
        $excelSheet = $excelReader->load($file)->getSheet();
        $excelRowTotal = $excelSheet->getHighestRow();
        $excelColTotal = $excelSheet->getHighestColumn();

        $arrayIntersect = [];
        $insertData = [];
        $excelButlerIds = [];
        $projectButlerIds = ButlerVisitIndicators::find()
            ->select('butler_visit_indicators.identification AS identification')
            ->leftJoin('butler', 'butler_visit_indicators.butler_id=butler.id')
            ->where(['butler.project_house_id' => $projectId, 'butler.status' => 1])
            ->asArray()
            ->all();
        if($projectButlerIds){
            $projectButlerIds = ArrayHelper::getColumn($projectButlerIds, 'identification');
        }

        $excelProjectId = trim($excelSheet->getCell('A2')->getValue());

        if($excelProjectId != $projectId){
            return $this->renderJsonFail('导入失败，请选择与文件名对应的项目');
        }


        for($i=2; $i<=$excelRowTotal; $i++){
            $butlerId = trim($excelSheet->getCell('B'.$i)->getValue());

            if(empty($butlerId)){
                continue;
            }

            $managementNumber = trim($excelSheet->getCell('F'.$i)->getValue());
            $resideNumber = trim($excelSheet->getCell('G'.$i)->getValue());
            $years = trim($excelSheet->getCell('H'.$i)->getValue());
            $theFirstQuarter = trim($excelSheet->getCell('I'.$i)->getValue());
            $secondQuarter = trim($excelSheet->getCell('J'.$i)->getValue());
            $thirdQuarter = trim($excelSheet->getCell('K'.$i)->getValue());
            $fourthQuarter = trim($excelSheet->getCell('L'.$i)->getValue());
            $identification = $years . $butlerId;
            $excelButlerIds[$i] = $identification;

            $butlerV = ButlerVisitIndicators::findOne(['butler_id' => $butlerId, 'years' => $nYears]);
            if($butlerV){
                switch ($quarter){
                    case 2:
                        $theFirstQuarter = $butlerV->the_first_quarter;
                        break;
                    case 3:
                        $theFirstQuarter = $butlerV->the_first_quarter;
                        $secondQuarter = $butlerV->second_quarter;
                        break;
                    case 4:
                        $theFirstQuarter = $butlerV->the_first_quarter;
                        $secondQuarter = $butlerV->second_quarter;
                        $thirdQuarter = $butlerV->third_quarter;
                        break;
                }
            }

            $insertData[$i] = [
                'butler_id' => $butlerId,
                'pm_manager_id' => $this->user->id,
                'project_house_id' => $projectId,
                'management_number' => intval($managementNumber),
                'reside_number' => intval($resideNumber),
                'years' => intval($years),
                'the_first_quarter' => intval($theFirstQuarter),
                'second_quarter' => intval($secondQuarter),
                'third_quarter' => intval($thirdQuarter),
                'fourth_quarter' => intval($fourthQuarter),
                'identification' => $identification,
                'created_at' => time(),
                'updated_at' => time(),
            ];
        }

        if($projectButlerIds){
            $arrayIntersect = array_intersect($projectButlerIds, $excelButlerIds);
        }
        if($arrayIntersect){
            ButlerVisitIndicators::deleteAll(['identification' => $arrayIntersect]);
        }

        $insert = false;
        if(!empty($insertData)){
            $insert = \Yii::$app->db->createCommand()->batchInsert('butler_visit_indicators', ['butler_id', 'pm_manager_id', 'project_house_id', 'management_number', 'reside_number', 'years', 'the_first_quarter', 'second_quarter', 'third_quarter', 'fourth_quarter', 'identification','created_at', 'updated_at'], $insertData)->execute();
        }

        if(!$insert){
            return $this->renderJsonFail('上传失败');
        }

        return $this->renderJsonSuccess(['dataInfo' => $excelRowTotal . '-' . $excelColTotal . $insert]);
    }

    /**
     * 获取项目管家列表：group=1
     * @param $projectHouseId
     * @return string
     */
    public function actionGetProjectButlerList($projectHouseId)
    {
        $model = Butler::find()
            ->select('id,nickname')
            ->where(['project_house_id' => $projectHouseId, 'status' => Butler::STATUS_ENABLE])
            ->andWhere(['group' => Butler::GROUP_1])
            ->asArray()->all();

        return $this->renderJsonSuccess($model);
    }

    /**
     * 管家未走访房产列表
     * @return string
     */
    public function actionNoVisitHouse()
    {
        $data = $this->post();
        $HouseList = [];
        $houseIds = [];

        $model = House::find();

        if(count($data) < 3){
            $butler = Butler::findOne(['id' => $data['butlerId']]);
            $houseIds = explode(',', $butler->regions);
            if(empty($butler->project_house_id)){
                return $this->renderJson([]);
            }

            if(in_array($butler->project_house_id, $houseIds)){
                $houseIds = $butler->project_house_id;
                $model->where(['house_id' => $houseIds]);
            } else {
                $model->where(['house_id' => $houseIds]);
            }
        } else {
            $houseIds = $data['id'];
            $model->where(['parent_id' => $houseIds]);
        }

        $HouseList = $model
            ->select('house_id,house_name,deepest_node')
            ->orderBy('house_id ASC')
            ->all();

        $HouseArr = [];
        foreach ($HouseList as $v) {
            /**
             * @var House $v
             */
            if ($v->deepest_node == 1) {

                //检查房产是否已经存在已走访列表中
                if($this->existButlerRegionVisitHouseRedis($v->house_id)){
                    continue;
                }

                $HouseArr[] = ['id' => $v->house_id, 'name' => $v->house_name, 'isParent' => false, 'open' => true, 'deepest_node' => $v->deepest_node];
            } else {

                $childCounts = $this->IsCheckBox($v->house_id);

                if ($childCounts > 0) {
                    $name = "{$v->house_name}（{$childCounts}条记录）";

                    $HouseArr[] = ['id' => $v->house_id, 'name' => $name, 'isParent' => true, 'open' => true, 'deepest_node' => $v->deepest_node];
                } else {
                    $HouseArr[] = ['id' => $v->house_id, 'name' => $v->house_name, 'nocheck' => true, 'isParent' => true, 'deepest_node' => $v->deepest_node];
                }

            }
        }

        return $this->renderJson($HouseArr);
    }

    public function IsCheckBox($house_id)
    {
        $HouseCount = House::find()
            ->where(['deepest_node' => 1, 'parent_id' => $house_id])
            ->count();
        if ($HouseCount == 0) {
            return 0;
        } else {
            return $HouseCount;
        }
    }

    /**
     * 统计管家半年内走访过的总记录数
     * @return string
     */
    public function actionButlerViInfo()
    {
        $butlerIds = $this->get('butlerIds');
        $years = $this->get('years');
        if(empty($butlerIds)){
            return $this->renderJsonFail([]);
        }

        $quarterWhere = $this->getQuarterWhere();
        $butlerIds = explode('-', $butlerIds);
        $lists = [];
        foreach ($butlerIds as $row){
            $lists[] = [
                'c' => VisitHouseOwner::find()
                    ->where(['butler_id' => $row, 'years' => $years])
                ->andWhere(['quarter' => $quarterWhere['where']])->count(),
                'id' => $row,
            ];
        }

        return $this->renderJsonSuccess($lists);
    }

    //检查房产是否已经存在已走访房产列表中
    private function existButlerRegionVisitHouseRedis($houseId)
    {
        $prefix = 'butlerVisitRegionHouse_' . $this->user->id;
        return Redis::init()->zrank($prefix, $houseId);
    }

    /**
     * 获取现季度
     * @return int
     */
    private function getNowQuarter()
    {
        $nowMonth = date('n', time());
        $quarter = 1;

        if($nowMonth >= 10){
            $quarter = 4;
        }else if($nowMonth >= 7){
            $quarter = 3;
        }else if($nowMonth >= 4){
            $quarter = 2;
        }

        return $quarter;
    }

    private function getQuarterWhere()
    {
        $nowMonth = date('n', time());
        $quarter = [];

        if($nowMonth > 6){
            $quarter['where'] = [3, 4];
        } else {
            $quarter['where'] = [1, 2];
        }

        return $quarter;
    }

    /**
     * 根据季度获取日期
     * @param $years
     * @param $quarter
     * @return array
     */
    private function getSearchTimes($years, $quarter)
    {
        $searchStartTime = $years;
        $searchEndTime = $years;

        switch($quarter){
            case 1:
                $searchStartTime .= '-01-01 00:00:00';
                $searchEndTime .= '-03-31 23:59:59';
                break;
            case 2:
                $searchStartTime .= '-04-01 00:00:00';
                $searchEndTime .= '-06-30 23:59:59';
                break;
            case 3:
                $searchStartTime .= '-07-01 00:00:00';
                $searchEndTime .= '-09-30 23:59:59';
                break;
            case 4:
                $searchStartTime .= '-10-01 00:00:00';
                $searchEndTime .= '-12-31 23:59:59';
                break;
        }
        $searchStartTime = strtotime($searchStartTime);
        $searchEndTime = strtotime($searchEndTime);

        return [
            'startDateTime' => $searchStartTime,
            'endDateTime' => $searchEndTime,
        ];
    }

    protected function projectRegionCache($ex=7200)
    {
        $key = 'projectRegionLists';
        $projectRegionLists = FileCache::init()->get($key);
        if(empty($projectRegionLists)){
            $projectRegionLists = ProjectRegion::find()->select('id, name')->asArray()->all();
            FileCache::init()->set($key, $projectRegionLists, $ex);
        }

        return $projectRegionLists;
    }

}