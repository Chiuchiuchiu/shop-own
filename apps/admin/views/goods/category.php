<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */
/* @var array $projectRegion */
/* @var int $projectRegionId */
/* @var $parent */

use common\models\Goods;
use common\models\GoodsCategory;
use \components\inTemplate\widgets\Html;

$this->title = '商品分类';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('goods/category')]);
?>
    <div class="form-group">
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('status', $status,\yii\helpers\ArrayHelper::merge(['' => '全部状态'], \common\models\GoodsCategory::statusMap()))?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('shop_id', $shopId, $shopList)?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="分类名" value="<?= $search ?>" class="form-control">
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

    <div class="col-lg-2">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?= \components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'label'=>'上级分类',
                'format'=>'raw',
                'value'=>function(GoodsCategory $model){

                    return $model->parent ? $model->parent->name : "顶级";
                }
            ],
            'keywords',
            [
                'label'=>'所属商铺',
                'format'=>'raw',
                'value'=>function(GoodsCategory $model){
                    return $model->shop->name ?? '-';
                }
            ],
            "level",
            [
                'label'=>'顶部banner',
                'format'=>'raw',
                'value'=>function(GoodsCategory $model){
                    return \components\inTemplate\widgets\Html::img($model->banner_url);
                }
            ],
            [
                'label'=>'左侧菜单icon',
                'format'=>'raw',
                'value'=>function(GoodsCategory $model){
                    return \components\inTemplate\widgets\Html::img($model->img_url);
                }
            ],
            [
                'label'=>'右侧菜单icon',
                'format'=>'raw',
                'value'=>function(GoodsCategory $model){
                    return \components\inTemplate\widgets\Html::img($model->wap_banner_url);
                }
            ],
            'statusText',
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => '{setStatus}',
                'buttons' => [
                    'setStatus' => function ($key,GoodsCategory $model){

                        if($model->status == GoodsCategory::STATUS_ACTIVE){
                            $content = "<span class='btn btn-xs btn-danger'>隐藏</span>";
                            $status = GoodsCategory::STATUS_DELETE;
                            $statusText = "隐藏";
                        }else{
                            $content = "<span class='btn btn-xs btn-primary'>开启</span>";
                            $status = GoodsCategory::STATUS_ACTIVE;
                            $statusText = "开启";
                        }

                        return Html::a(
                            $content,
                            "javascript:;",
                            [
                                'class' => 'setStatus',
                                'data-status' => $status,
                                'data-id' => $model->id,
                                'data-content' => $statusText,
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

        var content = $(this).data('content');

        updateStatus(id, status, content);

    });

    function updateStatus(id, status, content){
        layer.confirm("您确定" + content + "该商品分类？", {
                btn: ['确定', '取消']},
            function(){
                var ii = layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.ajax({
                    type: 'get',
                    url: "/goods/category-set-status?id=" + id + "&status=" + status,
                    timeout: 3000, //超时时间：30秒
                    dataType:'json',
                    success: function (data) {
                        if(data.code == 200){
                            layer.msg("修改成功，请稍等...", {
                                time:500,
                                end:function(){
                                    location.reload();
                                }
                            })
                        }else{
                            layer.close(ii);
                            layer.msg(data.msg);
                        }
                    }
                });
            },
            function(){}
        )
    }
</script>
<?php \common\widgets\JavascriptBlock::end();?>
