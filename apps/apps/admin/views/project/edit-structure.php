<?php
/**
 * @var $projectModel  \common\models\Project
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $this yii\web\View
 */
use components\inTemplate\widgets\Html;
use \yii\bootstrap\Modal;

$this->title = '编辑 ' . $projectModel->house_name;
$this->params['breadcrumbs'][] = ['label' => '项目', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget([
    'url' => ['index'],
    'name' => '返回项目',
    'option'=>[
        'class'=>'btn btn-w-m btn-white pull-left'
    ]
]);

echo \components\inTemplate\widgets\IBox::widget([
    'content' => \components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'project_house_id',
            'name',
            'parent_reskind',
            'typeText',
            'reskind',
            'groupText',
            'ordering',
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => "{update-structure}",
            ]
        ],
    ])
]);

?>

<?php
Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">预览</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
?>

<div class="form-group">
    <select data-required="1" id="type" data-i="0">
        <option value="1" selected>住宅</option>
        <option value="2">车位</option>
    </select>
    <label>类型</label>
</div>

<div class="form-group">
    <select id="project" name="" data-required="">
        <option value="">请选择</option>
        <option value="<?= $projectModel->house_id ?>"><?= $projectModel->house_name ?></option>
    </select>

    <label>小区</label>

    <div id="houseChoose">

    </div>
</div>

<?php
Modal::end();
?>

<?php
Modal::begin([
    'id' => 'alert-Modal',
    'header' => '<h4 class="modal-title" id="myModalLabel">提示</h4>',
    'footer' => '<button class="btn btn-primary" data-dismiss="modal">关闭</button>',
]);
?>
<div id="alert-content">

</div>

<?php
Modal::end();
?>

<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::button('预览效果', ['class' => 'btn btn-info', 'id' => 'preview', 'data-toggle' => "modal", 'data-target' => "#create-modal"]) ?>
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>
    <script type="text/javascript">

        $('#project,#type').on('change', function () {
            var type = $('#type').val();
            var project = $('#project').val();

            $.getJSON('house-query', {houseId: project,group:type}, function (res) {
                if(res.code==0){
                    $('#houseChoose').html('');
                    $.each(res.data.structure, function (i, row) {
                        $('#houseChoose').append(
                            $('<div/>')
                                .append(
                                    $('<select/>').attr('data-required',1).attr('data-i',i)
                                )
                                .append(
                                    $('<label/>').html(row)
                                ).addClass('form-group')
                        );
                    });
                    $('#houseChoose select:last').attr('data-last',1);
                    $('#houseChoose select[data-i=0]').append($('<option/>').attr('value', '').html('请选择'));
                    $.each(res.data.list, function (houseId, row) {
                        $('#houseChoose select[data-i=0]').append($('<option/>').attr('value', row.house_id).html(row.house_name));
                    });
                }
            });

            $('#houseChoose').html('');

        }).trigger('change');

        $('#houseChoose').on('change','select', function () {
            var i = $(this).attr('data-i');
            i++;
            $('#houseChoose select[data-i]').each(function(){
                if($(this).attr('data-i')>=i){
                    $(this).parent().show();
                    $(this).attr('data-required',1);
                    $('option',this).remove();
                }
            });
            var target = $('#houseChoose select[data-i='+i+']');
            var houseId=$(this).val();
            var type = $('#type').val();

            $.getJSON('house-query', {houseId: houseId,group: type}, function (res) {
                target.append($('<option/>').attr('value', '').html('请选择'));
                $.each(res.data.list, function (houseId, row) {
                    target.append($('<option/>').attr('value', row.house_id).html(row.house_name));
                });
                if(res.data.list.length==0){
                    $('input[name=houseId]').val(houseId);
                    $('#houseChoose select[data-i]').each(function(){
                        if($(this).attr('data-i')>=i){
                            $(this).parent().hide();
                            $(this).removeAttr('data-required');
                        }
                    });
                }
            });
        });

    </script>

<?php \common\widgets\JavascriptBlock::end(); ?>