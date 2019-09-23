<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/27
 * Time: 10:51
 * @var \yii\web\View $this
 */

/* @var $dataProvider \yii\data\ActiveDataProvider*/
/** @var $house_id integer */
/** @var $projects array */
/** @var $type int */
/* @var array|string $dateTime */
/* @var string $memberName */
/* @var string $email */
/* @var integer $paperInvoiceCount */

$this->title = '项目';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['fpzz/project'])])
?>

    <div class="form-group">

        <div class="col-sm-3">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-12 sl ctr-template">

                    <?php
                    echo \components\inTemplate\widgets\Chosen::widget([
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
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<div class="col-lg-12">
    <?= \components\za\Html::a('新增', ['create', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-success']);?>
</div>

    <div>总记录数：<?= $dataProvider->totalCount ?></div>

<?php
\components\inTemplate\widgets\IBox::begin();

echo \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'table table-hover table-striped table-bordered'
    ],
    'columns' => [
        'id',
        [
            'label' => '项目',
            'format' => 'raw',
            'value' => function (\apps\pm\models\ProjectFpzzAccount $model){
                  if($model != null && $model->project != null){
                      $_house_name = $model->project->house_name;
                  }else{
                      $_house_name = "--";
                  }
                 return \yii\helpers\Html::label($_house_name);
            }
        ],
        'tips',
        'statusText',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{show-pdf}',
            'buttons' => [
                'show-pdf' => function($key, \apps\pm\models\ProjectFpzzAccount $model, $url){
                    return \yii\helpers\Html::button('编辑', ['class' => 'layui-btn lay-btn', 'data-id' => $model->id]);
                }
            ]
        ]
    ],
]);

\components\inTemplate\widgets\IBox::end();

?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $('.lay-btn').on('click', function(){
        var dataId = $(this).attr('data-id');
        if(dataId < 1){
            return false;
        }

        layer.open({
            type: 2,
            title: '编辑',
            maxmin: true,
            shadeClose: true,
            area: ['800px', '520px'],
            content: 'edit-project?dataId='+dataId,
            btn: ['关闭'],
            yes: function (){
                location.reload()
            }
        })
    });

    $('.remarks').on('click', function (){
        $('#member-name').val($(this).attr('u'));
        $('#fpdm').val($(this).attr('data-fd'));
        $('#fphm').val($(this).attr('data-fh'));
        $('#fp-id').val($(this).attr('data-id'));
    });

    $('#save').click(function (){
        var data = $('#remarks-form').serialize();
        var remarks = $('#remarks-c').val();

        if(remarks.length < 1){
            alert('请填写备注信息！');
            return false;
        }

        if(confirm('是否确认提交？')){
            $.ajax({
                type: "POST",
                url: '/fpzz/cancel',
                async: false,
                data: data,
                success: function (res){
                    if(res.code == 0){
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                },
                error: function (error){
                    alert('错误！');
                },
                dataType: 'json'
            });
        }

    });

    $('.cause').on('click', function(){
        var pId = $(this).attr('data-id');

        $.getJSON('/fpzz/cause', {pmOrderId:pId}, function(res){

            layer.open({
                type: 1
                ,title: false //不显示标题栏
                ,closeBtn: false
                ,area: '300px;'
                ,shade: 0.8
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['关闭']
                ,btnAlign: 'c'
                ,moveType: 1 //拖拽模式，0或者1
                ,content: '<div style="padding: 50px; line-height: 22px; background-color: ; font-weight: bold;text-align:center;">'+res.data.message+'</div>'
                ,success: function(layero){

                }
            });
        });

    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
