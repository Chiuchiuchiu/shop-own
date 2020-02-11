<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\business\controllers;

use common\models\FreightTemplate;
use common\models\Shop;
use yii\data\ActiveDataProvider;


class ShopController extends Controller
{
    /**
     * 运费模板
     * @param null $search
     * @return string
     * @author zhaowenxi
     */
    public function actionExpress($search=null)
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = FreightTemplate::find()->where(['shop_id' => $this->user->shop_id])
            ->andFilterWhere(['LIKE', 'name', $search]);
        $dataProvider->setSort(false);

        return $this->render('express', get_defined_vars());
    }

    /**
     * 库存设置
     * @return string
     * @author zhaowenxi
     */
    public function actionStock(){

        $model = Shop::findOne($this->user->shop_id);

        return $this->render('stock', get_defined_vars());
    }

    /**
     * 添加运费模
     * @author zhaowenxi
     */
    public function actionCreateExpress(){

        $model = new FreightTemplate();


        return $this->render('create-express', get_defined_vars());
    }

    /**
     * 编辑运费模
     * @author zhaowenxi
     */
    public function actionUpdateExpress(){

        $model = new FreightTemplate();

        return $this->render('update-express', get_defined_vars());
    }

}