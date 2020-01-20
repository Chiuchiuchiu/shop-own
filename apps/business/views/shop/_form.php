<?php

use components\inTemplate\widgets\ActiveForm;
use common\models\Shop;


/* @var $this yii\web\View */
/* @var $model \common\models\Shop */
/* @var $shopOfficialFileModel \common\models\ShopOfficialFile */
/* @var $shopManager \common\models\ShopManager */
/* @var array $categoryInfo */
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?php

echo $form->field($model,'name')->textInput();
echo $form->field($model,'category_id')->dropDownList($categoryInfo);
echo $form->ajaxUpload($model,'logo', 'logo', 'logo', '商铺LOGO');
echo $form->ajaxUpload($shopOfficialFileModel,'id_card_img', 'id_card_img', 'id_card_img', '负责人身份证');
echo $form->ajaxUpload($shopOfficialFileModel,'license_img', 'license_img', 'license_img', '营业执照');
echo $form->field($model,'icon_name')->textInput();
echo $form->field($shopManager,'name')->textInput();
echo $form->field($shopManager,'mobile')->textInput();
echo $form->field($shopManager,'email')->textInput();
echo $form->field($model,'platform_commission')->textInput();
echo $form->field($model,'description')->textarea();
echo $form->field($model,'service_type')->checkboxList(Shop::serviceTypeMap(), ['item' => function($index, $label, $name, $checked, $value) use ($model){

    $checkStr = in_array($value, explode(',', trim($model->service_type, ','))) ? "checked" : "";
    $res = "<label><input type='checkbox' name='Shop[service_type][]' value='{$value}' {$checkStr}> {$label}</label>";
    $index % 3 == 2 && $res .= "<br>";

    return $res;
}
]);
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
