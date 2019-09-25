<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/27
 * Time: 14:33
 */

/* @var $dataProvider \yii\data\ActiveDataProvider*/
/* @var $model \common\models\PmOrderFpzz*/

$this->title = '账单：' . ' ' . $model->house_address . ' ；支付金额：' . \yii\helpers\Html::label($model->total_amount, '', ['class' => 'text-info']);

$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);

//echo \components\inTemplate\widgets\Html::buttonA('查看开票清单', ['show-item', 'id' => $model->id], ['class' => 'btn btn-w-m btn-info pull-right']);

/*echo $model->status != 3 ? \components\inTemplate\widgets\Html::buttonA('生成开票清单', ['create-item', 'id' => $model->id], ['class' => 'btn btn-w-m btn-primary pull-right']) : '';*/

\components\inTemplate\widgets\IBox::begin();
echo \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'houseFullName',
        [
            'label' => '支付订单号',
            'format' => 'raw',
            'value' => function (\common\models\PmOrderItem $model){
                return \yii\helpers\Html::label($model->pmOrder->number, '', ['class' => 'text-info']);
            }
        ],
        'contract_no',
        'bill_date',
        'charge_item_name',
        'amount',
        'statusText',
        'completed_at:datetime',
    ],
]);
\components\inTemplate\widgets\IBox::end();
