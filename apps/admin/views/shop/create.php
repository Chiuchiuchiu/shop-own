<?php
use \components\inTemplate\widgets\ActiveForm;
use common\models\Shop;

/* @var $this yii\web\View */
/* @var $model common\models\Shop */
/* @var $categoryInfo 商铺分类 */
/* @var $ShopOfficialFileModel common\models\ShopOfficialFile*/

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
echo $form->field($model,'category_id')->dropDownList($categoryInfo);
echo $form->ajaxUpload($model,'logo', 'logo', 'logo', '商铺LOGO');
echo $form->ajaxUpload($ShopOfficialFileModel,'id_card_img', 'id_card_img', 'id_card_img', '负责人身份证');
echo $form->ajaxUpload($ShopOfficialFileModel,'license_img', 'license_img', 'license_img', '营业执照');
echo $form->field($model,'icon_name')->textInput();
echo $form->field($model,'mobile')->textInput();
echo $form->field($model,'email')->textInput();
echo $form->field($model,'platform_commission')->textInput();
echo $form->field($model,'description')->textarea();
echo $form->field($model,'service_type')->checkboxList(Shop::serviceTypeMap());
echo $form->field($model,'status')->dropDownList(\yii\helpers\ArrayHelper::merge([''=>'请选择'], Shop::statusMap()));
echo $form->field($model,'inventory_type')->dropDownList(\yii\helpers\ArrayHelper::merge([''=>'请选择'], Shop::inventoryMap()));

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
