<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array*/

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin();
echo $form->field($model, 'title')->textInput(['maxlength' => true]);
echo $form->field($model, 'bg_color')->textInput(['maxlength' => true]);
echo $form->field($model, 'btn_color')->textInput(['maxlength' => true]);
echo $form->field($model, 'btn_text')->textInput(['maxlength' => true]);
echo $form->field($model, 'options1')->textInput(['maxlength' => true]);
echo $form->field($model, 'options2')->textInput(['maxlength' => true]);
echo $form->field($model, 'comment_tag')->textarea(['maxlength' => true]);
echo $form->ajaxUpload($model, 'file', 'pic', 'pic', '封面图');
?>
<div class="form-group">
    <label class="control-label col-sm-3">选择项目</label>
    <div class="col-sm-6"><select id="region_id" onchange="ProjectChoose();">
            <option value="">请选择分公司</option>
            <?php foreach ($RegionList as $v){
                echo '<option value="'.$v->id.'">'.$v->name.'</option>';
            }?>
        </select>
        <select name="project_id" id="project_id">
            <option value="<?=$model->project_id;?>" selected>请选择</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3">时间</label>
    <div class="col-sm-6">
        <?= \kartik\date\DatePicker::widget([
            'name'=>'dateTime',
            'value' => date('Y-m-d',$model->created_at?$model->created_at:time()),
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
        echo \components\inTemplate\widgets\KindEditor::widget([
            'name'=>sprintf('%s[%s]',$model->formName(),'site'),
            'content'=>$model->site
        ])
        ?>
    </div>
</div>

<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<script>
    function ProjectChoose() {
        $.ajax({
            type: 'GET',
            url: "/question/project-choose?Region_id=" + $('#region_id').val(),
            timeout: 3000, //超时时间：30秒
            dataType:'json',
            success: function (data) {
                $('select[name=project_id]').empty();
                $.each(data.houseArr,function(n,value){

                    $('select[name=project_id]').append("<option data-id='"+value['house_id']+"' value='"+value['house_id']+"'>"+value['house_name']+"</option>");
                });
                $('select[name=project_id]').trigger("change");
            },
            error: function (data) {
                alert(data);
            }
        });
    }
</script>