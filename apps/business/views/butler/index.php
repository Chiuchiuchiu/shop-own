<?php
use components\inTemplate\widgets\GridView;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var integer $projects */
/* @var integer $house_id */
/* @var string $butler_name */
/* @var int $group */
/* @var int $status */
/* @var array $butlerGroupList */

$this->title = '管家';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['butler/index'])])

?>

    <div class="form-group">

        <div class="col-sm-7">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-4 sl ctr-template">

                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projects,
                    ])?>

                </div>

            </div>
        </div>
    </div>

    <div class="form-group">

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= \components\inTemplate\widgets\Html::dropDownList('group', $group, ['' => '全部(分组)', '管家' => \common\models\Butler::groupMap()]) ?>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?= \components\inTemplate\widgets\Html::dropDownList('status', $status, ['' => '全部（状态）', '状态' => \common\models\Butler::statusMap()]) ?>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?php echo \yii\bootstrap\Html::input('input', 'bName', $butler_name, ['class' => 'krajee-datepicker form-control', 'placeholder' => '昵称']) ?>
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

<?php \components\inTemplate\widgets\IBox::begin(['title' => '更换分组为', 'iboxContentStyle' => 'padding: 0px 20px;']); ?>

<p>
    <?php foreach($butlerGroupList as $key => $v){?>
        <button class="btn btn-success update-group" data-value="<?= $key ?>" type="button"><?= $v ?></button>
    <?php }?>

    <?php
    echo Html::a(
        '导出当前数据报表',
        ['export', 'group'=>$group, 'house_id' => $house_id, 'status' => $status, 'bName' => $butler_name],

        ['class'=>'btn btn-info pull-right']
    );
    ?>
    <input type="hidden" id="sum-status" value="0">
</p>

<?php \components\inTemplate\widgets\IBox::end(); ?>

<div class="col-lg-12">
    <div>
        <button class="btn btn-primary icheckBoxAll" type="button"><i class="fa fa-check"></i>全选</button>
        总条数：<?= $dataProvider->totalCount ?>
    </div>
</div>

<?php
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => '选择',
            'format' => 'raw',
            'value' => function(\common\models\Butler $model){
                return \components\inTemplate\widgets\ICheck::widget([
                    'value' => $model->id . '-' . $model->group
                ]);
            }
        ],
        'id',
        'groupText',
        'projectName',
        'nickname',
        [
            'label' => '手机号',
            'format' => 'raw',
            'value' => function(\common\models\Butler $model){
                $ph = '-';
                if(isset($model->butlerAuth)){
                    $ph = $model->butlerAuth->account;
                }

                return $ph;
            }
        ],
        [
            'label' => '管理区域',
            'format' => 'raw',
            'value' => function(\common\models\Butler $model){
                $houseName = '';
                if(!empty($model->regions)){
                    foreach($model->regionData as $row){
                        /**
                         * @var \common\models\House $row
                         */
                        $houseName .= '，' . $row->house_name;
                    }

                    $houseName = ltrim($houseName, '，');
                }

                return $houseName;
            }
        ],
        'mana_number',
        'statusText',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {delete}'
        ],
    ],
]);
\components\inTemplate\widgets\IBox::end();

?>

<div class="modal-backdrop fade in" style="display: none;"></div>

<?php \common\widgets\JavascriptBlock::begin()?>

<script>
    $('.closeModal').click(function (){
        $('.modal-footer').hide();
        $('#modal-form').hide();
    });

    $('.icheckBoxAll').on('click', function (){

        $("input[type='checkbox']").each(function (key, index){
            if(!$(this).parent().hasClass('checked')){
                $(this).prop('checked', true);
                $(this).parent().addClass('checked');
            }
        });

    });

    $('.update-group').on('click', function (){
        var groupText = $(this).html();
        var st = $('#sum-status').val();
        var dv = $(this).attr('data-value');
        var v;
        var data = [];
        var ajaxGet = function (ids, group){
            $.ajax({
                type: 'GET',
                url: '/butler/update-group',
                data: {butlerIds:ids, group:group},
                beforeSend: function (){
                    $('#sum-status').val('1');
                    $('.modal-backdrop').show();
                },
                success: function (res){
                    $('#sum-status').val('0');
                    if(res.code == '0'){
                        location.reload();
                    } else {
                        $('.modal-backdrop').hide();
                        alert(res.message);
                    }
                },
                error: function (){
                    $('.modal-backdrop').hide();
                    alert('服务繁忙');
                },
                dataType: 'json'
            });
        };
        if(st < 1){
            $("input:checked").each(function(){
                v = $(this).val().split('-');
                if(dv != v[1]){
                    data.push(v[0]+'-'+dv);
                } else {
                    $(this).prop('checked', false);
                    $(this).parent().removeClass('checked');
                }
            });

            if(data.length > 0){
                data = data.join(',');

                layer.open({
                    type: 1,
                    title: false, //不显示标题栏
                    closeBtn: false,
                    area: '300px;',
                    shade: 0.8,
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    btn: ['提交', '关闭'],
                    btnAlign: 'c',
                    moveType: 1, //拖拽模式，0或者1
                    content: '<div style="padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;">将更新其分组为：'+ groupText +'</div>',
                    success: function(layero){
                        let btn = layero.find('.layui-layer-btn');
                        let closeA = btn.find('.layui-layer-btn1');
                        let buttonB = btn.find('.layui-layer-btn0');
                        $(closeA).on('click', function (){
                            layer.closeAll();
                        });
                        $(buttonB).on('click', function(){
                            // btn.hide();
                            ajaxGet(data, dv);
                        })
                    }
                });
            }

            return;
        }

        alert("正在处理中……");
    });

</script>

<?php \common\widgets\JavascriptBlock::end()?>
