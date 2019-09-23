<?php
use components\inTemplate\widgets\Html;
use common\models\RepairHold;
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/6
 * Time: 15:50
 */

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $status integer */
/* @var $dateTime \common\valueObject\RangDateTime */

$this->title = '暂挂列表';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['repair/hold'])])
?>

    <div class="form-group">
        <div class="col-sm-7">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-4 sl ctr-template">
                    <?= \components\inTemplate\widgets\Chosen::widget([
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

            <?= Html::dropDownList('status', $status, RepairHold::statusMap(), ['class' => 'input-group form-control'])?>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<div class="col-lg-12">总记录数：<?= $dataProvider->totalCount ?></div>

<?php
\components\inTemplate\widgets\IBox::begin();
echo \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '业主姓名',
            'value' => function(RepairHold $model){
                return $model->repair->name;
            }
        ],
        [
            'label' => '联系电话',
            'value' => function(RepairHold $model){
                return $model->repair->tel;
            }
        ],
        [
            'label' => '业主地址',
            'value' => function(RepairHold $model){
                return $model->repair->address;
            }
        ],
        [
            'format' => 'raw',
            'label' => '报事状态',
            'value' => function(RepairHold $model){
                return \components\za\Html::label($model->repair->getStatusText(), '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '报事内容',
            'value' => function(RepairHold $model){
                return isset($model->repair->content) ? mb_substr($model->repair->content, 0, 20) . '....': '';
            }
        ],
        'content',
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{setStatus}',
            'buttons' => [
                'setStatus' => function ($key,RepairHold $model, $index){
                    switch ($model->status){
                        case RepairHold::STATUS_WAIT:
                            return "<span class='btn btn-xs btn-primary setStatus' data-id='{$model->id}' data-status='2'>通过</span> <span class='btn btn-xs btn-danger setStatus' data-id='{$model->id}' data-status='3'>不通过</span>";break;
                        case RepairHold::STATUS_YES:
                            return "<span class='btn btn-xs btn-success'>已审核通过</span>"; break;
                        case RepairHold::STATUS_NO:
                            return "<span class='btn btn-xs btn-warning'>已审核不通过</span>"; break;
                        default :
                            return "<span class='btn btn-xs btn-default'>未知</span>"; break;
                    }
                }
            ],
        ],
    ],
]);

\components\inTemplate\widgets\IBox::end();

?>

<?php \yii\widgets\ActiveForm::begin(['method' => 'POST', 'action' => '/repair/search-status', 'id' => 'form-f']) ?>
    <input type="hidden" name="id" value="" id="idV">
<?php \yii\widgets\ActiveForm::end() ?>

<?php \common\widgets\JavascriptBlock::begin() ?>
<script type="text/javascript">
    $('.setStatus').click(function(){

        var status = $(this).data('status');

        var id = $(this).data('id');

        layer.confirm("您确定审核该暂挂吗？", {
                btn: ['确定', '取消']},
            function(){
                var ii = layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.ajax({
                    type: 'get',
                    url: "/repair/set-status?id=" + id + "&status=" + status,
                    timeout: 3000, //超时时间：30秒
                    dataType:'json',
                    success: function (data) {
                        if(data.code == 0){
                            layer.msg("审核成功", {
                                time:500,
                                end:function(){
                                    location.reload();
                                }
                            })
                        }else{
                            layer.close(ii);
                            layer.msg(data.message);
                        }
                    }
                });
            },
            function(){}
        )

    })

</script>
<?php \common\widgets\JavascriptBlock::end() ?>
