<?php

use components\inTemplate\widgets\ActiveForm;
use common\models\ProjectServicePhone;

/* @var $this yii\web\View */
/* @var $model \common\models\Project */
/* @var $rbac apps\admin\models\RBAC */
/* @var array $projectInfo */
/* @var array $categoryInfo */

$this->title = isset($model->projectInfo->house_name) ? $model->projectInfo->house_name . $model->name : '所有';
$this->params['breadcrumbs'][] = ['label' => '编辑电话分类', 'url' => ['category']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= \components\inTemplate\widgets\BackBtn::widget() ?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);

echo $form->field($model,'name')->textInput();
echo $form->field($model,'status')->dropDownList(ProjectServicePhone::statusMap());
echo $form->field($model,'project_house_id')->dropDownList($projectInfo);

?>
<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

