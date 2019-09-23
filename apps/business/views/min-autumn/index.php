<?php

use yii\helpers\Html;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/16
 * Time: 15:20
 */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var integer $house_id */
/* @var array $projectsArray */
/* @var \common\valueObject\RangDateTime $dateTime */
/* @var $redPackAmount */

$this->title = '业主答题列表';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title' => '搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['min-autumn/index'])])

?>

    <div class="form-group" >
        <div class="col-sm-3">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-12 sl ctr-template">
                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projectsArray,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">

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

        <div class="col-sm-1">
            <label for=""></label>
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>

    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

    <div class="col-lg-11">
        <div>
            总条数：<?= $dataProvider->totalCount ?>条；
            <label for="" class="text-warning">总额：<?= $redPackAmount ?>元</label>
        </div>
    </div>

    <div class="col-lg-1">
        <div>
            <?=
                Html::a(
                    '导出当前数据报表',
                    ['export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'house_id' => $house_id],
                    ['class'=>'btn btn-info pull-right']
                );
            ?>
        </div>
    </div>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '用户',
            'format' => 'raw',
            'value' => function(\common\models\MinAutumnRedPack $model){
                return $model->member->showName;
            }
        ],
        [
            'label' => '手机号',
            'format' => 'raw',
            'value' => function(\common\models\MinAutumnRedPack $model){
                return $model->member->phone;
            }
        ],
        [
            'label' => '住址',
            'format' => 'raw',
            'value' => function(\common\models\MinAutumnRedPack $model){
                return $model->house->ancestor_name;
            }
        ],
        [
            'label' => '金额',
            'format' => 'raw',
            'value' => function(\common\models\MinAutumnRedPack $model){
                return \yii\helpers\Html::label($model->amount / 100, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '微信红包订单号',
            'format' => 'raw',
            'value' => function(\common\models\MinAutumnRedPack $model){
                return $model->wechat_mch_id ?? "没有订单号";
            }
        ],
        [
            'label' => '状态',
            'format' => 'raw',
            'value' => function(\common\models\MinAutumnRedPack $model){
                return $model::statusType()[$model->status];
            }
        ],
        'created_at:datetime',
    ],
])
]); ?>

<?php \common\widgets\JavascriptBlock::begin() ?>
    <script>
        $('.export').click(function(){
            alert(123);
        })
    </script>
<?php \common\widgets\JavascriptBlock::end() ?>
