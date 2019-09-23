<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/24
 * Time: 11:55
 */

namespace apps\admin\controllers;
use apps\admin\models\QuestionCategory;
use apps\admin\models\Question;
use common\valueObject\UploadObject;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class QuestionCategoryController extends Controller
{
    public function actionIndex($search=null)
    {

            $dataProvider = new ActiveDataProvider();
            $dataProvider->query = QuestionCategory::find()
                ->orderBy('created_at DESC');


            return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new QuestionCategory();
        $QuestionCategory = QuestionCategory::parentAll();
        if($this->isPost && $model->load(\Yii::$app->request->post())){
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
            'QuestionCategory'=>ArrayHelper::map(QuestionCategory::parentAll(),'id','title')
        ]);


    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('update', [
            'model' => $model,
            'QuestionCategory'=>ArrayHelper::map(QuestionCategory::parentAll(),'id','title')
        ]);
    }
    public function actionImportExcel($id){

        $model = $this->findModel($id);
        return $this->renderPartial('import-excel', [
            'model' => $model
        ]);
    }
    public function actionUploadXlsSave(){

        $uploadModel = new UploadObject();
        if($uploadModel->save('file',UploadObject::SAVE_PUBLIC_PATH)){
            $category_id = \Yii::$app->request->post('category_id');
            $ROmmt =  \Yii::getAlias('@cdnUrl/'.UploadObject::SAVE_PUBLIC_PATH.'/'.$uploadModel->getBaseFileName());
            $File = \Yii::getAlias('@root'.$ROmmt);
            $reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
            $PHPExcel = $reader->load($File); // 载入excel文件
            $excelSheet = $PHPExcel->getSheet(0); // 读取第一個工作表
            $highestRow = $excelSheet->getHighestRow(); // 取得总行数
            $highestColumm = $excelSheet->getHighestColumn(); // 取得总列数
            $insertData = [];
            for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
                $insertData[] = [
                    'title'=>$excelSheet->getCell('A'.$row)->getValue(),
                    'category_id'=>$category_id,
                    'type_isp'=>Question::TypeIsp($excelSheet->getCell('B'.$row)->getValue()),
                    'created_at'=>date('Y-m-d H:i:s'),
                ];
            }
            if(!empty($insertData)){
                $insert = \Yii::$app->db->createCommand()->batchInsert(Question::tableName(), ['title','category_id','type_isp','created_at'], $insertData)->execute();
            }
            echo '<div style="background-color: #dff0d8;border-color: #d6e9c6;color: #3c763d; text-align: center; margin-top: 15px;height: 45px; line-height: 45px; ">导入 '.count($insertData).' 题目成功，跳转中</div>';
            echo "<script>parent.AClose();</script>";
        }else{
            $this->setFlashErrors($uploadModel->getErrors());
        }
        
    }
    public function actionDelete($id)
    {
        $res = $this->findModel($id);
        $res->delete();
        return $this->backRedirect(['index']);
    }
    protected function findModel($id)
    {
        if (($model = QuestionCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}