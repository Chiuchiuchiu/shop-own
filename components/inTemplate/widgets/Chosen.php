<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/16 10:46
 * Description:
 */

namespace components\inTemplate\widgets;


use components\inTemplate\assets\ChosenAsset;
use yii\bootstrap\Html;
use yii\web\View;
use yii\bootstrap\Widget;

class Chosen extends Widget
{

    public $name=null;
    public $value=null;
    public $items=null;
    public $addClass='';
    public function init()
    {
        parent::init();
        $view = $this->getView();
        ChosenAsset::register($view);
        $jsCode = <<<JS
        var config = {
                '.chosen-select'           : {width:"95%", search_contains:true},
                '.chosen-select-deselect'  : {allow_single_deselect:true},
                '.chosen-select-no-single' : {disable_search_threshold:10},
                '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
                '.chosen-select-width'     : {width:"95%"}
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }

JS;
        $view->registerJs(sprintf('$(function(){%s})', $jsCode), View::POS_END);
    }

    public function run()
    {
     return Html::dropDownList(
         $this->name,
         $this->value,
         $this->items
         ,
         ['class' => 'form-control chosen-select '.$this->addClass,
         'style'=>'width:95%'
         ]

     );
    }


}