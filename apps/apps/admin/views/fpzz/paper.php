<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/27
 * Time: 10:51
 */

/* @var $dataProvider \yii\data\ActiveDataProvider*/
/** @var $house_id integer */
/** @var $projects array */
/** @var $type int */
/* @var array|string $dateTime */
/* @var string $memberName */
/* @var string $email */

$this->title = '纸质发票';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['fpzz/paper'])])
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
            <input name="member-name" class="form-control" type="text" value="<?= $memberName ?>" placeholder="用户名">
        </div>

        <div class="col-sm-2">
            <label for="">&nbsp;</label>
            <input name="email" class="form-control" type="text" value="<?= $email ?>" placeholder="邮箱">
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

    <div>总记录数：<?= $dataProvider->totalCount ?></div>

<?php
\components\inTemplate\widgets\IBox::begin();

echo \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '用户名',
            'value' => function (\common\models\PmOrderFpzz $model){
                return $model->member->showName;
            }
        ],
        [
            'label' => '支付订单号',
            'format' => 'raw',
            'value' => function (\common\models\PmOrderFpzz $model){
                return \yii\helpers\Html::label($model->pmOrder->number, '', ['class' => 'text-info']);
            }
        ],
        'house_address',
        'email',
        'phone',
        'user_name',
        'typeText',
        'total_amount',
        [
            'label' => '备注信息',
            'format' => 'raw',
            'value' => function (\common\models\PmOrderFpzz $model){
                return \yii\helpers\Html::label($model->remarks, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '提交状态',
            'format' => 'raw',
            'value' => function (\common\models\PmOrderFpzz $model){
                return \yii\helpers\Html::label($model->statusText, '', ['class' => 'text-success']);
            }
        ],
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{detail} {show-item}',
            'buttons' => [
                'detail' => function ($url, $model, $key){
                    /* @var $model \common\models\PmOrderFpzz*/
                    return \components\inTemplate\widgets\Html::buttonA('查看明细', ['detail', 'id' => $model->id], ['class' => 'btn btn-info btn-rounded']);
                },
                'show-item' => function ($url, $model, $key){
                    /* @var $model \common\models\PmOrderFpzz*/

                    if($model->type == 1){
                        return \components\inTemplate\widgets\Html::buttonA('查看电子发票', ['show-fped', 'pmOrderId' => $model->pm_order_id], ['class' => 'btn btn-primary btn-rounded']);
                    }

                    return \yii\helpers\Html::button('编辑', ['class' => 'remarks btn btn-primary btn-rounded', 'data-toggle' => "modal", 'data-id' => $model->id, 'o' => $model->pmOrder->number, 'u' => $model->user_name, 'data-target' => "#myModal5" ]);
                },
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
                        业主姓名：<input style="background-color:#cccccc;" id="member-name" name="member-name" value="" readonly />
                    </h4>
                    <h5>
                        订单号：<input style="background-color:#cccccc;" id="order-number" name="order-number" value="" readonly />
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
        $('#order-number').val($(this).attr('o'));
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
                url: '/fpzz/save-remarks',
                async: false,
                data: data,
                success: function (res){
                    if(res.code == 0){
                        location.reload();
                    } else {
                        alert('提交失败');
                    }
                },
                error: function (error){
                    alert('错误！');
                },
                dataType: 'json'
            });
        }

    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
