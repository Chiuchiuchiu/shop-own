<?php
use components\inTemplate\widgets\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $pic */

$this->title = '登录人数';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['data-count/member'])])

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
                    ])?>

                </div>

            </div>
        </div>

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
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label'=>'月份',
            'format' => 'raw',
            'value' => function(\common\models\OperationLog $model){
                return date('Y-m', $model->created_at);
            }
        ],
        'totalCount',
        'memberCount',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{show-projects}',
            'buttons' => [
                'show-projects' => function($key, \common\models\OperationLog $model, $url){
                    return \yii\helpers\Html::a('按项目统计', "/data-count/log-projects?date=" . date('Ym', $model->created_at));
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
