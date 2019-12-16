<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $sign $sign */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */
/* @var array $projectRegion */
/* @var int $projectRegionId */

use common\models\Shop;
use \components\inTemplate\widgets\Html;

$this->title = '商铺管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/shop')]);
?>
    <div class="form-group">
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">
                <?= Html::dropDownList('status', $status,\yii\helpers\ArrayHelper::merge(['' => '全部'], Shop::statusMap()))?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="项目名" value="<?= $search ?>" class="form-control">
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
                'label'=>'logo',
                'format'=>'raw',
                'value'=>function(Shop $model){
                    return \components\inTemplate\widgets\Html::img($model->logo);
                }
            ],
            [
                'label'=>'联系人',
                'format'=>'raw',
                'value'=>function(Shop $model){
                    return $model->shopManager ? $model->shopManager->name : "未设置" ;
                }
            ],
            [
                'label'=>'联系电话',
                'format'=>'raw',
                'value'=>function(Shop $model){
                    return $model->shopManager ? $model->shopManager->mobile : "未设置" ;
                }
            ],
            [
                'label' => '商铺分类',
                'format' => 'raw',
                'value' => function(Shop $model){
                    return isset($model->shopCategory) ? $model->shopCategory->name : '未设置';
                }
            ],
            'statusText',
            [
                'label' => '每笔收取佣金',
                'format' => 'raw',
                'value' => function(Shop $model){
                    return $model->platform_commission . "%";
                }
            ],
            'total_amount',
            'amount_wait',
            'icon_name',
            'service_end_time:date',
            'created_at:date',
            [
                'label' => '关闭时间',
                'format' => 'raw',
                'value' => function(Shop $model){
                    if($model->deleted_at > 0){
                        return Html::label(date('Y-m-d H:i:s', $model->deleted_at), '', ['class' => 'text-warning']);
                    }

                    return '-';
                }
            ],
            [
                'label' => '进入商家后台',
                'format' => 'raw',
                'value' => function(Shop $model){
                    //return Html::a('进入管理后台', '/project/jump-pm?key=' . $model->url_key, ['target' => '_blank']);
                }
            ],
            [
                'label' => '小程序',
                'format' => 'raw',
                'value' => function(Shop $model){
                    //return Html::a('下载二维码', '/project/download-qrcode', ['download']);
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template'=>'{update?id}',
            ],
        ],
    ])
])?>