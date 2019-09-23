<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $Amount double */
/* @var $status string */
/* @var $number string */
/* @var $projects array */
/* @var $house_id integer */
/* @var $pmOrderCount integer */
/* @var $discountStatus integer */

$this->title = '退款订单列表';
$this->params['breadcrumbs'][] = $this->title;
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['pm-order/refund-lists'])])
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
        <?= Html::dropDownList('status', $status, ['' => '全部(状态)', '状态' => \common\models\PmOrderRefund::statusType()], ['class' => 'input-group form-control'])?>
    </div>

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?= Html::input('text', 'number', $number, ['class' => 'input-group form-control', 'placeholder' => '退款单号'])?>
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

<div class="col-lg-12">
    <a class="btn btn-success pull-left" href="javascript:void();">总金额：<?= $Amount ?> （元）</a>
    <br/>
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
            'value' => function(\common\models\PmOrderRefund $model){
                return Html::label($model->pmOrder->member->showName, '', ['class' => 'text-info']);
            }
        ],
        'refund_number',
        'reason',
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\PmOrderRefund $model){
                return Html::label($model->project->house_name, '', ['class' => 'text-success']);
            }
        ],
        [
            'label' => '房产',
            'format' => 'raw',
            'value' => function(\common\models\PmOrderRefund $model){
                return Html::label($model->house->ancestor_name, '', ['class' => 'text-info']);
            }
        ],
        'statusText',
        [
            'label' => '实付金额',
            'format' => 'raw',
            'value' => function(\common\models\PmOrderRefund $model){
                return Html::label($model->amount . '元', '', ['class' => 'text-warning']);
            }
        ],
        'created_at:datetime',
        [
            'label' => '退款时间',
            'format' => 'raw',
            'value' => function(\common\models\PmOrderRefund $model){
                if($model->updated_at > 0){
                    return Html::label(date('Y-m-d H:i:s', $model->updated_at), '', ['class' => 'text-warning']);
                }

                return '-';
            }
        ],
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{order-check}',
            'buttons' => [
                'order-check' => function ($url, $model, $key) {
                    if($model->status != \common\models\PmOrderRefund::STATUS_SUCCESS){
                        return Html::a("<span class='btn btn-xs btn-success'>退款结果查询</span>",
                            "javascript:;",
                            [
                                'class' => 'check',
                                'data-id' => $model->id,
                            ]);
                    }else{
                        return "";
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
                url: "/pm-order/refund-order-check?id=" + id,
                timeout: 3000, //超时时间：30秒
                dataType:'json',
                success: function (data) {
                    if(data.code == 0){
                        layer.alert("退款状态：" + data.data.status + "<br/>金额：" + data.data.amount + "元<br/>备注：" + data.data.remark);
                    }else{
                        layer.msg(data.message);
                    }
                    layer.close(ii);
                }
            });

        })
    </script>
<?php \common\widgets\JavascriptBlock::end();?>