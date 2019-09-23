<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 16:31
 * Description:
 */

namespace components\inTemplate\widgets;


use yii\base\Widget;
use yii\bootstrap\Html;

class BackBtn extends Widget
{
    public $name = "返回";
    public $url = "javascript:window.location.href=document.referrer; ";
    public $option = [];
    public function run()
    {

        return Html::a($this->name,$this->url,array_merge(['class'=>'btn btn-w-m btn-white'],$this->option));
    }


}