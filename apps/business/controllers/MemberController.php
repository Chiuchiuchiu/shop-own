<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/1/22
 * Time: 15:01
 */

namespace apps\business\controllers;

use apps\business\models\Member;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;

class MemberController extends Controller
{
    public function actionIndex()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $dataProvider = new ActiveDataProvider();
        $phone = $this->get('phone', null);
        $userName = $this->get('name', null);

        $query = Member::find();
        if(!empty($phone) || !empty($userName)){
            $query->andFilterWhere(['=', 'phone', $phone])->andFilterWhere(['like', 'name', $userName]);
        } else {
            $query->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()]);
        }

        $dataProvider->query = $query;
        $dataProvider->setSort([
            'defaultOrder' => [
                'created_at' => SORT_DESC,
            ],
            'attributes' => [
                'created_at'
            ]
        ]);
        return $this->render('index', get_defined_vars());
    }
}