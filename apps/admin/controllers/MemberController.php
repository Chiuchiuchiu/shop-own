<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/1/22
 * Time: 15:01
 */

namespace apps\admin\controllers;

use \common\models\Member;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class MemberController extends Controller
{
    public function actionIndex()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $dataProvider = new ActiveDataProvider();
        $mobile = $this->get('mobile', null);
        $nickName = $this->get('nickName', null);

        $query = Member::find()->andFilterWhere(['mobile' => $mobile])->andFilterWhere(['like', 'nick_name', $nickName])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()]);

        $dataProvider->query = $query;
        $dataProvider->setSort([
            'defaultOrder' => [
                'sort' => SORT_DESC,
                'id' => SORT_DESC,
            ],
            'attributes' => [
                'sort',
                'id'
            ]
        ]);

        return $this->render('index', get_defined_vars());
    }

    public function actionGroup(){

        $this->setFlashError('功能未开放', '请等待下一版本');

        return $this->goBack();
    }

    public function actionUpdate($id){

        $model = Member::findOne($id);

        if($model){
            if($this->isPost){

                $postData = $this->post();

                if($model->load($postData) && $model->save()){

                    $this->setFlashSuccess();
                    return $this->backRedirect();
                }

                $this->setFlashError('编辑失败', '信息填写有误');
                return $this->backRedirect();
            }
        }else{
            throw new NotFoundHttpException();
        }

        return $this->render('update', get_defined_vars());
    }
}