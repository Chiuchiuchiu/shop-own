<?php
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $type int */
/* @var $rs array */
/* @var $result array */
/* @var $projectArray array */
/* @var $search int */

$this->title = '各项目管家分属区域数据';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['/wechats-mp-manage']]);

\components\inTemplate\widgets\IBox::begin(['title' => '搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['export'])])
?>

<div class="form-group">

    <div class="col-sm-5">
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
        <?php echo \yii\bootstrap\Html::submitButton('导出', ['class' => 'btn btn-info btn-block']) ?>
    </div>

</div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>


