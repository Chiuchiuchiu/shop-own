<?php
namespace apps\business\controllers;
use common\models\PmOrderAuditing;
use common\models\PmOrderAuditingLog;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class PmOrderAuditingController extends Controller
{
    public function actionIndex(){
        $dataProvider = new ActiveDataProvider([
            'query' => PmOrderAuditing::find()->orderBy('id DESC')->where(['!=','status',0]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id){
        $model = PmOrderAuditing::findOne($id);
        if(!$model){
            throw new NotFoundHttpException();
        }
        if($this->isPost){
            $log = new PmOrderAuditingLog();
            $log->data = serialize($model->toArray());
            $log->manager_id=$this->user->id;
            $log->message = "同意了该笔账单";
            $log->pm_order_auditing_id = $model->id;
            $log->save();
            $model->status = $model::STATUS_AUTH;
            $model->save();
        }
        $logs  = PmOrderAuditingLog::findAll(['pm_order_auditing_id'=>$model->id]);
        return $this->render('view',['model'=>$model,'logs'=>$logs]);
    }
}
