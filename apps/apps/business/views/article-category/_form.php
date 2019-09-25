<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */
/* @var $form yii\widgets\ActiveForm */

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(); ?>


<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

