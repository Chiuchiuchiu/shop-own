<?php

namespace apps\business\controllers;

use common\models\ArticleCategory;
use Yii;
use common\models\Article;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param null $search
     * @return string
     * Description:
     */
    public function actionIndex($search=null,$id=null)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Article::find()
            ->where(['project_id'=>0])
                ->andWhere(['!=','status',Article::STATUS_DELETE])
            ->orderBy('post_at DESC')
            ->andFilterWhere(['like','title',$search])
            ->andFilterWhere(['category_id'=>$id])
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
        $model = new Article();
        if($this->isPost && $model->load(Yii::$app->request->post())){
            $model->project_id = 0;
            $model->post_at = strtotime($this->post('dateTime'));
            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('create', [
            'model' => $model,
            'categoryMap'=>ArrayHelper::map(ArticleCategory::findAll([
                'status'=>ArticleCategory::STATUS_ACTIVE,
                'project_id'=>0
            ]),'id','name')
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
        if ($this->isPost && $model->load(Yii::$app->request->post())) {
            $model->project_id = 0;
            $model->post_at = strtotime($this->post('dateTime'));
            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('update', [
            'model' => $model,
            'categoryMap'=>ArrayHelper::map(ArticleCategory::findAll([
                'status'=>ArticleCategory::STATUS_ACTIVE,
            ]),'id','name')
        ]);
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $res = $this->findModel($id);
        $res->status = Article::STATUS_DELETE;
        $res->save();
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
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
