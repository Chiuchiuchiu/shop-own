<?php
use components\inTemplate\widgets\ActiveForm;
use common\models\ProjectHouseStructure;
use \yii\bootstrap\Modal;

/**
 * @var $this yii\web\View
 * @var $model ProjectHouseStructure
 */
$this->title = '编辑 '. $model->project->house_name . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '项目', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget([
    'name' => '返回上一级',
    'url' => ['edit-structure', 'id' => $model->project_house_id],
    'option' => [
        'class' => 'btn btn-w-m btn-white pull-left'
    ]
]);
?>

<?php \components\inTemplate\widgets\IBox::begin();

$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'id' => $model->formName(),
]);

echo $form->field($model, 'name')->textInput(['maxlength' => 4]);
echo $form->field($model, 'group')->dropDownList(
    \yii\helpers\ArrayHelper::map(ProjectHouseStructure::findAll(['project_house_id' => $model->project_house_id]), 'group', 'groupText')
);
echo $form->field($model, 'ordering')->textInput(['maxlength' => true]);
echo $form->field($model, 'reskind')->textInput(['readonly' => true]);
echo $form->field($model, 'type')->dropDownList(
//        \yii\helpers\ArrayHelper::map(ProjectHouseStructure::findAll(['project_house_id' => $model->project_house_id]), 'type', 'typeText')
        ProjectHouseStructure::typeMap()
)
    ->label('类型');

echo $form->field($model, 'parent_reskind')->dropDownList(ProjectHouseStructure::reskindMap());

?>

<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
            <?= \yii\bootstrap\Html::submitButton('提交', ['class' => 'btn btn-success',]) ?>
        </div>
    </div>
</div>

<?php

ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php
Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">预览</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
?>

<div class="form-group">
    <select data-required="1" id="type" data-i="0">
        <option value="1" selected>住宅</option>
        <option value="2">车位</option>
    </select>
    <label>类型</label>
</div>

<div class="form-group">
    <select id="project" name="" data-required="">
        <option value="">请选择</option>
        <option value="<?= $model->project_house_id?>"><?=$model->project->house_name?></option>
    </select>

    <label>小区</label>

    <div id="houseChoose">

    </div>
</div>

<?php
Modal::end();
?>

<?php
Modal::begin([
    'id' => 'alert-Modal',
    'header' => '<h4 class="modal-title" id="myModalLabel">提示</h4>',
    'footer' => '<button class="btn btn-primary" data-dismiss="modal">关闭</button>',
]);
?>
    <div id="alert-content">

    </div>

<?php
Modal::end();
?>


<?php \common\widgets\JavascriptBlock::begin(); ?>
<script type="text/javascript">

    /*$('#type').on('change', function (){
        $('#houseChoose').html('');
    });*/

    $('#submit').on('click', function (e) {
        var $form = $("#<?php echo $model->formName();?>");
        $.ajax({
            url: $form.attr('action'),
            type: 'post',
            data: $form.serialize(),
            success: function (res) {
                if (res.code == '0') {
//                    $('#alert-content').html('修改成功，可点击“预览效果”查看');
                    location.reload();
                } else {
//                    $('#alert-content').html(res.message);
                    alert(res.message);

                }
//                $('#alert-Modal').modal('show');
            },
            dataType: 'json'
        });
    });

    $('#project,#type').on('change', function () {
        var type = $('#type').val();
        var project = $('#project').val();

        $.getJSON('house-query', {houseId: project,group:type}, function (res) {
            if(res.code==0){
                $('#houseChoose').html('');
                $.each(res.data.structure, function (i, row) {
                    $('#houseChoose').append(
                        $('<div/>')
                            .append(
                                $('<select/>').attr('data-required',1).attr('data-i',i)
                            )
                            .append(
                                $('<label/>').html(row)
                            ).addClass('form-group')
                    );
                });
                $('#houseChoose select:last').attr('data-last',1);
                $('#houseChoose select[data-i=0]').append($('<option/>').attr('value', '').html('请选择'));
                $.each(res.data.list, function (houseId, row) {
                    $('#houseChoose select[data-i=0]').append($('<option/>').attr('value', row.house_id).html(row.house_name));
                });
            }
        });

        $('#houseChoose').html('');

    }).trigger('change');

    $('#houseChoose').on('change','select', function () {
        var i = $(this).attr('data-i');
        i++;
        $('#houseChoose select[data-i]').each(function(){
            if($(this).attr('data-i')>=i){
                $(this).parent().show();
                $(this).attr('data-required',1);
                $('option',this).remove();
            }
        });
        var target = $('#houseChoose select[data-i='+i+']');
        var houseId=$(this).val();
        var type = $('#type').val();

        $.getJSON('house-query', {houseId: houseId,group: type}, function (res) {
            target.append($('<option/>').attr('value', '').html('请选择'));
            $.each(res.data.list, function (houseId, row) {
                target.append($('<option/>').attr('value', row.house_id).html(row.house_name));
            });
            if(res.data.list.length==0){
                $('input[name=houseId]').val(houseId);
                $('#houseChoose select[data-i]').each(function(){
                    if($(this).attr('data-i')>=i){
                        $(this).parent().hide();
                        $(this).removeAttr('data-required');
                    }
                });
            }
        });
    });


</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
