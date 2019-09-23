<?php

namespace apps\business\controllers;

use apps\business\models\RBAC;
use Yii;
use apps\business\models\ManagerGroup;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * GroupController implements the CRUD actions for ManagerGroup model.
 */
class GroupController extends Controller
{

    /**
     * Lists all ManagerGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ManagerGroup::find()->orderBy('id DESC')->where(['>','id',$this->user->group_id]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ManagerGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ManagerGroup();
        $model->scenario = ManagerGroup::SCENARIO_CREATE;
        $rbac = RBAC::find()->where(['state' => RBAC::STATE_ACTIVE, 'parent_id' => 0])->orderBy('order_id ASC')->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {

            return $this->render('create', [
                'model' => $model,
                'rbac' => $rbac,
                'userGroup' => unserialize($this->user->group->permission),
            ]);
        }
    }

    /**
     * Updates an existing ManagerGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = ManagerGroup::SCENARIO_UPDATE;
        $rbac = RBAC::find()->where(['state' => RBAC::STATE_ACTIVE, 'parent_id' => 0])->orderBy('order_id ASC')->all();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save())
                return $this->redirect(['index', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'rbac' => $rbac,
            'userGroup' => unserialize($this->user->group->permission),
        ]);

    }

    /**
     * Finds the ManagerGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ManagerGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if ($id!=ManagerGroup::GROUP_ROOT && ($model = ManagerGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
