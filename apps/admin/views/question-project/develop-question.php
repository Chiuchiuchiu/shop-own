<?php

use \apps\admin\models\QuestionProject;
use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime string */
/* @var $year string */
/* @var $season string */

$this->title = '调查问卷';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('develop-question')]); ?>
<div class="form-group">
    <label class="control-label col-sm-1">搜索</label>
    <div class="col-sm-2">
        <?= \kartik\date\DatePicker::widget([
            'model' => $dateTime,
            'attribute' => 'startDate',
            'options' => ['placeholder' => '年份', 'value' => $year, 'name' => 'year'],
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
        <?php echo \components\inTemplate\widgets\Chosen::widget([
            'name' => 'season',
            'value' => $season,
            'items' => [1=>'一季度',2=>'二季度',3=>'三季度',4=>'四季度'],
        ])?>
    </div>
    <div class="col-sm-2">

        <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
    </div>
</div>
<input type="hidden" class="year" value="<?= $year ?>">
<input type="hidden" class="season" value="<?= $season ?>">
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
<div class="form-group">
    <?php \components\ajaxUpload\widgets\AjaxUpload::widget([]); ?>
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>模板批量导入</h5>
            </div>
            <div class="ibox-content">
                <form role="form" class="form-inline" id="uploadFile">
                    <div class="form-group"><label for="exampleInputEmail2" class="sr-only">Email address</label>
                        <input name="UploadObject[file]" required id="excelFile" class="form-control" type="file" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>
                    <button id="submitFile" class="btn btn-white" type="submit">文件上传</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-1">
        <?= Html::a('导出已答题', ["export-develop-answer?year={$year}&season={$season}"], ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-sm-1">
        <?= Html::a('一键发送短信', 'javascript:void(0);', ['class' => 'btn btn-warning sendMsg']) ?>
    </div>
</div>
<div id="modal-form" style="display: none;" aria-hidden="true" class="modal fade in">
    <div class="modal-backdrop fade in"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text-center send-tips">
                            正在提交
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
<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'company',
        'project',
        'typeText',
        'name',
        'phone',
        'number',
        'year',
        'season',
        'created_at:datetime',
        [
            'label' => '是否答题',
            'format' => 'raw',
            'value' => function(\common\models\QuestionMemberDevelop $model){

                return $model->getCount() > 0 ? Html::a('答题详细', 'javascript:void(0);', ['class' => 'btn btn-xs btn-success','onclick'=>'devAnswerList(\''.$model->id.'\');']) : Html::label("未答题", '', ['class' => 'text-danger']);
            }
        ],
    ],
])
]); ?>
<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $('.sendMsg').click(function(){
        var year = $('.year').val();
        var season = $('.season').val();

        if(confirm("是否确定发送短信？")){
            layer.load(1);

            $.ajax({
                type: "POST",
                url: "send-dev-msg",
                data: {year:year, season:season},
                dataType: "json",
                success: function(res) {
                    //关闭loading
                    layer.closeAll('loading');

                    layer.msg(res.msg, {time: 2000}, function(){
                        if(res.code == 0){
                            location.reload();
                        }
                    });
                }
            })
        }
    });

    $('.closeModal').click(function (){
        $('.modal-footer').hide();
        $('#modal-form').hide();
    });

    $('#uploadFile').on('submit', function (){

        $('#excelFile').ajaxfileupload({
            action: '/default/upload',
            validate_extensions: false,
            params: {
                extra: 'info'
            },
            onComplete: function(response) {

                importExcel(response.data.savePath);
                //eval('response = '+response+';');
            },
            onStart: function() {
                $('.send-tips').html('正在上传…………');
                $('#modal-form').show();
            },
            onCancel: function() {
                console.log('no file selected');
            }
        }).change();

        return false;
    });

    var importExcel = function (filePath){

        $('#excelFile').val('');
        $.ajax({
            type: 'GET',
            url: '/question-project/import-dev-excel',
            data: {filePath: filePath},
            success: function (res){
                if(res.code === 0){
                    $('.send-tips').html('上传成功…………');
                    $('#modal-form').hide();
                    window.location.reload();
                } else {
                    $('.send-tips').html(res.message);
                    $('.modal-footer').show();
                }
            },
            error: function (res){
                $('.send-tips').html('系统错误…………');
            },
            dataType: 'JSON'
        });
    };
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
