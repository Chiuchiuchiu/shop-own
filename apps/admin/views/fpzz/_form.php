<?php

use yii\helpers\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Butler */
/* @var array $projectsArray */

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(); ?>

<div class="form-group">
    <label class="control-label col-sm-3">项目</label>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-4">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => $model->formName() . '[project_house_id]',
                    'value' => $model->project_house_id,
                    'items' => $projectsArray,
                    'addClass' => 'c-project'
                ])?>

            </div>
        </div>
    </div>
</div>

<?= $form->field($model, 'tips')->textInput() ?>
<?= $form->field($model, 'status')->dropDownList(\apps\pm\models\ProjectFpzzAccount::statusType()) ?>


<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script>

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>

