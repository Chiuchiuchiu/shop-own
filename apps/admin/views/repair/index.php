<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/6
 * Time: 15:50
 */

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $house_id integer */
/* @var $status integer */
/* @var $projectsArray array */
/* @var $dateTime \common\valueObject\RangDateTime */

$this->title = '报事报修';
$this->params['breadcrumbs'][] = $this->title;

use \common\models\Repair;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['repair/index'])])
?>

    <div class="form-group">

        <div class="col-sm-7">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-4 sl ctr-template">

                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projectsArray,
                    ])?>

                </div>

            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= \yii\helpers\Html::dropDownList('status', $status, Repair::statusList(), ['class' => 'input-group form-control'])?>
        </div>

        <div class="col-sm-5">
            <label for=""></label>
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
    <div class="col-lg-12">
        <?php
        echo \components\za\Html::a('导出当前数据报表',['export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'house_id' => $house_id, 'status' => $status],['class'=>'btn btn-info pull-right']);
        ?>

        <?php
        echo \components\za\Html::a('导出当前数据汇总',['export-collect', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'house_id' => $house_id, 'status' => $status],['class'=>'btn btn-success pull-right']);
        ?>
    </div>

    <div class="col-lg-12">总记录数：<?= $dataProvider->totalCount ?></div>

<?php
\components\inTemplate\widgets\IBox::begin();
echo \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'format' => 'raw',
            'label' => '项目',
            'value' => function(Repair $model){
                return isset($model->house->project) ? \components\za\Html::label($model->house->project->house_name, '', ['class' => 'text-success']) : '-';
            }
        ],
        'name',
        'tel',
        [
            'label' => '维修地址',
            'value' => function(Repair $model){
                return $model->address;
            }
        ],
        [
            'format' => 'raw',
            'label' => '报事类型',
            'value' => function(Repair $model){
                return \components\za\Html::tag('p', $model->flowStyleText, ['class' => 'text-info']);
            }
        ],
        [
            'format' => 'raw',
            'label' => '报事状态',
            'value' => function(Repair $model){
                return \components\za\Html::label($model->statusText, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '事物级别',
            'value' => function(Repair $model){
                return isset($model->repairResponse->level_name) ? $model->repairResponse->level_name : '';
            }
        ],
        [
            'label' => '投诉/报修内容',
            'value' => function(Repair $model){
                return mb_substr($model->content, 0, 30);
            }
        ],
        'reception_user_name',
        'order_user_name',
        'created_at:datetime',
        [
            'label' => '受理时间',
            'value' => function(Repair $model){
                return isset($model->repairResponse->created_at) ? date('Y-m-d H:i:s', $model->repairResponse->created_at) : '';
            }
        ],
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{detail} {s-status}',
            'buttons' => [
                'detail' => function ($url, $model, $key){
                    /* @var $model \common\models\PmOrderFpzz*/
                    return \components\inTemplate\widgets\Html::buttonA('查看', ['detail', 'id' => $model->id], ['class' => 'btn btn-info btn-rounded']);
                },
                's-status' => function ($url, $model, $key){
                    if(!in_array($model->status, [Repair::STATUS_HOLD, Repair::STATUS_EVALUATED, Repair::STATUS_CANCEL, 4])){
                        return \components\inTemplate\widgets\Html::button('查询状态', ['class' => 'btn btn-warning btn-rounded s-status', 'data-id' => $model->id, 'data-s' => $model->status]);
                    }
                }
            ]
        ]
    ],
]);

\components\inTemplate\widgets\IBox::end();
?>

<?php \yii\widgets\ActiveForm::begin(['method' => 'POST', 'action' => '/repair/search-status', 'id' => 'form-f']) ?>
    <input type="hidden" name="id" value="" id="idV">
<?php \yii\widgets\ActiveForm::end() ?>

<?php \common\widgets\JavascriptBlock::begin() ?>
<script type="text/javascript">

    $('.s-status').on('click', function (){
        var d = $(this).attr('data-id');
        var s = $(this).attr('data-s');
        var disableS = false;
        var dataV;
        $(this).removeClass('btn-warning').addClass('btn-wait');

        switch(s){
            case '0':
            case '1000':
            case '3000':
                $(this).removeClass('btn-wait').addClass('btn-warning');
                disableS = true;
                break;
        }

        if(disableS){
            alert('此状态不可查询！');
            return;
        }

        $('#idV').val(d);
        dataV = $('#form-f').serialize();
        $.post('/repair/search-status', dataV, function (res){
            if(res.code === 0){
                window.location.reload();
            } else {
                alert(res.msg);
            }
        }, 'json');

    });

</script>
<?php \common\widgets\JavascriptBlock::end() ?>
