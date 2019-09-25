<?php

use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\ButlerVisitIndicators */
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);

echo $form->field($model, 'years')->input('number', ['disabled' => true]);
echo $form->field($model,'the_first_quarter')->input('number');
echo $form->field($model,'second_quarter')->input('number');
echo $form->field($model,'third_quarter')->input('number');
echo $form->field($model,'fourth_quarter')->input('number');

?>
<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton('提交', ['btn btn-success']) ?>
        </div>
    </div>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>