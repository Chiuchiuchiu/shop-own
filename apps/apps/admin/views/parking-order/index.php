<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $Amount double */
/** @var $status string */
/** @var $plateNumber string */
/** @var $projects array */
/** @var $house_id integer */

$this->title = '账单';
$this->params['breadcrumbs'][] = $this->title;
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['parking-order/'])])
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
        <?= Html::dropDownList('status', $status, ['' => '全部', '已付款', '已退款'], ['class' => 'input-group form-control'])?>
    </div>

    <div class="col-sm-2">
        <label for="">&nbsp;</label>
        <input name="plate-number" class="form-control" type="text" value="<?= $plateNumber ?>" placeholder="车牌号">
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
    <a class="btn btn-success pull-left" href="javascript:void();">总金额：<?=$Amount?> （元）</a>
    <?php
        echo Html::a(
            '导出当前数据报表',
            ['export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'house_id' => $house_id, 'status' => $status, 'plate-number' => $plateNumber],
            ['class'=>'btn btn-info pull-right']
        );
    ?>
</div>

<div class="col-lg-12">
    <div>总条数（明细）：<?= $dataProvider->totalCount ?></div>
</div>

<?php

\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '姓名',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->member->showName, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '手机号',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->member->phone, '');
            }
        ],
        'plate_number',
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->project->house_name, '', ['class' => 'text-success']);
            }
        ],
        'parkingTypeText',
        [
            'label' => '订单号',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->number, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '道闸订单号',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->calc_id, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '类型',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                if($model->type == \common\models\ParkingOrder::TYPE_T){
                    return Html::label($model->typeText, '', ['style' => 'color:#3cce48']);
                }

                return Html::label($model->typeText, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '量（月）',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                if($model->type == \common\models\ParkingOrder::TYPE_M){
                    return Html::label($model->quantity, '', ['class' => 'text-info']);
                }

                return '-';
            }
        ],
        [
            'label' => '缴费开始日期',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return $model->effect_date > 0 ? date('Y-m-d', $model->effect_date) : '-';
            }
        ],
        [
            'label' => '缴费结束日期',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return $model->expire_date > 0 ? date('Y-m-d', $model->expire_date) : '-';
            }
        ],
        [
            'label' => '金额',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->amount, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '状态',
            'format' => 'raw',
            'value' => function(\common\models\ParkingOrder $model){
                return Html::label($model->statusText, '', ['class' => 'text-danger']);
            }
        ],
        'payed_at:datetime',
        'send_at:datetime',
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model, $key) {

                    if($model->status != '2000'){
                        return Html::a('退款', '#', [
                            'data-toggle' => 'modal',
                            'data-target' => '#update-modal',
                            'class' => 'data-update',
                            'data-id' => $key,
                        ]);
                    } else {
                        return '';
                    }

                },
            ],
        ],
    ],
]);
\components\inTemplate\widgets\IBox::end();


?>


<?php
use yii\bootstrap\Modal;
// 更新操作
Modal::begin([
    'id' => 'update-modal',
    'header' => '<h4 class="modal-title">退款</h4>',
    'footer' => '<a href="javascript:void(0);" id="submit" class="btn btn-primary">确认</a><a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
$requestUpdateUrl = \yii\helpers\Url::toRoute('update');
$updateJs = <<<JS
    var orderId = 0;

    $('.data-update').on('click', function () {
        $('.modal-body').html('是否确认退款？');
        
        orderId = $(this).closest('tr').data('key');

    });
    
    
    $('#submit').on('click', function (){
        $.post('{$requestUpdateUrl}', {id: orderId}, function (data){
            if(data.code == 0){
                if(confirm(data.data.message)){
                    window.location.reload();
                }
            } else {
                alert(data.message)
            }
        }, 'json');
    });


JS;
$this->registerJs($updateJs);
Modal::end();
?>​


<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">

    $(function () {

        function createDropDownList(parent_id) {
            var o = $('.ctr-template').clone();
            var url = null;
            o.removeClass('ctr-template');
            $('#region_ctr').append(o);
            var s = $('select',o);

            s.bind('change',function(){
                //remove right
                o.siblings('.sl').slice(o.index()).remove();
                if(s.val() != ''){
                    $('#house_id').val(s.val());
                }
            });

            switch(parent_id){
                case 0:
                    url = 'find-project';
                    break;
                default:
                    url = 'find-project';
                    break;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {id:parent_id},
                success: function (res) {
                    if(res.code==0){
                        s.html('');
                        if(res.data.list.length>0){
                            $('<option value="">请选择</option>').appendTo(s)
                            $.each(res.data.list,function(i,e){
                                var areaName = '';
                                if(typeof(e.area) === 'string' && e.area != ''){
                                    areaName = e.area + '--';
                                }
                                $('<option value="'+e.house_id+'">'+ areaName + e.house_name +'</option>').appendTo(s)
                            });
                            s.attr('readonly',false)
                        }else{
                            s.remove();
                        }
                    }else{
                        toastr.error(res.message?res.message:res)
                    }
                },
                async: false,
                dataType: 'json'
            });
        }

//        createDropDownList(0);
    });

</script>

<?php \common\widgets\JavascriptBlock::end();?>