<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\admin\controllers;

use common\models\ShopCategory;
use common\models\ShopOfficialFile;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;


class ShopCategoryController extends Controller
{
    public function actionIndex($search=null, $status=null)
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ShopCategory::find()
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
        $model = ShopCategory::findOne($id);

        if ($this->isPost) {
            $postData = $this->post();

            if($model->load($postData) && $model->save($postData)){

                $this->setFlashSuccess();
                return $this->backRedirect();
            }

            $this->setFlashError('编辑失败', '信息填写有误');
            return $this->backRedirect();
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionCreate()
    {
        $model = new ShopCategory();

        if ($this->isPost) {
            $postData = $this->post();

            if($model->load($postData) && $model->save($postData)){

                $this->setFlashSuccess();
                return $this->backRedirect();
            }

            $this->setFlashError('添加失败', '信息填写有误');
            return $this->backRedirect();
        }

        return $this->render('create', ['model' => $model]);
    }
}