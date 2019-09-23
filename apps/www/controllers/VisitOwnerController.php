<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/18
 * Time: 13:57
 */

namespace apps\www\controllers;


use apps\butler\models\Butler;

class VisitOwnerController extends Controller
{
    public function actionIndex()
    {
        $butlerId = $this->get('butlerId');
        $_user = Butler::findOne(['id' => $butlerId]);

        return $this->render('index', [
            '_user' => $_user,
        ]);
    }
}