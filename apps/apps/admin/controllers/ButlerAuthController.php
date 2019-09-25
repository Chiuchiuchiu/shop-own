<?php

namespace apps\admin\controllers;

use common\models\ButlerAuth;
use common\models\ButlerRegion;
use common\models\House;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ButlerAuthController extends Controller
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
     * Lists all notice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $house_id = $this->get('house_id');
        $phone = trim($this->get('phone'));

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();
        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider([
            'query' => ButlerAuth::find()
                ->andFilterWhere(['project_house_id' => $house_id, 'account' => $phone])
                ->orderBy('id DESC'),
        ]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'projects' => $projectsArray,
            'phone' => $phone,
            'house_id' => $house_id,
        ]);
    }

    /**
     * Creates a new notice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();
        $projectsArray = ArrayHelper::map($projects, 'house_id', 'house_name');

        $model = new ButlerAuth();

        if ($model->load($this->post())) {

            //判断是否有传region。 zhaownexi
            $postRegion = yii::$app->request->post('ButlerAuth')['region'];

            if(!$postRegion){
                $this->setFlashErrors(['请选择管辖区域并点击右方“添加”按钮']);
                return $this->render('create', [
                    'model' => $model,
                    'projectsArray' => $projectsArray,
                ]);
            }

            if ($model->save()) {
                $butlerAuthId = $model->id;
                $regionIds = explode(',',$model->region);
                ButlerRegion::deleteAll(['butler_auth_id' => $butlerAuthId]);

                if(in_array($model->project_house_id, $regionIds)){
                    $regionIds = $model->project_house_id;
                }

                if(ButlerRegion::saveButlerRegion(0, $regionIds, $butlerAuthId)) {
                    $this->setFlashSuccess();
                    $this->backRedirect();
                } else {
                    ButlerAuth::deleteAll(['id' => $model->id]);
                    $err = !empty($model->getErrors()) ? $model->getErrors() : ['请选择管理区域'];
                    $this->setFlashErrors($err);
                }

                $this->setFlashSuccess();
                $this->backRedirect();
            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('create', [
            'model' => $model,
            'projectsArray' => $projectsArray,
        ]);
    }

    /**
     * Updates an existing notice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();
        $projectsArray = ArrayHelper::map($projects, 'house_id', 'house_name');

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $butlerAuthId = $model->id;
            $regionIds = explode(',',$model->region);

            //只有未被使用的授权号才可以更新
            if(sizeof($regionIds) && $model->status == ButlerAuth::STATUS_DEFINE){
                ButlerRegion::deleteAll(['butler_auth_id' => $butlerAuthId]);

                $saveStatus = ButlerRegion::saveButlerRegion(0, $regionIds, $butlerAuthId);
                if($saveStatus){
                    $this->setFlashSuccess();
                    return $this->redirect(['index']);
                }else{
                    $this->setFlashError("管理区域保存失败");
                }
            } else {
                $this->setFlashError('仅可更新未被使用的授权号！');
            }

            return $this->backRedirect();
        } else {
            return $this->render('update', [
                'model' => $model,
                'projectsArray' => $projectsArray,
            ]);
        }
    }

    public function actionDelete($id)
    {
        $model = ButlerAuth::findOne(['id' => $id]);
        if($model){

            ButlerRegion::deleteAll(['butler_auth_id' => $id]);

            $model->delete();
            $this->setFlashSuccess();
        } else {
            $this->setFlashError('无法找到对应数据');
        }

        return $this->backRedirect();
    }

    /**
     * Finds the notice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ButlerAuth the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ButlerAuth::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
