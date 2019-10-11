<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $sign $sign */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */
/* @var array $projectRegion */
/* @var int $projectRegionId */

use common\models\ShopCategory;
use \components\inTemplate\widgets\Html;

$this->title = '商铺分类';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/shop/category')]);
?>
    <div class="form-group">
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

    <div class="col-lg-1">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?= \components\inTemplate\widgets\Html::a('新增', ['category-delete', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-primary pull-right']); ?>
<?= \components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'statusText',
            'created_at:datetime',
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template'=>'{category-update} {category-delete}',
                'buttons' => [
                    'category-update' => function($key, ShopCategory $model, $url){
                        return \yii\helpers\Html::button('编辑分类', ['class' => 'btn btn-success', 'data-id' => $model->member_id]);
                    },
                    'category-delete' => function($key, ShopCategory $model, $url){
                        return \yii\helpers\Html::button('删除分类', ['class' => 'btn btn-danger', 'data-id' => $model->member_id]);
                    },
                ]
            ],
        ],
    ])
])?>