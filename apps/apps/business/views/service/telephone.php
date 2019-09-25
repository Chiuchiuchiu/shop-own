<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */

use common\models\ProjectServicePhone;
use \components\inTemplate\widgets\Html;

$this->title = '便民电话管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/service/telephone')]);
?>
    <div class="form-group">
        <div class="col-sm-2">
            <div class="input-group m-b">
                <?= Html::dropDownList('status', $status,array_merge(['' => '全部'], ProjectServicePhone::statusMap()) )?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => 'id',
                    'value' => $id,
                    'items' => $projectSelect,
                ])?>

            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary">查找</button>
                </span>
            </div>
        </div>
    </div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

    <div class="col-lg-1">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?php
echo \components\inTemplate\widgets\Html::a('新增', ['create', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-success pull-right']);
?>
<?=\components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'telephone',
            'address',
            'statusText',
            [
                'label' => '所属分类',
                'format' => 'raw',
                'value' => function(ProjectServicePhone $model){
                    $name =  isset($model->projectCategory->name) ? $model->projectCategory->name : '未设置';
                    return Html::label($name, '', ['class' => 'text-warning']);
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template'=>'{update?id} {setStatus}',
                'name'=>[
                    'detail' => '查看明细' ,
                ],
                'buttons' => [
                    'setStatus' => function ($key,ProjectServicePhone $model, $index){
                        return Html::a(
                            $model->status == 1 ? "<span class='btn btn-xs btn-primary'>正常</span>" :'<span class="btn btn-xs btn-warning">禁用</span>',
                            "javascript:;",
                            [
                                'class' => 'setStatus',
                                'data-status' => $model->status,
                                'data-id' => $model->id,
                            ]
                        );
                    }
                ],
            ],
        ],
    ])
])?>

<?php \common\widgets\JavascriptBlock::begin();?>
<script>
    $('.setStatus').click(function(){

        var status = $(this).data('status');

        var id = $(this).data('id');

        var name = status == 1 ? "禁用" : "开启";

        layer.confirm("您确定"+ name +"该通讯电话？", {
                btn: ['确定', '取消']},
            function(){
                var ii = layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.ajax({
                    type: 'get',
                    url: "/service/set-status?id=" + id + "&status=" + status + "&type=" + 1,
                    timeout: 3000, //超时时间：30秒
                    dataType:'json',
                    success: function (data) {
                        if(data.code == 0){
                            layer.msg("编辑成功，请稍等...", {
                                time:500,
                                end:function(){
                                    location.reload();
                                }
                            })
                        }else{
                            layer.close(ii);
                            layer.msg(data.message);
                        }
                    }
                });
            },
            function(){}
        )

    })
</script>
<?php \common\widgets\JavascriptBlock::end();?>
