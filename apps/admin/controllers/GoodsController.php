<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use common\models\Goods;
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

    public function actionUpdate($id)
    {
        $model = Goods::findOne($id);

        if ($this->isPost) {
            $postData = $this->post();

            $postData['Shop']['service_type'] = implode(',', $postData['Shop']['service_type']);

            if($model->load($postData) && $ShopOfficialFileModel->load($postData)){

                $shop = Shop::find()->where(['name' => $postData['Shop']['name']])
                    ->andFilterWhere(['<>', 'id', $id])->count();
                if($shop > 0){
                    $this->setFlashError('', '商铺名已存在');
                    return $this->backRedirect();
                }

                $transaction = \Yii::$app->db->beginTransaction();

                if(!$model->save($postData)){
                    $transaction->rollBack();
                    $this->setFlashError('', '商铺基本信息编辑失败');
                    return $this->backRedirect();
                }

                $ShopOfficialFileModel->shop_id = $model->id;
                $ShopOfficialFileModel->id_card_img = $postData['ShopOfficialFile']['id_card_img'];
                $ShopOfficialFileModel->license_img = $postData['ShopOfficialFile']['license_img'];

                if(!$ShopOfficialFileModel->save()){

                    $transaction->rollBack();
                    $this->setFlashError('', '商铺证件信息编辑失败');
                    return $this->backRedirect();
                }

                $transaction->commit();
                $this->setFlashSuccess();
                return $this->backRedirect();
            }

            $this->setFlashError('编辑失败', '信息填写有误');
            return $this->backRedirect();
        }

        return $this->render('update', ['model' => $model, 'shopOfficialFileModel' => $ShopOfficialFileModel,'categoryInfo' => $categoryInfo]);
    }

}