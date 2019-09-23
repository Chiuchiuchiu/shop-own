<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/23
 * Time: 10:25
 */

namespace components\inTemplate\widgets;


use components\inTemplate\assets\ZTreeAsset;
use yii\bootstrap\Widget;

class ZTree extends Widget
{
    public function init()
    {
        $view = $this->getView();
        ZTreeAsset::register($view);
    }
}