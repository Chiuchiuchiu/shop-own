<?php

namespace apps\business\controllers;

use common\models\ButlerAuth;
use common\models\ButlerRegion;
use common\models\ButlerVisitIndicators;
use common\models\House;
use common\models\Project;
use common\models\VisitHouseOwner;
use Yii;
use common\models\Butler;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ButlerController implements the CRUD actions for Butler model.
 */
class ButlerController extends Controller
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
     * Lists all Butler models.
     * @return mixed
     */
    public function actionIndex()
    {
        $status = $this->get('status');
        $group = $this->get('group');
        $house_id = $this->get('house_id');
        $butler_name = trim($this->get('bName', null));

        $projects = $this->projectCache();

        $dataProvider = new ActiveDataProvider([
            'query' => Butler::find()->andFilterWhere(['project_house_id' => $house_id])
                ->andFilterWhere(['nickname' => $butler_name])
                ->andFilterWhere(['status' => $status, 'group' => $group])
                ->orderBy('id DESC'),
        ]);
        $dataProvider->setSort(false);

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');
        $butlerGroupList = Butler::groupMap();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'projects' => $projectsArray,
            'house_id' => $house_id,
            'butler_name' => $butler_name,
            'status' => $status,
            'group' => $group,
            'butlerGroupList' => $butlerGroupList,
        ]);
    }

    /**
     * Creates a new Butler model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new Butler();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['index']);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = $this->post();

        $projects = $this->projectCache();
        $projectsArray = [];
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        if ($model->load($post)) {
            $model->project_house_id = $post['Butler']['project_house_id'];

            if(!Yii::$app->request->post('Butler')['regions']){
                $this->setFlashError("请选择管辖区域并点击‘添加’");
                return $this->redirect(['update', "id" => $id]);
            }

            if($model->save()){
                $regionIds = explode(',',$model->regions);

                if(sizeof($regionIds)){

                    $butlerAuthId = 0;
                    if(isset($model->butlerAuth->used_to)){
                        $butlerAuthId = $model->butlerAuth->id;
                    }

                    $saveRegion = ButlerRegion::saveButlerRegion($model->id, $regionIds, $butlerAuthId, true);
                    if($saveRegion){

                        $model->mana_number = count($saveRegion);
                        $model->save();

                        //更新管家走访业主指标：项目值
                        ButlerVisitIndicators::updateAll(['project_house_id' => $model->project_house_id], ['butler_id' => $id]);

                        $this->setFlashSuccess();
                    }else{
                        $this->setFlashError("管理区域保存失败");
                    }
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
                'model' => $model,
                'projectsArray' => $projectsArray,
            ]);
    }

    /**
     * Deletes an existing Butler model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if(!empty($model->status)){
            $this->setFlashError('仅可删除已作废账号');
        } else {
            $butlerAuth = ButlerAuth::findOne(['used_to' => $id]);
            if($butlerAuth){
                $butlerAuth->delete();
            }

            /*VisitHouseOwner::deleteAll(['butler_id' => $id, 'project_house_id' => $model->project_house_id]);
                        ButlerVisitIndicators::deleteAll(['butler_id' => $id, 'project_house_id' => $model->project_house_id]);*/

//            $model->delete();
            $this->setFlashSuccess();
        }

        return $this->redirect(['index']);
    }

    /**
     * 更新分组
     * date: 2018-05-25
     * @return string
     */
    public function actionUpdateGroup()
    {
        $butlerIds = $this->get('butlerIds');
        $group = $this->get('group');
        if($butlerIds && $group){
            $butlerGIds = explode(',', $butlerIds);
            $butlerId = [];
            foreach($butlerGIds as $k => $v){
                $temp = explode('-', $v);
                $butlerId[] = $temp[0];
            }

            Butler::updateAll(['group' => $group], ['id' => $butlerId]);

            $this->setFlashSuccess('更新成功');
            return $this->renderJsonSuccess([]);
        }

        return $this->renderJsonFail('服务繁忙');
    }

    /**
     * Finds the Butler model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Butler the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Butler::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionExport($group = null, $status = null, $house_id = null, $bName = null){

        $data = Butler::find()->select('id,project_house_id,regions,nickname,group')
            ->filterWhere(['group' => $group])
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['LIKE', 'nickname',$bName])->all();

        if (empty($house_id)) {
            $projectName = '所有项目';
        } else {
            $projectModel = Project::findOne(['house_id' => $house_id]);
            $projectName = $projectModel->house_name;
        }

        $fileName = $projectName . '-管家列表.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));

        $str = "姓名,手机号,身份,分公司,项目,管辖区域,是否有包含项目\n";

        echo mb_convert_encoding($str, 'GBK', 'UTF8');

        if($data){
            foreach ($data AS $val){

                /* @var $val Butler */
                $str = implode(',', [
                        $val->nickname,
                        $val->butlerAuth->account ?? '-',
                        $val->groupText,
                        $val->project->projectRegionName ?? '-',
                        $val->project->house_name ?? '-',
                        implode('、', ArrayHelper::getColumn($val->regionData, 'house_name')),
                        in_array($val->project_house_id, explode(',', $val->regions)) ? '是' : '否'
                    ]) . "\n";

                echo mb_convert_encoding($str, 'GBK', 'UTF8');

            }
            die;
        }

    }
}
