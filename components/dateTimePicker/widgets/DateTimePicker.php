<?php
namespace components\dateTimePicker\widgets;

use components\dateTimePicker\assets\AppAsset;
use yii\bootstrap\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

class DateTimePicker extends \yii\base\Widget
{

    public $model;
    public $field;
    public $form = null;

    public $format = 'YYYY-MM-DD HH:mm:ss';
    public $inputKey = '';

    public function init()
    {
        parent::init();
        $view = $this->getView();
        AppAsset::register($view);
        $this->inputKey = 'dateTimePicker' . md5(microtime() . mt_rand(0, 99999));
        $jsCode = <<<JS
        $('#{$this->inputKey}').datetimepicker({
        'format':'{$this->format}'
        });
JS;
        $view->registerJs(sprintf('$(function(){%s})', $jsCode), View::POS_END);
    }

    public function run()
    {
        return $this->form instanceof ActiveForm ?
            $this->form->field($this->model, $this->field)->textInput(['id' => $this->inputKey]) :
            Html::textInput($this->field, '', ['id' => $this->inputKey]);
    }
}
