<?php
use \components\inTemplate\widgets\ActiveForm;
use common\models\ProjectServicePhone;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var array $projectList */

$this->title = '添加电话分类';
$this->params['breadcrumbs'][] = ['label' => '便民电话管理', 'url' => ['category']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['category']]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);

echo $form->field($model,'name')->textInput();
echo $form->field($model,'status')->dropDownList(ProjectServicePhone::statusMap());
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

    });
</script>

<?php \common\widgets\JavascriptBlock::end();?>
