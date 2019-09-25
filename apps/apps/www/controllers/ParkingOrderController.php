<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/17
 * Time: 16:22
 */

namespace apps\www\controllers;


use common\models\ParkingOrder;
use yii\data\ActiveDataProvider;

class ParkingOrderController extends Controller
{
    public function actionOrderList()
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ParkingOrder::find()
            ->where([
                'status' => [ParkingOrder::STATUS_PAYED, ParkingOrder::STATUS_TEST_PAYED, ParkingOrder::STATUS_REFUND],
                'member_id' => $this->user->id
            ])
            ->orderBy('created_at DESC');

        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('order-list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('order-list', [
                'dataProvider' => $dataProvider
            ]);
        }
    }

    public function actionOrderView($id)
    {
        $model = ParkingOrder::find()->where([
            'member_id' => $this->user->id,
            'id' => $id
        ])->one();

        return $this->render('order-view', ['model' => $model]);
    }

}