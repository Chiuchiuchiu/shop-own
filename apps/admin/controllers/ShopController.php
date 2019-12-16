<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use apps\business\models\ShopManager;
use apps\business\models\ShopManagerLoginLog;
use apps\business\models\ShopManagerGroup;
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

    public function actionUpdate($id)
    {
        $model = Shop::findOne($id);
        $ShopOfficialFileModel = ShopOfficialFile::findOne(['shop_id' => $id]);
        $shopManager = ShopManager::findOne(['shop_id' => $id]);

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

                $shopManager->shop_id = $model->id;
                $shopManager->name = $postData['ShopManager']['name'];
                $shopManager->mobile = $postData['ShopManager']['mobile'];
                $shopManager->email = $postData['ShopManager']['email'];

                if(!$shopManager->save()){

                    $transaction->rollBack();
                    $this->setFlashError('', '商铺联系信息添加失败');
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

        return $this->render('update', ['model' => $model, 'shopManager' => $shopManager, 'shopOfficialFileModel' => $ShopOfficialFileModel,'categoryInfo' => $categoryInfo]);
    }

    public function actionCreate()
    {
        $model = new Shop();
        $shopOfficialFileModel = new ShopOfficialFile();
        $shopManager = new ShopManager();
        $category = ShopCategory::find()->where(['status' => ShopCategory::STATUS_ACTIVE])->all();
        $categoryInfo = ArrayHelper::merge([''=>'请选择'], ArrayHelper::map($category, 'id', 'name'));

        if ($this->isPost) {
            $postData = $this->post();
            $postData['Shop']['service_type'] = implode(',', $postData['Shop']['service_type']);

            if($model->load($postData) && $shopOfficialFileModel->load($postData) && $shopManager->load($postData)){

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

                $shopManager->shop_id = $model->id;
                $shopManager->name = $postData['ShopManager']['name'];
                $shopManager->mobile = $postData['ShopManager']['mobile'];
                $shopManager->email = $postData['ShopManager']['email'];
                $shopManager->password = $shopManager->md5Password(123456);
                $shopManager->manager_group = ShopManagerGroup::GROUP_ROOT;
                $shopManager->status = ShopManagerGroup::STATE_ACTIVE;
                $shopManager->created_at = time();

                if(!$shopManager->save()){

                    $transaction->rollBack();
                    $this->setFlashError('', '商铺联系信息添加失败');
                    return $this->backRedirect();
                }

                $shopOfficialFileModel->shop_id = $model->id;
                $shopOfficialFileModel->id_card_img = $postData['ShopOfficialFile']['id_card_img'];
                $shopOfficialFileModel->license_img = $postData['ShopOfficialFile']['license_img'];

                if(!$shopOfficialFileModel->save()){

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

        return $this->render('create', ['model' => $model, 'shopManager' => $shopManager, 'shopOfficialFileModel' => $shopOfficialFileModel,'categoryInfo' => $categoryInfo]);
    }
}