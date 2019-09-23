<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/27
 * Time: 10:51
 * @var \yii\web\View $this
 */

/* @var $dataProvider \yii\data\ActiveDataProvider*/
/** @var $house_id integer */
/** @var $projects array */
/** @var $type int */
/* @var array|string $dateTime */
/* @var string $memberName */
/* @var string $email */
/* @var int $status */
/* @var integer $paperInvoiceCount */

$this->title = '电子发票';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['fpzz/index'])])
?>

    <div class="form-group">

        <div class="col-sm-3">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-12 sl ctr-template">

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
            <label for="">&nbsp;</label>
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
            <label for="">&nbsp;</label>
            <input name="member-name" class="form-control" type="text" value="<?= $memberName ?>" placeholder="发票抬头">
        </div>

        <div class="col-sm-2">
            <label for="">&nbsp;</label>
            <input name="email" class="form-control" type="text" value="<?= $email ?>" placeholder="电子邮箱">
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= \yii\helpers\Html::dropDownList('status', $status, ['' => '全部', 1 => '发送成功', 2 => '发送失败'], ['class' => 'input-group form-control'])?>
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

<div class="ibox float-e-margins " style="">
    <div class="ibox-content">
        <div class="form-group">

            <div class="col-sm-1">
                <a class="btn btn-info" href="/fpzz/paper" target="_blank">
                    查看纸质发票（<?= $paperInvoiceCount ?>）
                </a>
            </div>

        </div>

    </div>
</div>

    <div>总记录数：<?= $dataProvider->totalCount ?></div>

<?php
\components\inTemplate\widgets\IBox::begin();

echo \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'table table-hover table-striped table-bordered'
    ],
    'columns' => [
        'id',
        [
            'label' => '消费订单号',
            'format' => 'raw',
            'value' => function (\common\models\PmOrderFpzz $model){
                return \yii\helpers\Html::label($model->pmOrder->number);
            }
        ],
        'pm_order_id',
        'house_address',
        'email',
        'user_name',
        'register_id',
        'total_amount',
        [
            'label' => '提交状态',
            'format' => 'raw',
            'value' => function (\common\models\PmOrderFpzz $model){
                $tabClass = $model->status > 0 ? 'text-success' : 'text-danger';
                return \yii\helpers\Html::label($model->statusText, '-', ['class' => $tabClass]);
            }
        ],
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{show-pdf}',
            'buttons' => [
                'show-pdf' => function ($url, $model, $key){
                    /* @var $model \common\models\PmOrderFpzz */
                    if($model->status == \common\models\PmOrderFpzz::STATUS_SUCCESS){
                        return \components\inTemplate\widgets\Html::buttonA('查看电子发票', ['show-fped', 'id' => $model->id], ['class' => 'btn btn-primary btn-rounded', 'target' => '_blank']);
                    } else {
                        return \components\inTemplate\widgets\Html::button('查看原因', ['class' => 'btn btn-danger btn-rounded cause', 'data-id' => $model->pm_order_id]);
                    }

                }
            ]
        ]
    ],
]);

\components\inTemplate\widgets\IBox::end();

?>

<div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <?php \components\za\ActiveForm::begin(['id' => 'remarks-form']); ?>

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                    </button>
                    <h4 class="modal-title">
                        购方名称：<input style="background-color:#cccccc;" id="member-name" name="member-name" value="" readonly />
                    </h4>
                    <h5>
                        发票代码：<input style="background-color:#cccccc;" id="fpdm" name="fpdm" value="" readonly />
                    </h5>
                    <h5>
                        发票号码：<input style="background-color:#cccccc;" id="fphm" name="fphm" value="" readonly />
                    </h5>
                    <h5>
                        对应ID：<input style="background-color:#cccccc;" id="fp-id" name="fp-id" value="" readonly />
                    </h5>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>备注信息：</label>
                        <textarea placeholder="填写备注信息" class="form-control" name="remarks" id="remarks-c"></textarea>
                    </div>
                </div>

            <?php \components\za\ActiveForm::end(); ?>

            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="save">提交保存</button>
            </div>
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $('.remarks').on('click', function (){
        $('#member-name').val($(this).attr('u'));
        $('#fpdm').val($(this).attr('data-fd'));
        $('#fphm').val($(this).attr('data-fh'));
        $('#fp-id').val($(this).attr('data-id'));
    });

    $('#save').click(function (){
        var data = $('#remarks-form').serialize();
        var remarks = $('#remarks-c').val();

        if(remarks.length < 1){
            alert('请填写备注信息！');
            return false;
        }

        if(confirm('是否确认提交？')){
            $.ajax({
                type: "POST",
                url: '/fpzz/cancel',
                async: false,
                data: data,
                success: function (res){
                    if(res.code == 0){
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function (error){
                    alert('错误！');
                },
                dataType: 'json'
            });
        }

    });

    $('.cause').on('click', function(){
        var pId = $(this).attr('data-id');

        $.getJSON('/fpzz/cause', {pmOrderId:pId}, function(res){

            layer.open({
                type: 1
                ,title: false //不显示标题栏
                ,closeBtn: false
                ,area: '300px;'
                ,shade: 0.8
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['关闭']
                ,btnAlign: 'c'
                ,moveType: 1 //拖拽模式，0或者1
                ,content: '<div style="padding: 50px; line-height: 22px; background-color: ; font-weight: bold;text-align:center;">'+res.data.message+'</div>'
                ,success: function(layero){

                }
            });
        });

    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
