<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use common\models\Goods;
use common\models\GoodsCategory;
use common\models\Shop;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


class GoodsController extends Controller
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

    public function actionSetStatus($id, $status){

        $res = Goods::findOne($id);

        $res->status = $status;

        return $res->save() ? $this->renderJsonSuccess([]) : $this->renderJsonFail("修改失败！");
    }

    public function actionCategory($search = null, $status = null, $shop_id = null){

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

    public function actionCategorySetStatus($id, $status){

        $res = GoodsCategory::findOne($id);

        if($res){

            $isExistSon = GoodsCategory::find()->where(['parent_id' => $id, 'status' => GoodsCategory::STATUS_ACTIVE])->count();

            if($isExistSon > 0){
                return $this->renderJsonFail("修改失败！存在子类");
            }

            $isExistGoods = Goods::find()->where(['category_id' => $id])->count();

            if($isExistGoods > 0){
                return $this->renderJsonFail("修改失败！有商品使用此分类");
            }

            $res->status = $status;

            return $res->save() ? $this->renderJsonSuccess([]) : $this->renderJsonFail("修改失败！");
        }

        return $this->renderJsonFail("没有此分类");

    }
}