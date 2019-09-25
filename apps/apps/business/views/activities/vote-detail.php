<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 12:01
 */
/* @var integer $house_id */
/* @var array $projectsArray */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var int $group */
/* @var int $status */
/* @var int $page */
/* @var array $timeArr */

$this->title = '参与活动竞投人员列表';
$this->params['breadcrumbs'][] = $this->title;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['vote-lists']]);

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['activities/vote-detail'])])

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
                <input type="hidden" name="group" value="<?= $group ?>">
            </div>
        </div>
    </div>

    <div class="form-group">

        <div class="col-sm-4">
            <label>得票时间</label>
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
            <?= \yii\helpers\Html::dropDownList('status', $status, \common\models\ButlerElectionActivity::statusMapLists(), ['class' => 'input-group form-control'])?>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= \yii\helpers\Html::input('text', 'name', $name, ['class' => 'input-group form-control', 'placeholder' => '管家姓名'])?>
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
    echo \yii\helpers\Html::a(
        '导出当前数据报表',
        ['vote-export', 'house_id' => $house_id, 'status' => $status, 'group' => $group, 'timeArr' => $timeArr],
        ['class'=>'btn btn-info pull-right']
    );
    ?>
</div>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => '选择',
            'format' => 'raw',
            'value' => function(\common\models\ButlerElectionActivity $model){
                return \components\inTemplate\widgets\Html::checkbox('checkbox-m', false, ['data-id' => $model->id, 'value' => '']);
            }
        ],
        'id',
        'name',
        'phone',
        'statusText',
        [
            'label'=>'头像',
            'format'=>'raw',
            'value'=>function(\common\models\ButlerElectionActivity $model){
                return \components\inTemplate\widgets\Html::img($model->head_img);
            }
        ],
        [
            'label'=>'项目',
            'format'=>'raw',
            'value'=>function(\common\models\ButlerElectionActivity $model){
                return \components\za\Html::label($model->project->house_name, '', ['class' => 'text-info']);
            }
        ],
        'groupText',
        [
            'label'=>'投票数',
            'format'=>'raw',
            'value'=>function(\common\models\ButlerElectionActivity $model) use ($timeArr){
                return \components\za\Html::label($model->getVoteCount($timeArr), '', ['class' => 'text-info']);
            }
        ],
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{account-update}',
            'buttons'=>[
                'account-update'=>function($url,\common\models\ButlerElectionActivity $model, $key){
                    return \yii\helpers\Html::a('编辑', ['vote-account-update', 'id'=>$model->id, 'group' => $model->group], ['class' => 'btn btn-xs btn-success']);
                }
            ],
        ],
    ],
])
]); ?>

<div class="row">
    <div class="form-group">
        <div class="text-center">
            <button class="checkbox-all">全选</button>
            <button class="checkbox-not-checked">全不选</button>
<!--            <button class="btn btn-success btn-submit-e">提交修改</button>-->
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $(function (){

        $('.checkbox-all').on('click', checkboxAll);
        $('.checkbox-not-checked').on('click', checkboxNotChecked);
        $('.btn-submit-e').on('click', putIn);

        function checkboxAll(){
           var _checkbox = $("input[name='checkbox-m']");
           $(_checkbox).each(function (index, elem) {
               if(!$(elem).prop('checked')){
                   $(elem).prop('checked', true);
               }
           })
        }

        function checkboxNotChecked(){
            var _checkbox = $("input[name='checkbox-m']");
            $(_checkbox).each(function (index, elem) {
                if($(elem).prop('checked')){
                    $(elem).prop('checked', false)
                }
            })
        }

        function putIn() {
            var _array = new Array();
            var _checkbox = $("input[name='checkbox-m']");
            $(_checkbox).each(function (index, elem) {
                if($(elem).prop('checked')){
                    _array.push($(elem).attr('data-id'))
                }
            });
        }

    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
