<?php
/**
 * Created by
 * Author: zhaowenxi
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\business\controllers;

use yii\data\ActiveDataProvider;


class ShopController extends Controller
{
    public function actionExpress($search=null, $status=null)
    {
        var_dump($this->user);exit;
        $dataProvider = new ActiveDataProvider();

        return $this->render('express', []);
    }

}