<?php

namespace apps\business\controllers;

use common\models\Banner;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class BannerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actionIndex($search=null,$id=null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Banner::find()->orderBy('id DESC')
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
        $projects = $this->projectCache();

        $selectAll = ['house_id' => 0, 'house_name' => '全选'];
        array_unshift($projects, $selectAll);
        $projects = array_combine(array_column($projects, 'house_id'), $projects);

        $model = new Banner();
        if($this->isPost && $model->load(Yii::$app->request->post())){

            $model->projects = ',' . implode(',', Yii::$app->request->post()['Banner']['projects']) . ',';

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
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
        $projects = $this->projectCache();

        $selectAll = ['house_id' => 0, 'house_name' => '全选'];
        array_unshift($projects, $selectAll);
        $projects = array_combine(array_column($projects, 'house_id'), $projects);

        $model = $this->findModel($id);

        if ($this->isPost && $model->load(Yii::$app->request->post())) {

            $model->projects = ',' . implode(',', Yii::$app->request->post()['Banner']['projects']) . ',';

            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
        ]);
    }


    public function actionDelete($id)
    {
        $model = Banner::findOne($id);
        if(isset($model)){
            $model->delete();
        }
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
        if (($model = Banner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreateUrlAjax(){

        $post = $this->post();

        if(empty($post['params'])) return $this->renderJsonFail('参数缺失');
        if(!is_numeric($post['type_num'])) return $this->renderJsonFail('类型缺失');

        $url = '';

        switch ($post['type_num']){
            case Banner::TYPE_1:
                $url = sprintf(Banner::typeMap()[$post['type_num']]['url'], trim($post['params'], "|"));
                break;

            case Banner::TYPE_2:
                $params = explode('|', $post['params']);
                $url = sprintf(Banner::typeMap()[$post['type_num']]['url'], $params[0], $params[1]);
                break;
        }

        return $this->renderJsonSuccess(['url' => $url]);
    }
}
