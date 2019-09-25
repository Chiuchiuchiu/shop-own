<?php
use \components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectServiceCategory */
/* @var array $projectList */

$this->title = '添加便民电话';
$this->params['breadcrumbs'][] = ['label' => '便民电话管理', 'url' => ['telephone']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['category']]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);

echo $form->field($model,'name')->textInput();
echo $form->field($model,'status')->dropDownList(\common\models\ProjectServiceCategory::statusMap());
echo $form->field($model,'project_house_id')->dropDownList($projectList, ['id' => 'getCategory']);

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

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">
    $(function () {
        $('#getCategory').change(function () {
            var id = $(this).val();

            $.ajax({
                type: 'GET',
                url: 'get-categorise',
                async: false,
                data: {id:id},
                success: function (data) {
                    if(data.code == 0){
                        $('#projectservicephone-category_id').html('');
                        $('#projectservicephone-category_id').append('<option value="">请选择</option>');

                        $.each(data.data, function (key, val) {
                            $('#projectservicephone-category_id').append('<option value="'+val.id+'">'+val.name+'</option>');
                        });

                        $('#projectservicephone-category_id').css('display', 'block');
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
