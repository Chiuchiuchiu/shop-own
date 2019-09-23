<?php

namespace apps\admin\controllers;

use common\models\SignUp;
use Yii;
use common\models\Activity;
use common\models\ProjectRegion;
use common\models\Project;
use dosamigos\qrcode\QrCode;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ActivityController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actionIndex($search=null,$id=null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Activity::find()->orderBy('id DESC')
        ]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search'=>$search,
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Activity();
        $RegionList = ProjectRegion::find()
            ->where(['status'=>1])
            ->select(['id','name'])
            ->orderBy('id asc')
            ->all();

        if($this->isPost && $model->load(Yii::$app->request->post())){
            $model->project_id = $this->post('project_id');
            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('create', [
            'model' => $model,
            'RegionList'=>$RegionList
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $RegionList = ProjectRegion::find()
            ->where(['status'=>1])
            ->select(['id','name'])
            ->orderBy('id asc')
            ->all();


        if ($this->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('update', [
            'model' => $model,
            'RegionList'=>$RegionList
        ]);
    }

    public function actionSignUpList($activity_id=0,$keywords=''){
        $TopTitle ='';
        $SignUpDB = SignUp::find();
        $Items = Activity::findOne(['id'=>$activity_id]);
        $SignUpDB = $SignUpDB->where(['activity_id'=>$activity_id ]);
        $TopTitle =Project::findOne(['house_id'=>$Items->project_id])->house_name.'项目'.$Items->title.'活动的报名信息';
        if(isset($keywords) && $keywords<>'') {
            $QuestionUserIn = SignUp::find()->where(['like', 'surname', $keywords]) ->Orwhere(['like', 'telephone', $keywords])->select(['id'])->column();
            $SignUpDB = $SignUpDB->andWhere(['in','id',$QuestionUserIn]);
        }
        $SignUpDB = $SignUpDB->orderBy('created_at DESC');
        //
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $SignUpDB;
        return $this->render('signup-list', [
            'activity_id'=>$activity_id,
            'project_id'=>$Items->project_id,
            'dataProvider' => $dataProvider,
            'keywords'=>$keywords,
            'TopTitle'=>$TopTitle
        ]);
    }


    public function actionSignUpExport($activity_id=0,$keywords=''){
        $TopTitle ='';
        $SignUpDB = SignUp::find();
        $Items = Activity::findOne(['id'=>$activity_id]);
        $SignUpDB = $SignUpDB->where(['activity_id'=>$activity_id ]);
        $TopTitle =Project::findOne(['house_id'=>$Items->project_id])->house_name.'项目'.$Items->title.'活动的报名信息';

        if(isset($keywords) && $keywords<>'') {
            $QuestionUserIn = SignUp::find()->where(['like', 'surname', $keywords]) ->Orwhere(['like', 'telephone', $keywords])->select(['id'])->column();
            $SignUpDB = $SignUpDB->andWhere(['in','id',$QuestionUserIn]);
        }
        $SignUpDB = $SignUpDB->orderBy('created_at DESC')->all();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '流水号')
            ->setCellValue('B1', '联系人姓名')
            ->setCellValue('C1', '联系人电话')
            ->setCellValue('D1', '详细信息')
            ->setCellValue('E1', '选项1')
            ->setCellValue('F1', '选项2')
            ->setCellValue('G1', '房产信息')
            ->setCellValue('H1', '报名时间');
        $i=2;
        foreach ($SignUpDB as $row){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, ' '.$row->uid)
                ->setCellValue('B'.$i, $row->surname)
                ->setCellValue('C'.$i, ' '.$row->telephone)
                ->setCellValue('D'.$i, $row->site)
                ->setCellValue('E'.$i, ' '.$row->options1)
                ->setCellValue('F'.$i, ' '.$row->options2)
                ->setCellValue('G'.$i, $row->ancestor_name)
                ->setCellValue('H'.$i, date('Y年m月d日 H点i分',$row->created_at));
            $i++;
        }

        $projectName = $TopTitle.'详细报表';
        $fileName = $TopTitle.'详细报表.csv';
        $objPHPExcel->getActiveSheet()->setTitle('活动报名详细');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    public function actionCopy($id=0){

        $Items = Activity::findOne(['id'=>$id]);
        $RegionList = ProjectRegion::find()
            ->where(['status'=>1])
            ->select(['id','name'])
            ->orderBy('id asc')
            ->all();
        return $this->renderPartial('copy', [
            'id'=>$id,
            'Items'=>$Items,
            'RegionList'=>$RegionList
        ]);
    }
    public function actionCopySave()
    {

        $ProjectID = $this->post('project_id');
        $id= $this->post('id');
        $LibItem = Activity::findOne(['id'=>$id]);
        $ActivityCount = Activity::find()->where(['pic'=>$LibItem->pic,'project_id'=>$ProjectID])->count();
        if($ActivityCount==0){
        $NewLib = new Activity();
        $NewLib->uid = date("YmdHis").mt_rand(1000000,9999999);
        $NewLib->title = $LibItem->title;
        $NewLib->pic = $LibItem->pic;
        $NewLib->bg_color = $LibItem->bg_color;
        $NewLib->btn_color = $LibItem->btn_color;
        $NewLib->btn_text = $LibItem->btn_text;
        $NewLib->comment_tag = $LibItem->comment_tag;
        $NewLib->options1 = $LibItem->options1;
        $NewLib->options2 = $LibItem->options2;
        $NewLib->site = $LibItem->site;
        $NewLib->project_id = $ProjectID;
        $NewLib->click_numbers = 0;
        $NewLib->auth_numbers = 0;
        $NewLib->status = 1;
        $NewLib->save();
        echo '<div style="background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d; text-align: center; margin-top: 15px;height: 45px; line-height: 45px; ">复制成功,请稍等</div>';
        echo "<script>parent.AClose();</script>";
        }else{
            echo '<div style="background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d; text-align: center; margin-top: 15px;height: 45px; line-height: 45px; ">您已经复制过了</div>';
            echo "<script>parent.AClose();</script>";
        }
    }
    public function actionShowQrcode($id=0){
        $Items = Activity::findOne(['id'=>$id]);
        return $this->renderPartial('show-qrcode', [
            'id'=>$id,
            'Items'=>$Items
        ]);
    }

    public function actionShowQrcodeUrl($id=0){
        $Url = 'http://' . \Yii::$app->params['domain.www'] . '/activity?id=' . $id;
        QrCode::png($Url,false,'L',9);
        die();
    }




    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = Activity::findOne($id);
        if(isset($model)){
            $model->delete();
        }
        return $this->backRedirect(['index']);
    }

    public function actionCloseType($id)
    {
        $model = Activity::findOne($id);
        if($model->status==0){
            $model->status=1;
        }else{
            $model->status=0;
        }
        $model->save();
        return $this->backRedirect(['index']);

    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
