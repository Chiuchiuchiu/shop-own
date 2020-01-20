<?php

namespace apps\business\controllers;

use apps\business\models\ShopManager;
use apps\business\service\ManagerService;
use apps\business\valueObject\FileCache;
use common\models\ProjectRegion;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * ManagerController implements the CRUD actions for Manager model.
 */
class ShopManagerController extends Controller
{

    public $missPermission = ['shop-manager/login','shop-manager/logout'];

    public $enableCsrfValidation = false;

    /**
     * Lists all Manager models.
     * @return mixed
     */
    public function actionIndex()
    {
        $groupWhere = $this->user->group_id == 1 ? [] : ['<>', 'group_id', 1];  //是否root权限

        $dataProvider = new ActiveDataProvider([
            'query' => Manager::find()->where(['state'=>Manager::STATE_ACTIVE])
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
        $model = new Manager();
        $model->scenario = Manager::SCENARIO_CREATE_UPDATE;
        if ($this->isPost && $model->load($this->post()) && $model->save()) {
            $this->setFlashSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', get_defined_vars());
        }
    }

    /**
     * 添加分公司人员
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionCreateRegion()
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
            return $this->render('create-region', get_defined_vars());
        }
    }

    public function actionRegion(){
        $groupWhere = $this->user->group_id == 1 ? [] : ['<>', 'group_id', 1];  //是否root权限

        $dataProvider = new ActiveDataProvider([
            'query' => RmManager::find()->where(['state'=>RmManager::STATE_ACTIVE])
                ->andFilterWhere($groupWhere)->orderBy('id DESC'),
        ]);

        return $this->render('region', get_defined_vars());
    }

    /**
     * Updates an existing Manager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Manager::SCENARIO_UPDATE;
        if ($this->isPost && $model->load($this->post()) && $model->save()) {
            $this->setFlashSuccess();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', get_defined_vars());
        }
    }

    /**
     * Updates an existing Manager model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateRegion($id)
    {
        $model = RmManager::findOne($id);

        if($model == null){
            $model = new RmManager();
        }

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
     * @return Manager the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Manager::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionLogin($redirectURL = '')
    {
        $model = new ShopManager();
        $model->scenario = ShopManager::SCENARIO_LOGIN;
        
        if (\Yii::$app->request->isPost
            && $model->load(\Yii::$app->request->post())
            && ManagerService::login($model)->isSuccess
        ) {
            $this->setFlashSuccess("登录成功", "欢迎回来," . $model->name);
            return $this->goHome();
        }

        $this->layout = 'base';
        return $this->render('login', [
            'model' => $model,
            'redirectURL' => $redirectURL
        ]);
    }

    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect('/login');
    }

    public function actionChangePassword()
    {
        $model = ShopManager::findIdentity($this->user->id);
        $model->scenario = ShopManager::SCENARIO_CHANGE_PASSWORD;
        if($this->isPost && $model->load($this->post())){
            $model->need_change_pw=ShopManager::NEED_CHANGE_PASSWORD_NO;
            if($model->save()){
                $this->setFlashSuccess("密码修改成功，你必须重新登录");
                $this->redirect(Url::to(['shop-manager/logout']));
            }
        }
        return $this->render('change-password', get_defined_vars());
    }

    public function actionDelete($id){
        if($this->isPost){
            $model = Manager::findOne($id);
            $model->state = Manager::STATE_DELETE;
            $model->update();
            $this->setFlashSuccess();
        }
        return $this->redirect(Url::to('/manager'));
    }

    public function actionDeleteRegion($id){
        if($this->isPost){
            $model = RmManager::findOne($id);
            $model->state = RmManager::STATE_DELETE;
            $model->update();
            $this->setFlashSuccess();
        }
        return $this->redirect(Url::to('/manager'));
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
