<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ButlerElectionActivity */
/* @var int $group */

$this->title = '编辑投票';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['vote-detail?group=' . $group]]);

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin();

echo $form->field($model, 'name')->textInput(['maxlength' => true]);
echo $form->field($model, 'phone')->textInput(['maxlength' => true]);
echo $form->field($model, 'status')->dropDownList(\common\models\ButlerElectionActivity::statusMap());
echo $form->field($model, 'groupText')->textInput(['disabled' => true]);
echo $form->field($model, 'number')->textInput(['disabled' => true]);
echo $form->field($model, 'introduce')->textarea(['maxlength' => true]);
echo $form->field($model, 'head_img', ['template' => "{label}<div class='col-sm-6'> " . Html::img($model->head_img) . "</div>"]);

echo Html::hiddenInput($model->formName() . '[group]', $model->group);
echo Html::hiddenInput($model->formName() . '[project_house_id]', $model->project_house_id);

?>

<!--    <div class="form-group field-butlerelectionactivity-introduce required">-->
<!--        <label class="control-label col-sm-3" for="butlerelectionactivity-number">标签</label>-->
<!--        <div class="col-sm-6">-->
            <?php
//                $labels = [];
//                $buLabels = \common\models\ButlerLabels::findAll(['butler_id' => $model->butler_id]);
            ?>

<!--            --><?php //foreach ($buLabels as $key => $row) { ?>
<!--                --><?php ///* @var \common\models\ButlerLabels $row */?>
<!--                <span class="badge">--><?php // echo $row->individualLabels->name ?><!--</span>-->

<!--            --><?php //} ?>

<!--        </div>-->
<!--    </div>-->

    <div class="form-group text-center">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>

<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>