<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $shopId 商铺id */
/* @var $shopList 商铺列表 */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */
/* @var array $projectRegion */
/* @var int $projectRegionId */

use common\models\Goods;
use \components\inTemplate\widgets\Html;

$this->title = '商品管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/goods')]);
?>
    <div class="form-group">
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('status', $status,\yii\helpers\ArrayHelper::merge(['' => '全部状态'], Goods::statusMap()))?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('shop_id', $shopId, $shopList)?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="商品名" value="<?= $search ?>" class="form-control">
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

<?= \components\inTemplate\widgets\Html::a('新增', ['create', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-primary pull-right']); ?>
<?= \components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'label'=>'所属商铺',
                'format'=>'raw',
                'value'=>function(Goods $model){
                    return $model->shop->name;
                }
            ],
            [
                'label'=>'所属分类',
                'format'=>'raw',
                'value'=>function(Goods $model){
                    return $model->goodsCategory->name;
                }
            ],
            [
                'label'=>'主图',
                'format'=>'raw',
                'value'=>function(Goods $model){
                    return \components\inTemplate\widgets\Html::img($model->primary_pic_url);
                }
            ],
            'statusText',
            'goods_unit',
            'unit_price',
            'counter_price',
            'sell_volume',
            [
                'label' => '商品佣金',
                'format' => 'raw',
                'value' => function(Goods $model){
                    return $model->platform_commission > 0 ? $model->platform_commission . "%" : $model->shop->platform_commission . "%（店铺默认佣金）";
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => '{setStatus}',
                'buttons' => [
                    'setStatus' => function ($key,Goods $model, $index){
                        return Html::a(
                            $model->status == 1 ? "<span class='btn btn-xs btn-primary'>下架</span>" :'-',
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

        var name = status == 1 ? "关闭" : "开启"

        layer.confirm("您确定"+ name +"该调研计划？", {
                btn: ['确定', '取消']},
            function(){
                var ii = layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
                $.ajax({
                    type: 'get',
                    url: "/question-project/set-status?id=" + id + "&status=" + status,
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