<?php

use yii\helpers\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ PmOrderRefund */
/* @var $orderInfo common\models\ PmOrder */
/* @var array $projectsArray */

\components\inTemplate\widgets\IBox::begin();
?>

<?php $form = ActiveForm::begin(); ?>

<div class="form-group">
    <label class="control-label col-sm-3">原订单号</label>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-9">
                <input type="text" readonly class="form-control" value="<?= $orderInfo->number ?>" >
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3">缴费金额</label>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-9">
                <input type="text" readonly class="form-control" value="<?= $orderInfo->total_amount ?>" >
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3">房号</label>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-9">
                <input type="text" readonly class="form-control" value="<?= $orderInfo->house->ancestor_name ?>" >
            </div>
        </div>
    </div>
</div>

<?= $form->field($model, 'reason')->textarea(['rows' => 5]) ?>
<?= $form->field($model, 'pm_order_id')->hiddenInput(['value' => $orderInfo->id])->label(false) ?>

<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => 'btn btn-warning']) ?>

</div>

<?php ActiveForm::end();
\components\inTemplate\widgets\IBox::end();

?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script>


</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
