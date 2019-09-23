<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $Amount double */
/** @var $status string */
/** @var $number string */
/** @var $projects array */
/** @var $house_id integer */
/** @var $pmOrderCount integer */
/** @var $discountStatus integer */

$this->title = '缴费订单列表';
$this->params['breadcrumbs'][] = $this->title;
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['pm-order/order-lists'])])
?>

<div class="form-group">

    <div class="col-sm-7">
        <label>项目</label>
        <div id="region_ctr" class="row">
            <div class="col-sm-4 sl ctr-template">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projects,
                ])?>

            </div>

        </div>
    </div>

</div>

<div class="form-group">
    <div class="col-sm-4">
        <label>时间</label>
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
        <label>&nbsp;</label>
        <?= Html::dropDownList('status', $status, ['' => '已付款', '已退款'], ['class' => 'input-group form-control'])?>
    </div>

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?= Html::dropDownList('discountStatus', $discountStatus, ['' => '是否优惠-全部', '未优惠', '已优惠'], ['class' => 'input-group form-control'])?>
    </div>

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?= Html::dropDownList('payType', $payType, ['' => '支付渠道-全部', 2=>'微信官方', 3=>'招商微信', 4=>'小程序', 5=>"民生微信"], ['class' => 'input-group form-control'])?>
    </div>

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?= Html::input('text', 'number', $number, ['class' => 'input-group form-control', 'placeholder' => '订单号'])?>
    </div>

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
    </div>
</div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php \components\inTemplate\widgets\IBox::begin(['title' => '导出']); ?>

<div class="form-group">
    <div class="col-sm-2">
        <?php
        echo Html::a(
            '导出各项目缴费总额',
            ['export-project-order-amount', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(),],
            ['class'=>'btn btn-info']
        );

        ?>
    </div>

    <div class="col-sm-2">
        <?php
        echo Html::a(
            '导出各项目优惠类目总汇',
            ['export-discounts-item', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(),],
            ['class'=>'btn btn-info']
        );

        ?>
    </div>

    <div class="col-sm-2">
        <?php
        echo Html::a(
            '导出当前数据报表（无订单明细）',
            ['export-discounts-not-item', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(),'house_id' => $house_id, 'number' => $number,'discountStatus' => $discountStatus],
            ['class'=>'btn btn-info']
        );

        ?>
    </div>

</div>

<?php \components\inTemplate\widgets\IBox::end(); ?>


<div class="col-lg-12">
    <a class="btn btn-success pull-left" href="javascript:void();">总金额：<?= $Amount ?> （元）</a>
    <?php
    echo Html::a(
        '导出当前数据报表',
        ['export-parent', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'house_id' => $house_id, 'number' => $number,'discountStatus' => $discountStatus],
        ['class'=>'btn btn-info pull-right']
    );

    ?>
</div>

<div class="col-lg-12">
    <div>总单数：<?= $dataProvider->totalCount ?>；</div>
</div>

<?php

\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '用户',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                return Html::label($model->member->showName, '', ['class' => 'text-info']);
            }
        ],
        'number',
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                return Html::label($model->project->house_name, '', ['class' => 'text-success']);
            }
        ],
        [
            'label' => '房产',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                return Html::label($model->house->ancestor_name, '', ['class' => 'text-info']);
            }
        ],
        'payTypeText',
        [
            'label' => '缴费类型',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                return Html::label($model->project->projectFeeCycle->name, '');
            }
        ],
        [
            'label' => '实付金额',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                return Html::label($model->total_amount . '元', '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '优惠金额',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                if(isset($model->pmOrderDiscounts)){
                    return Html::label($model->pmOrderDiscounts->discounts_amount . '元', '', ['class' => 'text-warning']);
                }

                return '-';
            }
        ],
        'billTypeText',
        'payed_at:datetime',
        'created_at:datetime',
        [
            'label' => '退款时间',
            'format' => 'raw',
            'value' => function(\common\models\PmOrder $model){
                if($model->refund_at > 0){
                    return Html::label(date('Y-m-d H:i:s', $model->refund_at), '', ['class' => 'text-warning']);
                }

                return '-';
            }
        ],
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{show-item} {refund} {order-check}',
            'buttons' => [
                'show-item' => function ($url, $model, $key) {

                    return Html::a("<span class='btn btn-xs btn-primary'>查看明细</span>", '/pm-order/show-item?pmOrderId=' . $model->id);

                },
                'refund' => function ($url, $model, $key) {
                    if($model->pay_type == \common\models\PmOrder::PAY_TYPE_SUB_MS_GDAZWY){
                        return Html::a("<span class='btn btn-xs btn-danger'>申请退款</spam>", '/pm-order/refund?pmOrderId=' . $model->id);
                    }
                },
                'order-check' => function ($url, $model, $key) {
                    if($model->pay_type == \common\models\PmOrder::PAY_TYPE_SUB_MS_GDAZWY){
                        return Html::a("<span class='btn btn-xs btn-success'>支付结果查询</span>",
                            "javascript:;",
                            [
                                'class' => 'check',
                                'data-id' => $model->id,
                            ]);
                    }
                },
            ],
        ]
    ],
]);
\components\inTemplate\widgets\IBox::end();
?>
<?php \common\widgets\JavascriptBlock::begin();?>
    <script>
        $('.check').click(function(){

            var id = $(this).data('id');

            var ii = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });

            $.ajax({
                type: 'get',
                url: "/pm-order/order-check?id=" + id,
                timeout: 3000, //超时时间：30秒
                dataType:'json',
                success: function (data) {
                    if(data.code == 0){
                        layer.alert("支付状态：" + data.data.status + "<br/>金额：" + data.data.amount + "元<br/>备注：" + data.data.remark);
                    }else{
                        layer.msg(data.message);
                    }
                    layer.close(ii);
                }
            });

        })
    </script>
<?php \common\widgets\JavascriptBlock::end();?>