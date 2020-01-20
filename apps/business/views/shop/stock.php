<?php
use components\inTemplate\widgets\ActiveForm;
use common\models\Shop;

/** @var \common\models\Shop $model */

$this->title = '编辑减库存类型';
$this->params['breadcrumbs'][] = ['label' => '店铺设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>
<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<?php

echo $form->field($model,'inventory_type')->checkboxList(Shop::inventoryMap(),
    ['item' => function($index, $label, $name, $checked, $value) use ($model){
        $checkStr = in_array($value, explode(',', trim($model->inventory_type, ','))) ? "checked" : "";
        $res = "<label><input type='checkbox' name='Shop[inventory_type][]' value='{$value}' {$checkStr}> {$label}</label><br>";

        return $res;
    }
]);

?>


<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
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
