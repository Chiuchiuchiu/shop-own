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

$this->title = '小狗电器访问记录';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title' => '搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['activities/xg-request-log'])])

?>

    <div class="form-group">
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

    <div class="col-lg-12">
        <?php
        echo \yii\helpers\Html::a(
            '导出当前数据报表',
            ['xg-request-export', 'house_id' => $house_id, 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate()],
            ['class'=>'btn btn-info pull-right']
        );
        ?>
    </div>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'member_id',
        'title',
        'type_code',
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\XgRequestLog $model){
                return !empty($model->project->house_name) ? $model->project->house_name : '-';
            }
        ],
        'typeText',
        'created_at:datetime',
    ],
])
]); ?>