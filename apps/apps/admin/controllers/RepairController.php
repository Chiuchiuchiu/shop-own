<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/5/6
 * Time: 15:46
 */

namespace apps\admin\controllers;


use apps\butler\models\Butler;
use apps\butler\models\RepairResponse;
use common\models\House;
use common\models\Project;
use common\models\Repair;
use common\models\RepairHold;
use common\valueObject\RangDateTime;
use components\newWindow\NewWindow;
use components\wechatSDK\QYWechatSDK;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class RepairController extends Controller
{
    public function actionIndex($status=null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id', null);
        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');


        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Repair::find()
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('created_at DESC');

        $dataProvider->setSort(false);

        return $this->render('index', get_defined_vars());
    }

    public function actionDetail($id=null)
    {
        $model = Repair::findOne($id);

        return $this->render('detail', get_defined_vars());
    }

    public function actionSearchStatus()
    {
        if($this->isPost && $this->isAjax){
            $id = $this->post('id');
            if($id){
                $model = Repair::findOne(['id' => $id]);
                if (!$model) {
                    return $this->renderJsonFail('');
                }
                $dataArray = [];

                if (!in_array($model->status,[Repair::STATUS_WAIT, Repair::STATUS_CANCEL, Repair::STATUS_HOLD]) && $model->repairResponse->business_id > 0) {
                    if ($model->status != Repair::STATUS_EVALUATED) {
                        $res = (new NewWindow())->getRepairDetail($model->repairResponse->business_id);

                        if (isset($res['Response']['Data']['Record'][0]['ServiceState'])) {
                            if($res['Response']['Data']['Record'][0]['ServiceState'] > 0){
                                $model->status = $res['Response']['Data']['Record'][0]['ServiceState'];
                                $model->save();
                            }
                        }

                        if (isset($res['Response']['Data']['Record']))
                            $dataArray = $res['Response']['Data']['Record'];

                        $levelName = isset($dataArray[0]['LevelName']) ? $dataArray[0]['LevelName'] : '';

                        $this->saveRepairResponse($model->id, $res['Response']['Data']['NWRespCode'], $res['Response']['Data']['NWErrMsg'], $res['Response']['Data']['Record'], $levelName, $res['Response']['Data']);

                        return $this->renderJsonSuccess('');
                    }
                }

                return $this->renderJsonFail('暂无法查询');
            }

        }

        return $this->renderJsonFail('');
    }

    /**
     * 暂挂列表
     * @param null $status
     * @return string
     * @author zhaowenxi
     */
    public function actionHold($status=null)
    {
        $house_id       = $this->get('house_id', null);
        $projectsArray  = $this->projectCache();
        $dateTime       = (new RangDateTime())->autoLoad($this->get());

        $projects = [];
        $projects[''] = '全部';
        $projects['项目列表'] = ArrayHelper::map($projectsArray, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = RepairHold::find()
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('id DESC');

        $dataProvider->setSort(false);

        return $this->render('hold', get_defined_vars());
    }

    /**
     * 审核暂挂
     * @param $id
     * @param $status
     * @return false|string
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionSetStatus($id, $status){

        $res = RepairHold::findOne($id);

        if(!$res || $res->status != 1) return $this->renderJsonFail("操作失败！");

        $transaction = \Yii::$app->db->beginTransaction();

        try{
            $res->status = $status;
            $res->updated_at = time();

            if(!$res->save()) throw new \Exception('审核失败！(err:1)');

            if($status == 2){

                $repairInfo = Repair::findOne($res->repair_id);

                $repairInfo->status = Repair::STATUS_HOLD;
                $repairInfo->updated_at = time();

                if(!$repairInfo->save()) throw new \Exception('审核失败！(err:2)');
            }

            $this->notificationButler($res);

            $transaction->commit();

            return $this->renderJsonSuccess([]);

        }catch (\Exception $e){

            $transaction->rollBack();

            return $this->renderJsonFail($e->getMessage());
        }
    }

    /**
     * 发送通知管家
     * @param RepairHold $repairHold
     * @param string $msgtype
     * @param int $agentid
     * @return bool|array
     */
    private function notificationButler(RepairHold $repairHold, string $msgtype='text',int $agentid=1000002)
    {
        $butlerModel = Butler::findOne($repairHold->butler_id);

        if($butlerModel){

            $updated = date('Y-m-d H:i', $repairHold->updated_at);

            switch ($repairHold->status){
                case RepairHold::STATUS_YES: $statusStr = "审核通过";break;
                case RepairHold::STATUS_NO: $statusStr = "审核不通过，请继续跟进处理";break;
                default: $statusStr = "未知";break;
            }

            $data = [
                'touser' => $butlerModel->wechat_user_id,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' =>
                        "暂挂审批通知：\n状态【{$statusStr}】，\n审批日期【{$updated}】，\n审核人【财到家admin】，\n暂挂原因【{$repairHold->content}】，\n报事内容【{$repairHold->repair->content}】，\n业主姓名【{$repairHold->repair->name}】\n业主电话【{$repairHold->repair->tel}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);

            $res = $wechatQYSDK->sendMsg($data);

            return $res;
        }

        return 0;
    }

    public function actionStatistics()
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

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Project::find()
            ->leftJoin([])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->orderBy('created_at DESC');

        $dataProvider->setSort(false);

        return $this->render('statistics', get_defined_vars());
    }

    /**
     * 导出汇总数据
     * @author zhaowenxi
     */
    public function actionExportCollect(){

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projectHouseId = $this->get('house_id', null);

        $status = $this->get('status', null);
        $statusWhere = $status ? ['r.status' => $status] : ['NOT IN', 'r.status', [Repair::STATUS_HOLD, Repair::STATUS_CANCEL, 4]];

        $startTime = date('Ym', $dateTime->getStartTime());
        $endTime = date('Ym', $dateTime->getEndTime());
        $differTime = $endTime - $startTime;

        if($differTime > 3){
            $this->setFlashError('数据导出只能跨 3 个月');

            return $this->redirect('/repair');
        }

        $objPHPExcel = new \PHPExcel();

        $projectInfo = Project::find()->select("project.house_id,project_region.name,project.house_name")
            ->leftJoin('project_region', 'project.project_region_id = project_region.id')
            ->asArray()->all();

        $projects = array_combine(array_column($projectInfo, 'house_id'), $projectInfo);

        $model = Repair::find()
            ->select('r.house_id,r.status,r.project_house_id,hr.parent_ids,flow_style_id')
            ->alias('r')
            ->leftJoin('house_relevance AS hr', 'hr.house_id = r.house_id')
            ->andFilterWhere(['BETWEEN', 'r.created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['r.project_house_id' => $projectHouseId])
            ->andFilterWhere($statusWhere)
            ->orderBy('r.id DESC')->asArray()->all();

        $inputData = [];

        foreach ($model as $v){

            $area = explode(',', trim($v['parent_ids'], ','));

            if(isset($area[1])){

                //要区分投诉和维修
                if(in_array($v['status'], [Repair::STATUS_EVALUATED, Repair::STATUS_CANCEL])){
                    if($v['flow_style_id'] == 'w'){
                        $inputData[$area[1]]['repairFinish'] = isset($inputData[$area[1]]['repairFinish']) ? $inputData[$area[1]]['repairFinish'] + 1 : 1;
                    }else{
                        $inputData[$area[1]]['complaintFinish'] = isset($inputData[$area[1]]['complaintFinish']) ? $inputData[$area[1]]['complaintFinish'] + 1 : 1;
                    }

                }else{
                    if($v['flow_style_id'] == 'w'){
                        $inputData[$area[1]]['repairNotFinish'] = isset($inputData[$area[1]]['repairNotFinish']) ? $inputData[$area[1]]['repairNotFinish'] + 1 : 1;
                    }else{
                        $inputData[$area[1]]['complaintNotFinish'] = isset($inputData[$area[1]]['complaintNotFinish']) ? $inputData[$area[1]]['complaintNotFinish'] + 1 : 1;
                    }
                }

                if($v['flow_style_id'] == 'w'){
                    $inputData[$area[1]]['repairTotal'] = isset($inputData[$area[1]]['repairTotal']) ? $inputData[$area[1]]['repairTotal'] + 1 : 1;
                }else{
                    $inputData[$area[1]]['complaintTotal'] = isset($inputData[$area[1]]['complaintTotal']) ? $inputData[$area[1]]['complaintTotal'] + 1 : 1;
                }

                $inputData[$area[1]]['project_name'] = $projects[$v['project_house_id']]['house_name'];
                $inputData[$area[1]]['area_id'] = House::findOne($area[1])->house_name;
                $inputData[$area[1]]['project_id'] = $v['project_house_id'];

            }
        }

        array_multisort(array_column($inputData, 'project_id'), SORT_ASC, array_column($inputData, 'area_id'), SORT_ASC, $inputData);

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

        $objActSheet->setCellValue('A1', '分公司');
        $objActSheet->setCellValue('B1', '项目');
        $objActSheet->setCellValue('C1', '区域');
        $objActSheet->setCellValue('D1', '维修总条数');
        $objActSheet->setCellValue('E1', '维修已完成');
        $objActSheet->setCellValue('F1', '维修未完成');
        $objActSheet->setCellValue('G1', '维修完成率');
        $objActSheet->setCellValue('H1', '投诉总条数');
        $objActSheet->setCellValue('I1', '投诉已完成');
        $objActSheet->setCellValue('J1', '投诉未完成');
        $objActSheet->setCellValue('K1', '投诉完成率');

        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);

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

        $k = 2;

        foreach ($inputData AS $val){

            $repairTotal = $val['repairTotal'] ?? 0;
            $repairFinish = isset($val['repairFinish']) ? $val['repairFinish'] : 0;
            $repairNotFinish = isset($val['repairNotFinish']) ? $val['repairNotFinish'] : 0;

            $complaintTotal = $val['complaintTotal'] ?? 0;
            $complaintFinish = isset($val['complaintFinish']) ? $val['complaintFinish'] : 0;
            $complaintNotFinish = isset($val['complaintNotFinish']) ? $val['complaintNotFinish'] : 0;

            $objActSheet->setCellValue('A' . $k, $projects[$val['project_id']]['name']);
            $objActSheet->setCellValue('B' . $k, $val['project_name']);
            $objActSheet->setCellValue('C' . $k, $val['area_id']);
            $objActSheet->setCellValue('D' . $k, $repairTotal);
            $objActSheet->setCellValue('E' . $k, $repairFinish);
            $objActSheet->setCellValue('F' . $k, $repairNotFinish);
            $objActSheet->setCellValue('G' . $k, $repairTotal == 0 ? "0%" : round($repairFinish / $repairTotal * 100) . '%');
            $objActSheet->setCellValue('H' . $k, $complaintTotal);
            $objActSheet->setCellValue('I' . $k, $complaintFinish);
            $objActSheet->setCellValue('J' . $k, $complaintNotFinish);
            $objActSheet->setCellValue('K' . $k, $complaintTotal == 0 ? "0%" : round($complaintFinish / $complaintTotal * 100) . '%');

            $k++;
        }

        unset($inputData);
        unset($model);

        $fileName = $dateTime->getStartDate() . '--' . $dateTime->getEndDate();
        $fileName .= "报事汇总.xls";
        $fileName = mb_convert_encoding($fileName, 'GBK', 'UTF8');
        //重命名表
        $objPHPExcel->getActiveSheet()->setTitle('列表');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载

        die();

    }

    public function actionExport()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $projectHouseId = $this->get('house_id', null);
        $status = $this->get('status', null);

        $startTime = date('Ym', $dateTime->getStartTime());
        $endTime = date('Ym', $dateTime->getEndTime());
        $differTime = $endTime - $startTime;

        if($differTime > 5){
            $this->setFlashError('数据导出只能跨 5 个月');

            return $this->redirect('/repair');
        }

        $objPHPExcel = new \PHPExcel();

        $model = Repair::find()
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $projectHouseId])
            ->andFilterWhere(['status' => $status])
            ->orderBy('id DESC');

//        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

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

        $objActSheet->setCellValue('A1', 'ID');
        $objActSheet->setCellValue('B1', '项目');
        $objActSheet->setCellValue('C1', '客户名');
        $objActSheet->setCellValue('D1', '联系人电话');
        $objActSheet->setCellValue('E1', '报事状态');
        $objActSheet->setCellValue('F1', '投诉/报修内容');
        $objActSheet->setCellValue('G1', '类型');
        $objActSheet->setCellValue('H1', '更新时间');
        $objActSheet->setCellValue('I1', '提交时间');
        $objActSheet->setCellValue('J1', '所属区域');
        $objActSheet->setCellValue('K1', '住址');
        $objActSheet->setCellValue('L1', '及时性（1-5分）');
        $objActSheet->setCellValue('M1', '满意度（1-5分）');
        $objActSheet->setCellValue('N1', '评语');

        // 设置个表格宽度
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(50);

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

        $k = 1;
        foreach($model->each() as $key => $val){
            /* @var $val Repair */
            $k += 1;
            $projectName = isset($val->house->project) ? $val->house->project->house_name : '-';

            // 表格内容
            $objActSheet->setCellValue('A' . $k, $val->id);
            $objActSheet->setCellValue('B' . $k, $projectName);
            $objActSheet->setCellValue('C' . $k, str_replace('//s*/', '', self::emojiFilter($val->name)));
            $objActSheet->setCellValue('D' . $k, $val->tel);
            $objActSheet->setCellValue('E' . $k, $val->statusText);
            $objActSheet->setCellValue('F' . $k, str_replace('//s*/', '', self::emojiFilter(trim($val->content))));
            $objActSheet->setCellValue('G' . $k, $val->flowStyleText);
            $objActSheet->setCellValue('H' . $k, date('Y-m-d H:i', $val->updated_at));
            $objActSheet->setCellValue('I' . $k, date('Y-m-d H:i', $val->created_at));
            $objActSheet->setCellValue('J' . $k, explode('->', $val->address)[1] ?? '-');
            $objActSheet->setCellValue('K' . $k, $val->house->ancestor_name);
            $objActSheet->setCellValue('L' . $k, $val->repairCustomerEvaluation->timeliness ?? '-');
            $objActSheet->setCellValue('M' . $k, $val->repairCustomerEvaluation->satisfaction ?? '-');
            $objActSheet->setCellValue('N' . $k, isset($val->repairCustomerEvaluation->customer_idea) ? str_replace('//s*/', '', self::emojiFilter($val->repairCustomerEvaluation->customer_idea)) : '-');

//            //20190408部分图片太大会影响性能，估取消 zhaowenxi
//            // 图片生成
//            if(!empty($val->pics)){
//                $picArray = explode(',', $val->pics);
//                foreach($picArray as $pPey => $pValue){
//                    $Coordinates = 'I' . $k;
//                    switch($pPey){
//                        case 1:
//                            $Coordinates = 'J' . $k;
//                            break;
//                        case 2:
//                            $Coordinates = 'K' . $k;
//                            break;
//                    }
//                    $pPath = str_replace('@cdnUrl', './attached', $pValue);
//
//                    $objDrawing = new \PHPExcel_Worksheet_Drawing();
//                    $objDrawing->setPath($pPath);
//                    $objDrawing->setHeight(80);//照片高度
//                    $objDrawing->setWidth(80); //照片宽度
//                    $objDrawing->setCoordinates($Coordinates);
//                    $objDrawing->setOffsetX(12);
//                    $objDrawing->setOffsetY(12);
//                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
//                }
//            }

            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(80);

        }

        $fileName = $dateTime->getStartDate() . '——' . $dateTime->getEndDate();
        $fileName .= "报事信息表.xls";
        $fileName = mb_convert_encoding($fileName, 'GBK', 'UTF8');
        //重命名表
         $objPHPExcel->getActiveSheet()->setTitle('列表');

        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载

        die();
    }

    /**
     * 获取报事报修详情（新视察），反馈回来的内容写入到 repairResponse 表
     * @param $repairId
     * @param $code
     * @param $errorMsg
     * @param $responseData
     * @param string $levelName
     * @param array $result
     * @return bool
     */
    protected function saveRepairResponse($repairId, $code, $errorMsg, $responseData, $levelName = '', $result = [])
    {
        $model = RepairResponse::findOrCreate($repairId);

        $model->code = $code;
        $model->error_msg = $errorMsg;
        $model->response_data = serialize($responseData);
        $model->level_name = $levelName;
        $model->service_state = isset($result['Record'][0]['ServiceState']) ? $result['Record'][0]['ServiceState'] : 0;

        return $model->save();
    }

    protected static function emojiFilter($text){//过滤emoji表情符号
        $text = json_encode($text);
        preg_match_all("/(\\\\ud83c\\\\u[0-9a-f]{4})|(\\\\ud83d\\\u[0-9a-f]{4})|(\\\\u[0-9a-f]{4})/", $text, $matchs);
        if(!isset($matchs[0][0])) { return json_decode($text, true); }
        $emoji = $matchs[0];
        foreach($emoji as $ec) {
            $hex = substr($ec, -4);
            if(strlen($ec)==6) {
                if($hex>='2600' and $hex<='27ff') {
                    $text = str_replace($ec, '', $text);
                }
            } else {
                if($hex>='dc00' and $hex<='dfff') {
                    $text = str_replace($ec, '', $text);
                }
            }
        }
        return json_decode($text, true);
    }
}