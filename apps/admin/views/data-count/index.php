<?php
use components\inTemplate\widgets\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $pic */
/* @var $clickPlace */

$this->title = '数据统计';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['data-count/index'])])

?>
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
            <?php echo \yii\bootstrap\Html::input('input', 'pic', $pic, ['class' => 'form-control', 'placeholder' => '图片路径']) ?>
        </div>
        <?= \yii\bootstrap\Html::input('hidden', 'clickPlace', $clickPlace) ?>
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
    总条数：<?= $dataProvider->totalCount ?>
</div>

<?php
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label'=>'图片',
            'format' => 'raw',
            'value' => function(\common\models\ThirdpartyViewHistory $model){

                $pic = strstr($model->pic, 'http') ? $model->pic : 'http://' . $model->pic;

                return '<img src="'.Yii::getAlias($pic).'">';
            }
        ],
        'picGroupBy',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{show-projects}',
            'buttons' => [
                'show-projects' => function($key, \common\models\ThirdpartyViewHistory $model, $url){
                    return \yii\helpers\Html::a('按项目统计', "/data-count/projects?pic={$model->pic}");
                }
            ]
        ]
    ],
]);
\components\inTemplate\widgets\IBox::end();

?>

<?php \common\widgets\JavascriptBlock::begin()?>
<script>

</script>
<?php \common\widgets\JavascriptBlock::end()?>
