<?php

use components\inTemplate\widgets\ActiveForm;
use common\models\Project;


/* @var $this yii\web\View */
/* @var $model Project */
/* @var array $projectRegion */
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
echo $form->field($model,'house_name')->textInput();
echo $form->field($model,'url_key')->textInput();
echo $form->field($model,'area')->textInput();
echo $form->ajaxUpload($model,'logo', 'logo', 'logo','楼盘LOGO(660w)');
echo $form->ajaxUpload($model,'icon', 'icon', 'icon','楼盘ICON(180x50)');
echo $form->field($model,'status')->dropDownList(Project::statusMap());
echo $form->field($model,'project_fee_cycle_id')->dropDownList(\yii\helpers\ArrayHelper::map(\apps\business\models\ProjectFeeCycle::findAll(['status' => \apps\business\models\ProjectFeeCycle::STATUS_ENABLE]), 'id', 'name'));
echo $form->field($model, 'project_region_id')->dropDownList($projectRegion);
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