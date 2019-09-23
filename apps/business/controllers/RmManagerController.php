<?php

namespace apps\business\controllers;

use apps\business\service\ManagerService;
use apps\business\models\Manager;
use apps\business\valueObject\FileCache;
use apps\rm\models\RmManager;
use common\models\ProjectRegion;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class RmManagerController extends Controller
{

    public $missPermission = ['manager/login','manager/logout'];

    /**
     * Lists all Manager models.
     * @return mixed
     */
    public function actionIndex()
    {
        $groupWhere = $this->user->group_id == 1 ? [] : ['<>', 'group_id', 1];  //是否root权限

        $dataProvider = new ActiveDataProvider([
            'query' => RmManager::find()->where(['state'=>RmManager::STATE_ACTIVE])
                ->andFilterWhere($groupWhere)->orderBy('id DESC'),
        ]);

        return $this->render('index', get_defined_vars());
    }

    /**
     * Creates a new Manager model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RmManager();
        $model->scenario = RmManager::SCENARIO_CREATE_UPDATE;

        $projectRegion = $this->projectRegionCache();
        $projectRegionList = ArrayHelper::map($projectRegion, 'id', 'name');

        $searchProjectRegionList = [];
        $searchProjectRegionList[''] = '全部';
        $searchProjectRegionList['公司列表'] = $projectRegionList;

        if ($this->isPost && $model->load($this->post()) && $model->save()) {
            $this->setFlashSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', get_defined_vars());
        }
    }

    /**
     * Updates an existing Manager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $projectRegion = $this->projectRegionCache();
        $projectRegionList = ArrayHelper::map($projectRegion, 'id', 'name');

        $model = $this->findModel($id);
        $model->scenario = RmManager::SCENARIO_UPDATE;
        if ($this->isPost && $model->load($this->post()) && $model->save()) {
            $this->setFlashSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', get_defined_vars());
        }
    }

    /**
     * Finds the Manager model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RmManager the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RmManager::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDelete($id){
        if($this->isPost){
            $model = RmManager::findOne($id);
            $model->state = RmManager::STATE_DELETE;
            $model->update();
            $this->setFlashSuccess();
        }
        return $this->redirect(Url::to('/rm-manager'));
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
