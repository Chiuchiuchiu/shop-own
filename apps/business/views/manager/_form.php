<?php
/**
 * @var $model  \apps\admin\models\Manager
 * @var $this yii\web\View
 */
use \yii\bootstrap\ActiveForm;

?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<?= $form->field($model, 'real_name')->textInput(['maxlength' => true,'readonly'=>!$model->isNewRecord]) ?>
<?= $form->field($model, 'email')->textInput(['maxlength' => true,'readonly'=>!$model->isNewRecord]) ?>

<?=$model->isNewRecord?$form->field($model, 'password')->textInput():'' ?>

<?= $form->field($model, 'group_id')->dropDownList(\yii\helpers\ArrayHelper::map(
    \apps\business\models\ManagerGroup::findAll(['state' => \apps\business\models\ManagerGroup::STATE_ACTIVE]), 'id', 'name')); ?>
    <div class="form-group">
        <div class="text-center">
            <?php echo \yii\bootstrap\Html::submitButton($model->isNewRecord ? '创建账户' : '提交修改', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>