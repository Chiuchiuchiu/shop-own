<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $shopId 商铺id */
/* @var $shopList 商铺列表 */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $number string */
/* @var array $projectRegion */
/* @var int $projectRegionId */

use common\models\Order;
use \components\inTemplate\widgets\Html;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/order')]);
?>
    <div class="form-group">
        <div class="col-sm-4">
            <?= \kartik\date\DatePicker::widget([
                'model' => $dateTime,
                'attribute' => 'startDate',
                'attribute2' => 'endDate',
                'options' => ['placeholder' => '开始时间'],
                'options2' => ['placeholder' => '结束时间'],
                'type' => \kartik\date\DatePicker::TYPE_RANGE,
                'language' => 'zh-CN',
                'separator' => "至",
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-m-d'
                ]
            ]) ?>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('status', $status,\yii\helpers\ArrayHelper::merge(['' => '全部状态'], Order::statusMap()))?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('shop_id', $shopId, $shopList)?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <input type="text" name="number" placeholder="订单号" value="<?= $number ?>" class="form-control">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">查找</button>
                </span>
            </div>
        </div>
    </div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

    <div class="col-lg-2">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?= \components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'label' => '所属店铺',
                'format' => 'raw',
                'value' => function(Order $model){
                    return $model->shop->name ?? '-';
                }
            ],
            'order_number',
            'goods_amount',
            'discount_amount',
            'express_amount',
            'total_amount',
            'statusText',
            [
                'label' => '下单时间',
                'format' => 'raw',
                'value' => function(Order $model){
                    return $model->created_at ? date('Y-m-d H:i', $model->created_at) : '-';
                }
            ],
            [
                'label' => '支付时间',
                'format' => 'raw',
                'value' => function(Order $model){
                    return $model->paid_at ? date('Y-m-d H:i', $model->paid_at) : '-';
                }
            ],
            [
                'label' => '收货时间',
                'format' => 'raw',
                'value' => function(Order $model){
                    return $model->receiving_at ? date('Y-m-d H:i', $model->receiving_at) : '-';
                }
            ],
            [
                'label' => '完成时间',
                'format' => 'raw',
                'value' => function(Order $model){
                    return $model->finish_at ? date('Y-m-d H:i', $model->finish_at) : '-';
                }
            ],
        ],
    ])
])?>
<?php \common\widgets\JavascriptBlock::begin();?>
<script>

</script>
<?php \common\widgets\JavascriptBlock::end();?>
