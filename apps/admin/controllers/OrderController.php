<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use common\models\Shop;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


class OrderController extends Controller
{
    public function actionIndex($search = null, $status = null, $shop_id = null)
    {
        $shopList = ArrayHelper::merge([''=>'全部商铺'], ArrayHelper::map(Shop::find()->all(), 'id', 'name'));

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Goods::find()
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['like', 'name', $search])
            ->andFilterWhere(['shop_id' => $shop_id]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search' => $search,
            'status' => $status,
            'shopList' => $shopList,
            'shopId' => $shop_id,
        ]);
    }

    public function actionRefund($search = null, $status = null, $shop_id = null){

        $shopList = ArrayHelper::merge([''=>'全部商铺'], ArrayHelper::map(Shop::find()->all(), 'id', 'name'));

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = GoodsCategory::find()
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['like', 'name', $search])
            ->andFilterWhere(['shop_id' => $shop_id]);
        $dataProvider->setSort(false);

        return $this->render('category', [
            'dataProvider' => $dataProvider,
            'search' => $search,
            'status' => $status,
            'shopList' => $shopList,
            'shopId' => $shop_id,
        ]);
    }

}