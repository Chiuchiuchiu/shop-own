<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 14:38
 */

namespace apps\admin\controllers;


use common\models\IndividualLabels;
use yii\data\ActiveDataProvider;

class IndividualLabelController extends Controller
{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = IndividualLabels::find()->orderBy('id DESC');
        $dataProvider->setSort(false);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new IndividualLabels();
        if($this->isPost && $model->load($this->post())){
            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }

            $this->setFlashErrors($model->getErrors());
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id=null)
    {
        $model = empty($id) ? new IndividualLabels() : IndividualLabels::findOne($id);
        if($this->isPost && $model->load($this->post())){
            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }

            $this->setFlashErrors($model->getErrors());
        }

        return $this->render('update', ['model' => $model]);
    }

}