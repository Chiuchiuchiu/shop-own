<?php

use components\inTemplate\widgets\ActiveForm;
use common\models\Member;


/* @var $this yii\web\View */
/* @var $model \common\models\Member */

?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?php

echo $form->field($model,'nick_name')->textInput(['readonly'=>true]);
echo $form->field($model,'mobile')->textInput(['readonly'=>true]);
echo $form->field($model,'status')->dropDownList(\yii\helpers\ArrayHelper::merge([''=>'请选择'], Member::statusMap()));
echo $form->field($model,'member_type')->dropDownList(\yii\helpers\ArrayHelper::merge([''=>'请选择'], Member::memberMap()));

?>
<input type="hidden" name="Member[id]" value="<?= $model->id ?>"/>

<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
