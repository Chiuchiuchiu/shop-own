<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use common\models\Order;
use common\models\Shop;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


class OrderController extends Controller
{
    public function actionIndex($number = null, $status = null, $shop_id = null)
    {

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $shopList = ArrayHelper::merge([''=>'全部商铺'], ArrayHelper::map(Shop::find()->all(), 'id', 'name'));

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Order::find()
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['order_number' => $number])
            ->andFilterWhere(['shop_id' => $shop_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'number' => $number,
            'status' => $status,
            'shopList' => $shopList,
            'shopId' => $shop_id,
            'dateTime' => $dateTime,
        ]);
    }

    public function actionRefund($number = null, $shop_id = null){

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $shopList = ArrayHelper::merge([''=>'全部商铺'], ArrayHelper::map(Shop::find()->all(), 'id', 'name'));

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Order::find()
            ->andFilterWhere(['status' => [Order::STATUS_REFUND, Order::STATUS_REFUNDING]])
            ->andFilterWhere(['order_number' => $number])
            ->andFilterWhere(['shop_id' => $shop_id])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()]);
        $dataProvider->setSort(false);

        return $this->render('refund', [
            'dataProvider' => $dataProvider,
            'number' => $number,
            'shopList' => $shopList,
            'shopId' => $shop_id,
            'dateTime' => $dateTime,
        ]);
    }

}