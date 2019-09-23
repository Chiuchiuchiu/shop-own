<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array*/

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin();

echo $form->field($model, 'title')->textInput(['maxlength' => true]);
echo $form->field($model, 'site')->textInput(['maxlength' => true]);
echo $form->field($model, 'type_id')->dropDownList(\apps\admin\models\Question::typeMap());
?>
<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
