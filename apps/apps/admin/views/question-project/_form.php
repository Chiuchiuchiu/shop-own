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

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'placeholder' => "请输入调研名称"]); ?>

    <div class="form-group">

        <label class="control-label col-sm-3">开始与截止时间</label>
        <div class="col-sm-6">
            <?= DatePicker::widget([
                'model' => $dateTime,
                'attribute' => 'startDate',
                'attribute2' => 'endDate',
                'type' => DatePicker::TYPE_RANGE,
                'language' => 'zh-CN',
                'separator' => "至",
                'pluginOptions' => [
                    'startDate' => 'data-date-start-date',  //开始时间为今天
                    'autoclose' => true,
                    'format' => 'yyyy-m-d'
                ]
            ]) ?>
        </div>
    </div>

    <div class="form-group field-questionproject-title required">
        <label class="control-label col-sm-3" for="questionproject-title">&nbsp;</label>
        <div class="col-sm-6">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a href="/question-project" class="btn btn-success">取消</a>
        </div>
    </div>

<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
