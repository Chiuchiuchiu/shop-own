<?php
use \components\inTemplate\widgets\ActiveForm;
use common\models\Project;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $dateTime  */
/* @var array $projectRegion */

$this->title = '新增商铺';
$this->params['breadcrumbs'][] = ['label' => '商铺管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?php

echo $form->field($model,'name')->textInput();
echo $form->field($model,'category_id')->dropDownList(\common\models\ShopCategory::statusMap());
echo $form->ajaxUpload($model,'logo', 'logo', 'logo', '商铺LOGO');
echo $form->field($model,'mobile')->textInput();
echo $form->field($model,'password')->textInput();
echo $form->field($model,'status')->dropDownList(Project::statusMap());


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

    });


</script>

<?php \common\widgets\JavascriptBlock::end();?>
