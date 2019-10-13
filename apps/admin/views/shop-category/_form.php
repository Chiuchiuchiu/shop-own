<?php

use components\inTemplate\widgets\ActiveForm;
use common\models\ShopCategory;


/* @var $this yii\web\View */
/* @var $model \common\models\Shop */
/* @var array $categoryInfo */
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?php

echo $form->field($model,'name')->textInput();
echo $form->field($model,'status')->dropDownList(\yii\helpers\ArrayHelper::merge([''=>'请选择'], ShopCategory::statusMap()));

?>


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
