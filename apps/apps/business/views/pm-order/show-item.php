<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $Amount double */
/** @var $status string */
/** @var $projects array */
/** @var $house_id integer */
/** @var $pmOrderCount integer */
/* @var integer $pmOrderStatus */

$this->title = '账单';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget();

?>

    <div class="col-lg-12">
        <div class="col-sm-1 pull-right">
        <?php
            echo \yii\helpers\Html::a(
                '计算总额',
                null,
                ['class'=>'btn btn-info sum-amount']
            );
        ?>
        </div>

    </div>

<?= \components\inTemplate\widgets\IBox::widget([
    'content' => \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'label' => '姓名',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label($model->pmOrder->member->showName, '', ['class' => 'text-info']);
                }
            ],
            'customer_name',
            [
                'label' => '手机号',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label($model->pmOrder->member->phone, '', ['class' => 'text-info']);
                }
            ],
            'houseFullName',
            'contract_no',
            [
                'label' => '微信订单号',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label($model->pmOrder->number, '', ['class' => 'text-info']);
                }
            ],
            'bill_date',
            'charge_item_name',
            [
                'label' => '面积/用量',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label($model->usage_amount, '', ['class' => 'text-info']);
                }
            ],
            [
                'label' => '金额',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label($model->amount, '', ['class' => 'text-warning data-amount', 'data-amount' => $model->amount]);
                }
            ],
            [
                'label' => '状态',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label($model->statusText, '', ['class' => 'text-danger']);
                }
            ],
            [
                'label' => '最后核销时间',
                'format' => 'raw',
                'value' => function(\common\models\PmOrderItem $model){
                    return Html::label(date('Y-m-d H:i:s',$model->completed_at ? $model->completed_at : $model->second_updated_at), '');
                }
            ],
        ],
    ])
]); ?>

<?php \common\widgets\JavascriptBlock::begin()?>

<script type="text/javascript">
    $('.sum-amount').click(function () {
        var a = 0;
        $('.data-amount').each(function (i, item) {
            a += parseFloat($(item).attr('data-amount'));
        });

        $(this).html(a.toFixed(2));
    });
</script>

<?php \common\widgets\JavascriptBlock::end()?>
