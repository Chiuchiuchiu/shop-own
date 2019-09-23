<?php
use \components\inTemplate\widgets\ActiveForm;
use common\models\Project;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $dateTime  */
/* @var array $projectRegion */

$this->title = '新增楼盘';
$this->params['breadcrumbs'][] = ['label' => '楼盘管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

    <div class="form-group required">
        <label class="control-label col-sm-3" for="project-words">输入项目名称</label>
        <div class="col-sm-4">
            <input id="project-words" class="form-control" type="text">
            <div id="projectWordsError" class="help-block help-block-error "></div>
        </div>

        <div class="col-sm-4">
            <a id="searchProject" class="btn btn-w-m btn-info">查询</a>
        </div>

    </div>

<?php
echo $form->field($model,'house_name')->dropDownList([], ['style' => 'display:none']);
echo $form->field($model,'url_key')->textInput();
echo $form->field($model,'area')->textInput();
echo $form->ajaxUpload($model,'logo', 'logo', 'logo', '楼盘LOGO(660w)');
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

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">

    $(function () {
        $('#searchProject').click(function () {
            var projectWords = $('#project-words').val();

            if(projectWords.length < 2){
                return false;
            }

            $.ajax({
                type: 'GET',
                url: 'search-projects',
                async: false,
                data: {projectName:projectWords},
                success: function (data) {
                    if(data.code == 0){
                        $('#project-house_name').html('');
                        $('#project-house_name').append('<option value="">请选择</option>');

                        $.each(data.data, function (key, val) {
                            $('#project-house_name').append('<option value="'+val+'">'+val+'</option>');
                        });

                        $('#project-house_name').css('display', 'block');
                    } else {
                        alert(data.message);
                    }
                },
                dataType: 'json'
            });
        });
    });


</script>

<?php \common\widgets\JavascriptBlock::end();?>
