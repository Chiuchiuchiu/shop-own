<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array*/

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin();

echo $form->field($model, 'title')->textInput(['maxlength' => true]);
echo $form->field($model, 'category_id')->dropDownList($categoryMap);
echo $form->field($model, 'show_type')->dropDownList(\common\models\Article::showTypeMap());
//echo $form->imgUpload($model,'pic','封面图')

echo $form->ajaxUpload($model, 'file', 'pic', 'pic', '封面图');

?>
<div class="form-group">
    <label class="control-label col-sm-3">时间</label>
    <div class="col-sm-6">
        <?= \kartik\date\DatePicker::widget([
            'name'=>'dateTime',
            'value' => date('Y-m-d',$model->post_at?$model->post_at:time()),
            'options' => ['placeholder' => '发布日期'],
            'type' => \kartik\date\DatePicker::TYPE_COMPONENT_PREPEND,
            'language' => 'zh-CN',
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-m-d'
            ]
        ]) ?>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3">公告内容</label>
    <div class="col-sm-6">
        <?php
        echo \components\inTemplate\widgets\UeEditor::widget([
            'name' => sprintf('%s[%s]',$model->formName(),'content'),
            'content' => $model->content,
        ])
        ?>
    </div>
</div>

<?php
echo $form->field($model,'status')->dropDownList(\common\models\Article::statusMap());
?>
<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

