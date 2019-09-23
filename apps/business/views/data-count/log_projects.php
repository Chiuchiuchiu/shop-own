<?php
use components\inTemplate\widgets\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $date */
/* @var $dateTime */

$this->title = '按项目统计' . $date;
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['data-count/log-projects?date=' . $date])])
?>

<div class="form-group">

    <div class="col-sm-5">
        <label>&nbsp;</label>
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
    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?= \components\za\Html::a('导出当月所有数据',['export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'date' => $date],['class'=>'btn btn-success btn-block']); ?>
    </div>
</div>

<div class="form-group">

</div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();

echo \components\inTemplate\widgets\BackBtn::widget(['url'=>['member']]);
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label'=>'项目名称',
            'format' => 'raw',
            'value' => function(\common\models\OperationLog $model){
                return $model->project->house_name ?? '游客（Guest）';
            }
        ],
        'totalCount',
        'memberCount',
    ],
]);
\components\inTemplate\widgets\IBox::end();

?>

<?php \common\widgets\JavascriptBlock::begin()?>
<script>

</script>
<?php \common\widgets\JavascriptBlock::end()?>
