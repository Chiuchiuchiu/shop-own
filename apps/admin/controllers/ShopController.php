<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use common\models\Shop;
use common\models\ShopCategory;
use common\models\ShopOfficialFile;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


class ShopController extends Controller
{
    public function actionIndex($search=null, $status=null)
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Shop::find()
            ->andFilterWhere(['status' => $status])
            ->andFilterWhere(['like', 'name', $search]);
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function actionJumpPm($key)
    {
        $urlKey = $key;
        $token = md5(str_shuffle(microtime()) . $_SERVER['HTTP_USER_AGENT'] . $key);
        \Yii::$app->cache->set($token, $token, 30);

        $this->redirect('http://'. $urlKey . '.' . \Yii::$app->params['domain.pm'] . '?token=' . $token);
    }

    public function actionUpdate($id)
    {
        $model = Shop::findOne($id);
        $ShopOfficialFileModel = ShopOfficialFile::findOne(['shop_id' => $id]);

        $category = ShopCategory::find()->where(['status' => ShopCategory::STATUS_ACTIVE])->all();
        $categoryInfo = ArrayHelper::merge([''=>'请选择'], ArrayHelper::map($category, 'id', 'name'));

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

    public function actionCreate()
    {
        $model = new Shop();
        $ShopOfficialFileModel = new ShopOfficialFile();
        $category = ShopCategory::find()->where(['status' => ShopCategory::STATUS_ACTIVE])->all();
        $categoryInfo = ArrayHelper::merge([''=>'请选择'], ArrayHelper::map($category, 'id', 'name'));

        if ($this->isPost) {
            $postData = $this->post();
            $postData['Shop']['service_type'] = implode(',', $postData['Shop']['service_type']);

            if($model->load($postData) && $ShopOfficialFileModel->load($postData)){

                $shop = Shop::findOne(['name' => $postData['Shop']['name']]);

                if($shop){
                    $this->setFlashError('', '商铺名已存在');
                    return $this->backRedirect();
                }

                $transaction = \Yii::$app->db->beginTransaction();

                if(!$model->save($postData)){
                    $transaction->rollBack();
                    $this->setFlashError('', '商铺基本信息添加失败');
                    return $this->backRedirect();
                }

                $ShopOfficialFileModel->shop_id = $model->id;
                $ShopOfficialFileModel->id_card_img = $postData['ShopOfficialFile']['id_card_img'];
                $ShopOfficialFileModel->license_img = $postData['ShopOfficialFile']['license_img'];

                if(!$ShopOfficialFileModel->save()){

                    $transaction->rollBack();
                    $this->setFlashError('', '商铺证件信息添加失败');
                    return $this->backRedirect();
                }

                $transaction->commit();
                $this->setFlashSuccess();
                return $this->backRedirect();
            }

            $this->setFlashError('添加失败', '信息填写有误');
            return $this->backRedirect();
        }

        return $this->render('create', ['model' => $model, 'shopOfficialFileModel' => $ShopOfficialFileModel,'categoryInfo' => $categoryInfo]);
    }
}