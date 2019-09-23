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

$this->title = '开票失败反馈记录';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['fpzz/index'])])
?>

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
            'label' => '微信昵称',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->member->nickname, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '用户名',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->member->name, '', ['class' => 'text-plain']);
            }
        ],
        'fpzz_result_id',
        [
            'label' => 'OrderFpID',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->pmOrderFpzzResult->pm_order_fpzz_id, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '开票记录ID',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->pmOrderFpzzResult->result_id, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '相关记录FpItems',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->pmOrderFpzzResult->item_ids, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '状态',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->pmOrderFpzzResult->statusText, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '金额合计',
            'format' => 'raw',
            'value' => function (\common\models\FpzzFeedback $model){
                return \yii\helpers\Html::label($model->pmOrderFpzzResult->jehj, '', ['class' => 'text-success']);
            }
        ],
        'ip',
        'created_at:datetime',
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
