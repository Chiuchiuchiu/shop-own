<?php
namespace apps\www\controllers;
use common\models\Feedback;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class FeedbackController extends Controller
{

    public function actionIndex()
    {
        $feedback = new Feedback();
        if($this->isPost){
            $feedback->load($this->post());
            $feedback->member_id=$this->user->id;
            if($feedback->save()){
                return $this->renderJsonSuccess([]);
            }else{
                $error = $feedback->getErrors();
            }
        }
        return $this->render('index', get_defined_vars());
    }

    public function actionList()
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Feedback::find()
            ->where(['member_id'=>$this->user->id])
            ->orderBy('status ASC,id DESC');
        $dataProvider->pagination->pageSize=3;
        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('list', ['dataProvider' => $dataProvider]);
        }
    }
    public function actionView($id){
        $model = Feedback::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $this->render('view',['model'=>$model]);
    }

}
