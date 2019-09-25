<?php
use \components\inTemplate\widgets\ActiveForm;
use common\models\Project;

/* @var $this yii\web\View */
/* @var $model common\models\ButlerVisitIndicators */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var integer $years */
/* @var array $butler */
/* @var array $projectList */

$this->title = '新增管家指标';
$this->params['breadcrumbs'][] = ['label' => '管家指标', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<div class="form-group field-butlervisitindicators-project_house_id required">
    <label class="control-label col-sm-3" for="butlervisitindicators-project_house_id">项目</label>
    <div class="col-sm-6">
        <?php echo \components\inTemplate\widgets\Chosen::widget([
            'name' => 'ButlerVisitIndicators[project_house_id]',
            'addClass' => 'selectedProject',
            'items' => $projectList,
        ])?>
        <div class="help-block help-block-error"></div>
    </div>
</div>

<div class="form-group field-butlervisitindicators-butler_id required">
    <label class="control-label col-sm-3" for="butlervisitindicators-butler_id">管家</label>
    <div class="col-sm-6">
        <?php echo \components\inTemplate\widgets\Chosen::widget([
            'name' => 'ButlerVisitIndicators[butler_id]',
            'addClass' => 'selectedButler',
            'items' => $butler,
        ])?>
        <div class="help-block help-block-error"></div>
    </div>
</div>

<div class="form-group field-butlervisitindicators-years required">
    <label class="control-label col-sm-3" for="butlervisitindicators-years">年份</label>
    <div class="col-sm-6">
        <?= \kartik\date\DatePicker::widget([
            'model' => $dateTime,
            'attribute' => 'startDate',
            'options' => ['placeholder' => '年份', 'value' => $years, 'name' => 'ButlerVisitIndicators[years]'],
            'type' => \kartik\date\DatePicker::TYPE_INPUT,
            'language' => 'zh-CN',
            'value' => date('Y', time()),
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy',
                'startView' => 2,
                'minViewMode' => 2,
                'maxViewMode' => 2,
            ]
        ]) ?>
        <div class="help-block help-block-error "></div>
    </div>

</div>

<?php

echo $form->field($model, 'management_number')->input('number', ['value' => 0]);
echo $form->field($model, 'reside_number')->input('number', ['value' => 0]);
echo $form->field($model,'the_first_quarter')->input("number");
echo $form->field($model,'second_quarter')->input("number");
echo $form->field($model,'third_quarter')->input("number");
echo $form->field($model,'fourth_quarter')->input("number");

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

<div id="modal-form" style="display: none;" aria-hidden="true" class="modal fade in">
    <div class="modal-backdrop fade in"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text-center send-tips">
                            正在获取管家列表…………
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="display: none">
                <button type="button" class="btn btn-white closeModal" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">

    $('.selectedProject').on('change', function () {
        var projectWords = $(this, "option:selected").val();

        if(projectWords.length < 1){
            return false;
        }

        $.ajax({
            type: 'GET',
            url: 'get-project-butler-list',
            data: {projectHouseId:projectWords},
            beforeSend: function (){
                $('.send-tips').html('正在获取管家列表…………');
                $('#modal-form').show();
            },
            success: function (data) {
                var html = "<option value=''>选择管家</option>";

                if(data.data.length > 0){
                    $.each(data.data, function (key, value){
                        html += '<option value="'+value.id+'">'+value.nickname+'</option>';
                    });
                }

                $(".selectedButler").empty();
                $(".selectedButler").html(html);
                $(".selectedButler").trigger('chosen:updated');
                $('#modal-form').hide();
            },
            dataType: 'json'
        });
    });

    $('#w0').on('submit', function (){
        var projectId = $('.selectedProject option:selected').val();
        var butlerId = $('.selectedButler option:selected').val();

        console.log("ProjectIdLength:"+ projectId.length);
        console.log("ButlerIdLength:" + butlerId.length);

        if(projectId.length < 1 || butlerId.length < 1){
            $('.send-tips').html('请选择项目以及该项目管家！必选');
            $('#modal-form').show();

            setTimeout(function(){
                $('#modal-form').hide();
            }, 2000);
            return false;
        }


    });

</script>

<?php \common\widgets\JavascriptBlock::end();?>
