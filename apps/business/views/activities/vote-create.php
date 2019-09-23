<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Vote */

$this->title = '编辑投票';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin();

echo $form->field($model, 'name')->textInput(['maxlength' => true]);
echo $form->field($model, 'class')->textInput(['maxlength' => true]);

?>

    <div class="form-group">
        <label class="control-label col-sm-3">开始时间</label>
        <div class="col-sm-6">
            <?= \kartik\date\DatePicker::widget([
                'name' => 'start_time',
                'value' => date('Y-m-d', $model->start_time ? $model->start_time : time()),
                'options' => ['placeholder' => '发布日期'],
                'type' => \kartik\date\DatePicker::TYPE_COMPONENT_PREPEND,
                'language' => 'zh-CN',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-m-d'
                ]
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-3">结束时间</label>
        <div class="col-sm-6">
            <?= \kartik\date\DatePicker::widget([
                'name' => 'end_time',
                'value' => date('Y-m-d', $model->end_time ? $model->end_time : strtotime('+1 days')),
                'options' => ['placeholder' => '发布日期'],
                'type' => \kartik\date\DatePicker::TYPE_COMPONENT_PREPEND,
                'language' => 'zh-CN',
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-m-d'
                ]
            ]) ?>
        </div>
    </div>

<?php
echo $form->field($model,'status')->dropDownList(\common\models\Vote::statusMap());
?>

<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>