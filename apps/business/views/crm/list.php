<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var integer $quarter */
/* @var integer $years */
/* @var integer $projectId */
/* @var integer $projectRegionId */
/* @var array projectRegionList */

$this->title = '走访记录';
$this->params['breadcrumbs'][] = $this->title;
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
?>

    <p><strong style="color: red;font-size: 1.2em;">注：走访报表数据与管家指标数据挂钩。如果管家有走访记录，但报表没有数据，请查看是否有录入 『指标』数据，请操作“<a href="/crm">指标管理</a>”录入数据。</strong></p>

<?php
$from = \components\inTemplate\widgets\ActiveForm::begin([
    'method' => 'get',
    'action' => \yii\helpers\Url::to(['crm/list']),
])
?>
    <div class="form-group">
        <div class="col-sm-2">
            <label>年份</label>
            <?= \kartik\date\DatePicker::widget([
                'model' => $dateTime,
                'attribute' => 'startDate',
                'options' => ['placeholder' => '年份', 'value' => $years, 'name' => 'years'],
                'type' => \kartik\date\DatePicker::TYPE_INPUT,
                'language' => 'zh-CN',
                'value' => date('Y', time()),
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy',
                    'startView' => 2,
                    'minViewMode' => 2,
                    'maxViewMode' => 2,
                ]
            ]) ?>
        </div>

        <div class="col-sm-2">
            <label>分公司</label>
            <?php echo \components\inTemplate\widgets\Chosen::widget([
                'name' => 'projectRegionId',
                'items' => $projectRegionList,
            ])?>
        </div>

        <div class="col-sm-3">
            <label>项目</label>
            <?php echo \components\inTemplate\widgets\Chosen::widget([
                'name' => 'projectId',
                'addClass' => 'selectedProject',
                'items' => $projectList,
            ])?>
        </div>

        <div class="col-sm-2">
            <label>管家</label>
            <?php echo \components\inTemplate\widgets\Chosen::widget([
                'name' => 'butlerId',
                'addClass' => 'selectedButler',
                'items' => [],
            ])?>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= Html::dropDownList('quarter', $quarter, ['当前季度', '第一季度', '第二季度', '第三季度', '第四季度'], ['class' => 'input-group form-control'])?>
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

    <div class="ibox float-e-margins " style="">
        <div class="ibox-content">
            <div class="form-group">
                <div class="col-sm-1">
                    <a class="btn btn-info" href="export-report?years=<?= $years ?>&quarter=<?= $quarter ?>&projectId=<?= $projectId ?>&projectRegionId=<?= $projectRegionId ?>">
                        导出报表
                    </a>
                </div>
                <div class="col-sm-1">
                    <a class="btn btn-info" href="export-subsidiary?years=<?= $years ?>&quarter=<?= $quarter ?>&projectId=<?= $projectId ?>&projectRegionId=<?= $projectRegionId ?>">
                        导出当前明细
                    </a>
                </div>
                <div class="col-sm-1">
                    <a class="btn btn-primary" href="export-no-visit-to-project?years=<?= $years ?>&quarter=<?= $quarter ?>">
                        导出未走访的项目
                    </a>
                </div>
                <div class="col-sm-1">
                    <a class="btn btn-primary" href="export-butler-not-visit?years=<?= $years ?>&quarter=<?= $quarter ?>&projectId=<?= $projectId ?>">
                        导出未走访管家
                    </a>
                </div>
            </div>

        </div>
    </div>

<div class="col-lg-12">
    <div>总条数：<?= $dataProvider->totalCount ?></div>
</div>

<?php
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '项目分公司',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->projectRegionName, '');
            }
        ],
        [
            'label' => '业主昵称/姓名',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->member->showName, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '业主手机号',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->phone, '', ['class' => 'text-success']);
            }
        ],
        [
            'label' => '管家',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->butler->nickname, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '管家所属项目',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->butler->projectName, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->house->project->house_name, '');
            }
        ],
        [
            'label' => '房产',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->house->showName, '', ['class' => 'text-info']);
            }
        ],
        [
            'label' => '季度',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->quarterText, '', ['class' => 'text-success']);
            }
        ],
        [
            'label' => '综合满意度',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->satisfaction . ' 星', '', ['class' => $model->satisfaction > 3 ? 'text-warning' : 'text-danger']);
            }
        ],
        [
            'label' => '公共设施',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->pu_satisfaction . ' 星', '', ['class' => $model->pu_satisfaction > 3 ? 'text-warning' : 'text-danger']);
            }
        ],
        [
            'label' => '报事报修',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->ra_satisfaction . ' 星', '', ['class' => $model->ra_satisfaction > 3 ? 'text-warning' : 'text-danger']);
            }
        ],
        [
            'label' => '安全管理',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->sm_satisfaction . ' 星', '', ['class' => $model->sm_satisfaction>3 ? 'text-warning' : 'text-danger']);
            }
        ],
        [
            'label' => '清洁绿化',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->cg_satisfaction . ' 星', '', ['class' => $model->cg_satisfaction > 3 ? 'text-warning' : 'text-danger']);
            }
        ],
        [
            'label' => '管家服务',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                return Html::label($model->bs_satisfaction . ' 星', '', ['class' => $model->bs_satisfaction > 3 ? 'text-warning' : 'text-danger']);
            }
        ],
        [
            'label' => '业主意见',
            'format' => 'raw',
            'value' => function(\common\models\VisitHouseOwner $model){
                $html = "<details>{$model->content}</details>";

                return Html::label($html, '');
            }
        ],
        'created_at:datetime',
    ],
]);
\components\inTemplate\widgets\IBox::end();

?>

    <div id="modal-form" style="display: none;" aria-hidden="true" class="modal fade in">
        <div class="modal-backdrop fade in"></div>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="text-center send-tips">
                                正在获取管家列表…………
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display: none">
                    <button type="button" class="btn btn-white closeModal" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

    <script type="text/javascript">
        $('.selectedProject').on('change', function () {
            var projectWords = $(this, "option:selected").val();

            if(projectWords.length < 1){
                return false;
            }

            $.ajax({
                type: 'GET',
                url: 'get-project-butler-list',
                data: {projectHouseId:projectWords},
                beforeSend: function (){
                    $('.send-tips').html('正在获取管家列表…………');
                    $('#modal-form').show();
                },
                success: function (data) {
                    var html = "<option value=''>选择管家</option>";

                    if(data.data.length > 0){
                        $.each(data.data, function (key, value){
                            html += '<option value="'+value.id+'">'+value.nickname+'</option>';
                        });
                    }

                    $(".selectedButler").empty();
                    $(".selectedButler").html(html);
                    $(".selectedButler").trigger('chosen:updated');
                    $('#modal-form').hide();
                },
                dataType: 'json'
            });
        });
    </script>

<?php \common\widgets\JavascriptBlock::end(); ?>