<?php

/**
 * @var $this  yii\web\View
 * @var $model  \apps\admin\models\Manager
 */
$this->title = "修改密码";
$this->params['breadcrumbs'][] = ["label" => "个人设置"];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php \components\inTemplate\widgets\IBox::begin() ?>
<?php $form = \yii\bootstrap\ActiveForm::begin(['layout' => 'horizontal']) ?>
<?php echo $form->field($model, 'real_name')->textInput(['disabled'=>true]) ?>
<?php echo $form->field($model, 'email')->textInput(['disabled'=>true]) ?>
<?php echo $form->field($model, 'password')->passwordInput(['value'=>'']) ?>
<?php echo $form->field($model, 'confirmPassword')->passwordInput() ?>
<div class="form-group">
    <div class="text-center">
        <?php echo \yii\bootstrap\Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php \yii\bootstrap\ActiveForm::end() ?>
<?php \components\inTemplate\widgets\IBox::end() ?>
