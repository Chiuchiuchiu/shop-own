<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\business\controllers;


use apps\business\models\QuestionProject;
use apps\business\models\QuestionCategory;
use apps\business\models\Question as businessQuestion;
use common\models\AlidayuMsgLog;
use common\models\Project;
use common\models\MemberHouse;
use common\models\ProjectRegion;
use common\models\Question;
use common\models\QuestionAnswer;
use common\models\QuestionAnswerItems;
use common\models\QuestionAnswerItemsDevelop;
use common\models\QuestionItem;
use common\models\QuestionMemberDevelop;
use common\models\QuestionUserChose;
use common\models\SysSwitch;
use common\models\UploadExcelFileLog;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class QuestionProjectController extends Controller
{

    public function actionIndex($search=null)
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = QuestionProject::find()
            ->where(['type_isp'=>0])
            ->andFilterWhere(['like','title',$search])
            ->orderBy('created_at DESC');
        $CateGory = QuestionCategory::find()->select('id, title,parent_id')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('id desc')
            ->all();
        $All =[];

        foreach ($CateGory as $v){
            $All[] = ['id'=>$v['id'],
                'title'=>$v['title'],
                'List'=>QuestionCategory::List($v['id'])
                ];
        }
        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * 已提交问卷业主列表
     * @param int $id
     * @return string
     * @author zhaowenxi
     */
    public function actionAnswerList($id=0){

        $project_id = \Yii::$app->request->get('project_id');
        $project_region_id = \Yii::$app->request->get('project_region_id');

        $is_chose = \Yii::$app->request->get('is_chose');
        $keywords = \Yii::$app->request->get('keywords');
        $TopTitle ='';
        $QuestionAnswerDB = QuestionAnswer::find();
        $QuestionAnswerDB = $QuestionAnswerDB->where(['question_project_id'=>$id,'status'=>1]);
         if(isset($project_region_id) && $project_region_id<>'') {
              $ProjectRegionName = ProjectRegion::findOne(['id'=>$project_region_id]);
              $TopTitle = $ProjectRegionName->name;
            $QuestionAnswerDB = $QuestionAnswerDB->andFilterWhere(['project_region_id' => $project_region_id]);
         }
         if(isset($project_id) && $project_id>0) {
             $TopTitle = $TopTitle.' '.Project::findOne(['house_id'=>$project_id])->house_name;
             $QuestionAnswerDB = $QuestionAnswerDB->andFilterWhere(['project_house_id' => $project_id]);
         }

        if(isset($keywords) && $keywords<>'') {
              $QuestionUserIn = QuestionAnswer::find()->where(['like', 'surname', $keywords]) ->Orwhere(['like', 'ancestor_name', $keywords]) ->Orwhere(['like', 'telephone', $keywords])->select(['id'])->column();
              $QuestionAnswerDB = $QuestionAnswerDB->andWhere(['in','id',$QuestionUserIn]);
        }
        if(isset($is_chose) && $is_chose<>'') {
            if($is_chose==1){
                $QuestionAnswerDB = $QuestionAnswerDB->andWhere(['>','is_chose',0]);
            }elseif($is_chose==2){
                $QuestionAnswerDB = $QuestionAnswerDB->andWhere(['=','is_chose',0]);
            }
        }
        $QuestionAnswerDB = $QuestionAnswerDB->orderBy('created_at DESC');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $QuestionAnswerDB;
        $ProjectRegion = ProjectRegion::find()->where(['status'=>1])->orderBy('id','desc')->select(['id','name'])->asArray()->all();
        $Project =[];
        if(isset($project_region_id)){
            $Project = Project::find()->where(['status'=>1,'project_region_id'=>$project_region_id])->orderBy('house_id','desc')->select(['house_id','house_name'])->asArray()->all();
        }
        return $this->render('answer', [
            'id'=>$id,
            'ProjectRegion' => $ProjectRegion,
            'project_region_id'=>$project_region_id,
            'Project' => $Project,
            'is_chose'=>$is_chose,
            'project_id'=>$project_id,
            'dataProvider' => $dataProvider,
            'keywords'=>$keywords,
            'TopTitle'=>$TopTitle
        ]);

    }
    public function actionAnswerDel($id=0,$question_project_id=0){
        $Answer = QuestionAnswer::find()->where(['id'=>$id])->one();
        if(isset($Answer)){
            $Lib = QuestionUserChose::findOne(['answer_id'=>$id]);
            if(isset($Lib)){
                $Lib->status=1;
                $Lib->answer_id=0;
                $Lib->save();
            }
            $AnswerItem = QuestionAnswerItems::deleteAll(['question_answer_id'=>$Answer->id]);
            $Answer->delete();
            $this->redirect('/question-project/answer-list?id='.$question_project_id);
        }else{
            $this->redirect('/question-project/answer-list?id='.$question_project_id);
        }
    }
    public function actionAnswerEdit($id=0)
    {
        $AnswerItem = QuestionAnswer::findOne(['id'=>$id]);
        $ProjectRegion = ProjectRegion::find()
            ->where(['status'=>1])
            ->orderBy('id desc')
            ->select(['id','name'])
            ->asArray()
            ->all();
        $Project = Project::find()
            ->where(['project_region_id'=>$AnswerItem->project_region_id])
            ->orderBy('house_id desc')
            ->select(['house_id','house_name'])
            ->asArray()
            ->all();

        return $this->renderPartial('answer-edit', [
            'id'=>$id,
            'ProjectRegion'=>$ProjectRegion,
            'Project'=>$Project,
            'AnswerItem' => $AnswerItem
        ]);
    }

    public function actionAnswerSave()
    {

        $project_region_id = \Yii::$app->request->get('project_region_id');
        $project_id = \Yii::$app->request->get('project_id');
        $butler_id = \Yii::$app->request->get('butler_id');
        $id = \Yii::$app->request->get('id');
        $Answer = QuestionAnswer::findOne(['id'=>$id]);
        $Answer->project_region_id = $project_region_id;
        $Answer->project_house_id = $project_id;
        $Answer->butler_id = $butler_id;
        $Answer->is_chose = 0;
        $Answer->save();
        QuestionAnswerItems::updateAll(['type_isp' =>1,'project_region_id'=>$project_region_id,'project_house_id'=>$project_id], ['question_answer_id'=>$id]);

        echo '<div style="background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d; text-align: center; margin-top: 15px;height: 45px; line-height: 45px; ">编辑成功</div>';
        echo "<script>parent.AClose();</script>";
    }

    /**
     * 答题明细
     * @param int $id
     * @return string
     * @author zhaowenxi
     */
    public function actionAnswerChoose($id=0){
        $AnswerItem = QuestionAnswer::findOne(['id'=>$id]);
        $AnswerList = QuestionAnswerItems::find()->where(['question_answer_id'=>$AnswerItem->id])->orderBy('id','asc')->all();
        return $this->renderPartial('answer-choose', [
            'id'=>$id,
            'AnswerItem' => $AnswerItem,
            'AnswerList'=>$AnswerList
        ]);
    }

    /**
     * 导出已参与答题名单(已弃置，改用AnswerExport)
     * @param int $id
     * @param int $project_id
     * @param int $project_region_id
     * @param $keywords
     * @param $is_chose
     * @author zhaowenxi
     */
    public function actionAnswerExportBreak($id = 0, $project_id = 0, $project_region_id = 0, $keywords = '', $is_chose = 0){

        $topTitle = '全国';

        $questionData = QuestionAnswer::find()->where(['question_project_id' => $id, 'status'=>1]);
        
        if(isset($project_region_id) && $project_region_id) {
            $ProjectRegionName = ProjectRegion::findOne($project_region_id);
            $topTitle = $ProjectRegionName->name;
            $questionData->andFilterWhere(['project_region_id' => $project_region_id]);
        }

        if(isset($project_id) && $project_id>0) {
            $topTitle = Project::findOne(['house_id'=>$project_id])->house_name;
            $questionData->andFilterWhere(['project_house_id' => $project_id]);
        }

        if(isset($keywords) && $keywords) {

            $questionData->andFilterWhere([
                'or',
                ['like', 'telephone', $keywords],
                ['like', 'ancestor_name', $keywords],
                ['like', 'surname', $keywords],
            ]);
        }

        if(isset($is_chose) && $is_chose) {
            if($is_chose==1){
                $questionData->andWhere(['>','is_chose',0]);
            }elseif($is_chose==2){
                $questionData->andWhere(['=','is_chose',0]);
            }
        }

        $questionData = $questionData->orderBy('created_at DESC')->all();

        $Question = QuestionProject::findOne(['id'=>$id]);

        $QuestionIn = explode(',',$Question->content);

        $TopStr ='';

        $ListArr =  Question::find()
            ->where(['in','id',$QuestionIn])
            ->orderBy('id','asc')->select(['site'])->all();

        foreach ($ListArr as $v){
            $TopStr =$TopStr.','.$v->site;
        }

        $fileName = $topTitle.'详细报表.csv';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="编号,业主姓名,身份类型,手机号,物业单位,目标物业单位,所属分公司,所属项目".$TopStr.",业主原话,答题时间\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        $replysArr =[];

        foreach ($questionData as $keys=>&$row){
            $identityHouse = MemberHouse::identityHouse($row->member_house_id,$row->member_id);
            $replysFor = QuestionAnswerItems::find()->where(['question_answer_id'=>$row->id]) ->orderBy('id','asc')->select(['replys','site'])->all();

            $i=1;
            $site = '';
            foreach ($replysFor as $v){

                if($v->site){

                    $site .= $i.'、'. str_replace(["\r\n", "\r", "\n"], '', $v->site);

                    $i++;
                }
                $replysArr[] = $v->replys;

            }
            $replysStr = implode(',',$replysArr);
            $Str =  $row->id.",".
                $row->surname.",".
                $identityHouse.",".
                $row->telephone.",".
                $row->ancestor_name.",".
                $row->chose_ancestor_name.",".
                $row->project->house_name.",".
                $row->projectregion->name.",".
                $replysStr.",".
                $site.",".
                $row['created_at']."\n";

            unset($replysArr);
            echo mb_convert_encoding($Str,'GBK','UTF8');
        }
    }

    /**
     * 导出已参与答题名单
     * @param int $id
     * @param int $project_id
     * @param int $project_region_id
     * @param string $keywords
     * @param int $is_chose
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     * @author zhaowenxi
     */
    public function actionAnswerExport($id = 0, $project_id = 0, $project_region_id = 0, $keywords = '', $is_chose = 0){

        $projectName = '全国';

        $regionWhere = [];
        if(isset($project_region_id) && $project_region_id) {
            $ProjectRegionName = ProjectRegion::findOne($project_region_id);
            $projectName = $ProjectRegionName->name;
            $regionWhere = ['project_region_id' => $project_region_id];
        }

        $projectWhere = [];
        if(isset($project_id) && $project_id>0) {
            $projectName = Project::findOne(['house_id'=>$project_id])->house_name;
            $projectWhere = ['project_house_id' => $project_id];
        }

        $likeWhere = [];
        if(isset($keywords) && $keywords) {
            $likeWhere = [
                'or',
                ['like', 'telephone', $keywords],
                ['like', 'ancestor_name', $keywords],
                ['like', 'surname', $keywords],
            ];
        }

        $choseWhere = [];
        if(isset($is_chose) && $is_chose) {

            if($is_chose==1){
                $choseWhere = ['>','is_chose',0];
            }elseif($is_chose==2){
                $choseWhere = ['=','is_chose',0];
            }
        }

        $questionData = QuestionAnswer::find()
            ->where(['question_project_id' => $id, 'status' => \apps\business\models\Question::STATUS_ACTIVE])
            ->andFilterWhere($choseWhere)
            ->andFilterWhere($likeWhere)
            ->andFilterWhere($regionWhere)
            ->andFilterWhere($projectWhere)
            ->orderBy('created_at DESC')->all();

        $fileName = $projectName . "问卷调查报表.xlsx";

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
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('R')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('S')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('T')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('U')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('V')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('W')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('X')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Y')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Z')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AA')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AB')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AC')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AD')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AE')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AF')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AG')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AH')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AI')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AJ')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AK')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AL')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AM')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AN')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //合并单元格，区分住宅/写字楼
        $objPHPExcel->getActiveSheet()->mergeCells( 'B1:H1');
        $objPHPExcel->getActiveSheet()->mergeCells( 'I1:K1');
        $objPHPExcel->getActiveSheet()->mergeCells( 'L1:T1');
        $objPHPExcel->getActiveSheet()->mergeCells( 'U1:AA1');
        $objActSheet->getStyle("B1:H1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $objActSheet->getStyle("I1:K1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C5D9F1');
        $objActSheet->getStyle("L1:T1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F2DCDB');
        $objActSheet->getStyle("U1:AA1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('F79646');
        $objActSheet->setCellValue('B1', "业主信息");
        $objActSheet->setCellValue('I1', "一级指标");
        $objActSheet->setCellValue('L1', "住宅");
        $objActSheet->setCellValue('U1', "写字楼");

        //title
        $objActSheet->setCellValue('A2', '编号');
        $objActSheet->setCellValue('B2', '业主姓名');
        $objActSheet->setCellValue('C2', '身份类型');
        $objActSheet->setCellValue('D2', '手机号');
        $objActSheet->setCellValue('E2', '物业单位');
        $objActSheet->setCellValue('F2', '所属区域');
        $objActSheet->setCellValue('G2', '所属项目');
        $objActSheet->setCellValue('H2', '所属分公司');
        $objActSheet->setCellValue('AB2', '业主原话');
        $objActSheet->setCellValue('AC2', '答题时间');

        $allHeader = ['I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                       'AA','AB','AC'];

        $homeHeader = ['I','J','K','L','M','N','O','P','Q','R','S','T'];

        $officeHeader = ['I','J','K','U','V','W','X','Y','Z','AA'];

        $titleArr =  Question::find()
            ->where(['status' => \apps\business\models\Question::STATUS_ACTIVE])
            ->andWhere(['<>', 'type_id', \apps\business\models\Question::TYPE_DEVELOP])
            ->orderBy('id','asc')->select(['site'])->all();

        foreach ($titleArr as $k => $v){

            $objActSheet->setCellValue($allHeader[$k] . '2', isset($v->site) ? $v->site : '-');
        }

        $num = 3;
        foreach ($questionData as $key => $row){
            /** @var QuestionAnswer $row */
            $identityHouse = MemberHouse::identityHouse($row->member_house_id,$row->member_id);

            $replysFor = QuestionAnswerItems::find()->where(['question_answer_id'=>$row->id])
                ->orderBy('id','asc')->select(['replys','site','question_id'])->all();

            // 表格内容\单元格背景颜色
            $objActSheet->setCellValue('A' . $num, $row->id);
            $objActSheet->setCellValue('B' . $num, $row->surname);
            $objActSheet->setCellValue('C' . $num, $identityHouse);
            $objActSheet->setCellValue('D' . $num, $row->telephone);
            $objActSheet->setCellValue('E' . $num, $row->ancestor_name);
            $objActSheet->setCellValue('F' . $num, explode('->', $row->ancestor_name)[1]);
            $objActSheet->setCellValue('G' . $num, (isset($row->project->house_name) && $row->project->house_name) ? $row->project->house_name : '-');
            $objActSheet->setCellValue('H' . $num, (isset($row->projectregion->name) && $row->projectregion->name) ? $row->projectregion->name : '-');

            $headerNum = 0;
            $site = '';

            foreach ($replysFor as $v){

                /** @var QuestionAnswerItems $v */
                //写字楼与住宅在不同的格数开始
                $isOffice = SysSwitch::inVal("projectOffice", $row->project_house_id);

                $objActSheet->setCellValue(
                    $isOffice ? $officeHeader[$headerNum] . $num : $homeHeader[$headerNum] . $num,
                    $v->replys);

                $headerNum++;

                if($v->site){

                    $site .= $v->question->site . '：'. str_replace(["\r\n", "\r", "\n"], '', $v->site) . '。';

                }

                $objActSheet->setCellValue('AB' . $num, $site);
                $objActSheet->setCellValue('AC' . $num, $row->created_at);
            }

            $num++;
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
    /**
     * 添加调研计划
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionCreate()
    {
        $dateTime = (new RangDateTime());
        $dateTime->setStartDate(date('Y-m-d'));
        $dateTime->setEndDate(date('Y-m-d'));

        $model = new QuestionProject();
        if($this->isPost && $model->load(\Yii::$app->request->post())){
            $model->start_date = \Yii::$app->request->post('RangDateTime')['startDate'];
            $model->end_date = \Yii::$app->request->post('RangDateTime')['endDate'];
            $model->status = QuestionProject::STATUS_END; //默认关闭
            $model->created_at = date('Y-m-d H:i:s');
            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }
        return $this->render('create', [
            'model' => $model,
            'dateTime' => $dateTime,
        ]);
    }

    /**
     * 编辑调研问卷
     * @param $id
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dateTime = (new RangDateTime());
        $dateTime->setStartDate($model->start_date);
        $dateTime->setEndDate($model->end_date);

        if ($this->isPost && $model->load(\Yii::$app->request->post())) {


          //  var_dump(\Yii::$app->request->post('QuestionProject'));die;

         //   var_dump(\Yii::$app->request->post('RangDateTime')['startDate']);die;
            $model->start_date = \Yii::$app->request->post('RangDateTime')['startDate'];
            $model->end_date = \Yii::$app->request->post('RangDateTime')['endDate'];
            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'dateTime' => $dateTime,
        ]);
    }

    public function actionDelete($id)
    {
        $res = $this->findModel($id);
        $res->delete();
        return $this->backRedirect(['index']);
    }

    /**
     * 关闭调查问卷
     * @param $id
     * @param $status
     * @return \yii\web\Response
     * @author zhaowenxi
     */
    public function actionSetStatus($id, $status){

        //检查是否有多个计划在进行
        if($status == 2){
            $check = QuestionProject::find()->where(['status' => 1])->count();
            if($check >= 1)
                return $this->renderJsonFail("调研计划不能同时开启两个或以上");
        }

        $setStatus = $status == 1 ? 2 : 1;

        $res = QuestionProject::findOne($id);

        $res->status = $setStatus;

        if($res->save()){

            //批量修改该调研问卷下所属的楼盘项目状态
            QuestionItem::updateAll(['status' => $setStatus], ['question_id' => $id]);
        }

        return $this->renderJsonSuccess([]);
    }

    public function actionCategory($search=null)
    {
        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = QuestionProject::find()
            ->where(['parent_id'=>0])
            ->andFilterWhere(['like','title',$search])
            ->orderBy('created_at DESC');
        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);

    }
    public function actionStatistical($search=null)
    {
        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = QuestionProject::find()
            ->andFilterWhere(['like','title',$search])
            ->orderBy('created_at DESC');
        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionChoose($id=0){

        $model = $this->findModel($id);
        $Question =Question::find()->select(['id','title','type_id'])
            ->where(['status' => Project::STATUS_ACTIVE])
            ->orderBy('id','asc')->all();
        return $this->renderPartial('questionchoose', [
            'model' => $model,
            'Question'=>$Question
        ]);
    }
    public function actionChooseSave(){

//question
        if ($this->isPost && $this->post('ProjectID')) {
            $question = $this->post('question');
            $ProjectID = $this->post('ProjectID');
            $QuestionArr = implode(',',$question);

            $LibItem = QuestionProject::findOne(['id'=>$ProjectID]);
            $LibItem->content = $QuestionArr;
            $LibItem->save();
            echo '<div style="background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d; text-align: center; margin-top: 15px;height: 45px; line-height: 45px; ">编辑成功,请稍等</div>';
            echo "<script>parent.AClose();</script>";
        }
    }

    /**
     * 开发商答题者列表
     * @param null $year
     * @param null $season
     * @return string
     * @author zhaowenxi
     */
    public function actionDevelopQuestion($year = null, $season = null){

        $dateTime = new RangDateTime();

        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = QuestionMemberDevelop::find()
            ->filterWhere(['year' => $year])
            ->filterWhere(['season' => $season])
            ->orderBy('id DESC');

        return $this->render("develop-question", [
            'dataProvider' => $dataProvider,
            'dateTime' => $dateTime,
            'year' => $year,
            'season' => $season,
        ]);
    }

    /**
     * 开发商答题明细
     * @param int $id
     * @return string
     * @author zhaowenxi
     */
    public function actionDevAnswerList($id=0){
        $AnswerList = QuestionAnswerItemsDevelop::find()
            ->where(['develop_id'=>$id])
            ->orderBy('id','asc')->all();

        return $this->renderPartial('dev-answer-list', [
            'id'=>$id,
            'AnswerList'=>$AnswerList
        ]);
    }

    public function actionExportDevelopAnswer($year=null, $season=null){

        $member = QuestionMemberDevelop::find()
            ->alias('qmd')
            ->select('qmd.*')
            ->join('left join', 'question_answer_items_develop AS qaid', 'qmd.id = qaid.develop_id')
            ->filterWhere(['year' => $year])
            ->andFilterWhere(['season' => $season])
            ->orderBy('created_at DESC')->all();

        $fileName = "全国开发商、居委会、业委会问卷调查报表";

        if($year){

            $fileName .= "{$year}年";

            $season && $fileName .= "{$season}季度";
        }
        
        $fileName .= ".xlsx";

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
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('P')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Q')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('R')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('S')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('T')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('U')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('V')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('W')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('X')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Y')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('Z')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AA')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AB')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('AC')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //合并单元格，区分住宅/写字楼
        $objPHPExcel->getActiveSheet()->mergeCells( 'B1:H1');
        $objPHPExcel->getActiveSheet()->mergeCells( 'I1:AA1');

        $objActSheet->getStyle("B1:H1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $objActSheet->getStyle("I1:AA1")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('C5D9F1');

        $objActSheet->setCellValue('B1', "用户信息");
        $objActSheet->setCellValue('I1', "题目");

        //title
        $objActSheet->setCellValue('A2', '编号');
        $objActSheet->setCellValue('B2', '姓名');
        $objActSheet->setCellValue('C2', '身份类型');
        $objActSheet->setCellValue('D2', '手机号');
        $objActSheet->setCellValue('E2', '分公司');
        $objActSheet->setCellValue('F2', '所属项目');
        $objActSheet->setCellValue('G2', '职位');
        $objActSheet->setCellValue('H2', '年度/季度');
        $objActSheet->setCellValue('AB2', '评语');
        $objActSheet->setCellValue('AC2', '答题时间');

        $allHeader = ['I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA'];

        $titleArr =  Question::find()
            ->where(['status' => businessQuestion::STATUS_ACTIVE, 'type_id' => businessQuestion::TYPE_DEVELOP])
            ->orderBy('id','asc')->select(['site'])->all();

        foreach ($titleArr as $k => $v){

            $objActSheet->setCellValue($allHeader[$k] . '2', $v->site);
        }

        $num = 3;

        foreach ($member as $row){

            /** @var QuestionMemberDevelop  $row */
            $answerItems = QuestionAnswerItemsDevelop::find()->where(['develop_id'=>$row->id])
                ->orderBy('id','asc')->select(['score','site'])->all();

            // 表格内容\单元格背景颜色
            $objActSheet->setCellValue('A' . $num, $row->id);
            $objActSheet->setCellValue('B' . $num, $row->name);
            $objActSheet->setCellValue('C' . $num, QuestionMemberDevelop::TypeMap()[$row->member_type]);
            $objActSheet->setCellValue('D' . $num, $row->phone);
            $objActSheet->setCellValue('E' . $num, $row->company);
            $objActSheet->setCellValue('F' . $num, $row->project);
            $objActSheet->setCellValue('G' . $num, $row->job);
            $objActSheet->setCellValue('H' . $num, $row->year . '年/' . $row->season . '季度');

            $site = '';
            $ahNum = 0;

            /** @var QuestionAnswerItemsDevelop $v */
            foreach ($answerItems as $v){

                $objActSheet->setCellValue($allHeader[$ahNum] . $num, $v->score);

                if($v->site){

                    $site .= $allHeader[$ahNum] . '、'. str_replace(["\r\n", "\r", "\n"], '', $v->site) . '；';

                }

                $ahNum++;
            }

            $objActSheet->setCellValue('AB' . $num, $site);
            $objActSheet->setCellValue('AC' . $num, $answerItems ? date('Y-m-d H:i:s', $row->created_at) : '');

            $num++;
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

    /**
     * 导入开发商人员名单
     * @return false|string
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionImportDevExcel()
    {
        $filePath = $this->get('filePath');
        $file = '.' . str_replace('@cdnUrl', '/attached', $filePath);

        $fileType = \PHPExcel_IOFactory::identify($file);
        $excelReader = \PHPExcel_IOFactory::createReader($fileType);
        $excelSheet = $excelReader->load($file)->getSheet();
        $excelRowTotal = $excelSheet->getHighestRow();
        $excelColTotal = $excelSheet->getHighestColumn();

        $data = [];

        $num = 0;

        for($i=2; $i<=$excelRowTotal; $i++){

            $memberType = trim($excelSheet->getCell('C'.$i)->getValue());

            switch ($memberType){
                case "开发商" : $memberTypeNum = QuestionMemberDevelop::DEVELOP_TYPE;break;
                case "业委会" : $memberTypeNum = QuestionMemberDevelop::MEMBER_TYPE;break;
                case "居委会" : $memberTypeNum = QuestionMemberDevelop::NEIGHBORHOOD_TYPE;break;
                default : $memberTypeNum = 0;break;
            }

            $data[$num] = [
                'company' => trim($excelSheet->getCell('A'.$i)->getValue()),
                'project' => trim($excelSheet->getCell('B'.$i)->getValue()),
                'member_type' => $memberTypeNum,
                'name' => trim($excelSheet->getCell('D'.$i)->getValue()),
                'phone' => trim($excelSheet->getCell('E'.$i)->getValue()),
                'job' => trim($excelSheet->getCell('F'.$i)->getValue()),
                'season' => ceil(date('m')/3),
                'year' => date('Y'),
                'created_at' => time(),
            ];

            $num++;
        }

        $insert = false;
        if(!empty($data)){
            $insert = \Yii::$app->db->createCommand()->batchInsert('question_member_develop', [
                'company',
                'project',
                'member_type',
                'name',
                'phone',
                'job',
                'season',
                'year',
                'created_at',], $data)->execute();
        }

        if(!$insert){
            return $this->renderJsonFail('上传失败');
        }

        return $this->renderJsonSuccess(['dataInfo' => $excelRowTotal . '-' . $excelColTotal . $insert]);
    }

    /**
     * 开发商批量发送短信ajax
     * @return false|string
     * @author zhaowenxi
     */
    public function actionSendDevMsg(){
        if($this->isAjax){

            require(\Yii::getAlias('@components/alidayuSDK/TopSdk.php'));

            $post = $this->post();

            $members = QuestionMemberDevelop::find()
                ->filterWhere(['year' => $post['year'] ? $post['year'] : date("Y"),
                'season' => $post['season'] ? $post['season'] : ceil(date("m") / 3)])
                ->all();

            if($members){

                $i = 0;

                /** @var QuestionMemberDevelop $v */
                foreach ($members as $v){

                    $thisMember = QuestionMemberDevelop::findOne($v->id);

                    if(QuestionAnswerItemsDevelop::find()->where(['develop_id' => $v->id])->count() == 0){
                        $c = new \TopClient(\Yii::$app->params['alidayu.app'], \Yii::$app->params['alidayu.secret']);
                        $req = new \AlibabaAliqinFcSmsNumSendRequest;
                        $req->setSmsType("normal");
                        $req->setSmsFreeSignName("财到家");
                        $req->setSmsParam("{\"m\":\"" . $thisMember->getTypeText() . "\"}");
                        $req->setRecNum($v->phone);
                        $req->setSmsTemplateCode("SMS_165070924");
                        $msgRes = $c->execute($req);

                        //记录短信结果
                        if($log = $this->msgLog($v->phone, $msgRes)){
                            $thisMember->number += 1;
                            $thisMember->send_status = $log;
                            $thisMember->save();

                            $log == 1 && $i++;
                        }
                    }
                }

                return $this->renderJson(['code' => 0, 'msg' => "已发送成功{$i}条，稍后请核实发送结果"]);
            }

            return $this->renderJsonFail("没有发送名单");
        }

        return $this->renderJsonFail("非法操作");
    }

    protected function findModel($id)
    {

        if (($model = QuestionProject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 记录短信log
     * @param $phone
     * @param $req
     * @return int
     * @author zhaowenxi
     */
    protected function msgLog($phone, $req){
        $model = new AlidayuMsgLog();

        $model->status = (isset($req->result->success) && $req->result->success == 'true') ? AlidayuMsgLog::STATUS_ACTIVE : AlidayuMsgLog::STATUS_FALSE;
        $model->phone = $phone;
        $model->result = json_encode($req);

        $model->save();

        return $model->status;
    }

}