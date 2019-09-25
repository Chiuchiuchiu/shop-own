<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array*/

?>
<?php

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin();

?>

<?php
    $answerStr = '';
    isset($data->answer) && $answerStr = implode("|", json_decode($data->answer, true));
?>
<?= $form->field($model, 'title')->textInput([
        'maxlength' => true,
        'placeholder' => "请输入中秋题目",
        'value' => $data->title ?? ''
    ]);
?>
<?= $form->field($model, 'answer')->textarea([
        'maxlength' => true,
        'rows' => 4,
        'placeholder' => "请输入答案，以“|”分隔。例如：答案1|答案2",
        'value' => $answerStr
    ]);
?>
<?= $form->field($model, 'answer_true')->textInput([
        'maxlength' => true,
        'placeholder' => "正确答案。选择对应答案的第几条，例如1,2,3,4",
        'value' => (isset($data->answer_true) && in_array($data->answer_true, [0,1,2,3])) ? ($data->answer_true + 1) : '']); ?>

<div class="form-group field-questionproject-title required">
    <label class="control-label col-sm-3" for="questionproject-title">&nbsp;</label>
    <div class="col-sm-6">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/min-autumn/answer-list" class="btn btn-success">取消</a>
    </div>
</div>

<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
