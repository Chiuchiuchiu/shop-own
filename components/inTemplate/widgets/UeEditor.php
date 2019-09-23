<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/5/29
 * Time: 10:58
 */

namespace components\inTemplate\widgets;


use components\inTemplate\assets\UeEditorAsset;
use yii\base\Widget;

class UeEditor extends Widget
{
    public $key;
    public $content = '';
    public $name = '';

    public function init()
    {
        $view = $this->getView();
        UeEditorAsset::register($view);

        if(empty($this->key)) $this->key = 'K_'.md5(microtime(true).rand(0,99999));
        $jsCode = <<<JS
        var ue = UE.getEditor('{$this->key}');
JS;
        $view->registerJs($jsCode, \yii\web\View::POS_END);
    }

    public function run()
    {
        return Html::tag('script', $this->content,['id' => $this->key,'name' => $this->name]);
    }

}