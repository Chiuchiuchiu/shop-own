<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/14
 * Time: 15:07
 */

namespace components\inTemplate\widgets;


use components\inTemplate\assets\ICheckAsset;
use yii\bootstrap\Widget;
use yii\web\View;

class ICheck extends Widget
{
    public $class;
    public $value;

    public function init()
    {
        parent::init();
        $view = $this->getView();
        ICheckAsset::register($view);
        $js = <<<'JS'
         $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });
JS;
        $view->registerJs(sprintf('$(function(){%s})', $js), View::POS_LOAD);
    }

    public function run()
    {
        return "<div class=\"icheckbox_square-green\" style=\"position: relative;\"><input class=\"i-checks\" name=\"input[]\" style=\"position: absolute; opacity: 0;\" type=\"checkbox\" value=\"{$this->value}\"></div>";
    }

}