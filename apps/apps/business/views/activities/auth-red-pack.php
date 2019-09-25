<?php
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
/* @var $phone */
/* @var $even_name */
/* @var array $RedPackNumber */

$this->title = '房产认证红包领取';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title' => '搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['activities/auth-red-pack'])])

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

        <div class="col-sm-2">
            <label></label>
            <?= \yii\helpers\Html::dropDownList('even_name', $even_name, ['201712' => '18年', '201812' => '19年'], ['class' => 'input-group form-control'])?>
        </div>

        <div class="col-sm-2">
            <label></label>
            <?= \yii\helpers\Html::input('text', 'phone', $phone, ['class' => 'input-group form-control', 'placeholder' => '手机号'])?>
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

    <div class="col-lg-12">
        <div>
            总条数：<?= $dataProvider->totalCount ?>；
            <label for="" class="text-warning">总额：<?= $redPackAmount ?></label>

            <?php if(!empty($RedPackNumber)){?>
                【
                <?php foreach($RedPackNumber as $key => $row){?>

                    <label for="">金额</label><?= $row['amount'] ?>
                    -》已领：<?= $row['count'] ?>；
                <?php }?>
                】
            <?php }?>


            <?php
            echo \yii\helpers\Html::a(
                '导出当前数据报表（分批导，3个月）',
                ['christmas-red-export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'house_id' => $house_id, 'discountStatus' => ''],
                ['class'=>'btn btn-info pull-right']
            );

            ?>

        </div>
    </div>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'number',
        [
            'label' => '用户',
            'format' => 'raw',
            'value' => function(\common\models\WechatRedPack $model){
                return $model->member->showName;
            }
        ],
        [
            'label' => '手机号',
            'format' => 'raw',
            'value' => function(\common\models\WechatRedPack $model){
                return $model->member->phone;
            }
        ],
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\WechatRedPack $model){
                return $model->house->ancestor_name;
            }
        ],
        [
            'label' => '金额',
            'format' => 'raw',
            'value' => function(\common\models\WechatRedPack $model){
                return \yii\helpers\Html::label($model->amount, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '活动',
            'format' => 'raw',
            'value' => function(\common\models\WechatRedPack $model){
                return $model->remark;
            }
        ],
        'created_at:datetime',
    ],
])
]); ?>