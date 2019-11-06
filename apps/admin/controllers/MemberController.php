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

    public function actionUpdate($id){

        $memberInfo = Member::findOne($id);

        var_dump($memberInfo);exit;
    }
}