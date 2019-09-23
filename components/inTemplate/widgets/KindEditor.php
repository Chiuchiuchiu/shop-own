<?php

namespace components\inTemplate\widgets;



use components\inTemplate\assets\KindEditorAsset;
use yii\base\Widget;
use yii\helpers\Html;

class KindEditor extends Widget
{
    public $name;
    public $content;
    public $uploadUrl='/default/kind-upload';
    public $key;

    public function init()
    {
        parent::init();
        $view = $this->getView();
        KindEditorAsset::register($view);

        if(empty($this->key)) $this->key = 'K_'.md5(microtime(true).rand(0,99999));
        $jsCode = <<<JS
        KindEditor.ready(function(K) {
			var editor1 = K.create('#{$this->key}', {
				uploadJson : '{$this->uploadUrl}',
				allowFileManager : false,
				'width':'100%',
				autoHeightMode: true,
				filterMode: false,
			});
			prettyPrint();
		});
JS;
        $view->registerJs($jsCode, \yii\web\View::POS_END);

    }

    public function run()
    {
		return Html::textarea($this->name,$this->content,['id'=>$this->key]);
    }

}