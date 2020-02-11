<?php

/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $sign $sign */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */
/* @var array $projectRegion */
/* @var int $projectRegionId */

use \common\models\FreightTemplate;
use \components\inTemplate\widgets\Html;

$this->title = '运费模板管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/shop/express')]);
?>
    <div class="form-group">
        <div class="col-sm-2">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="关键字" value="<?= $search ?>" class="form-control">
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
echo \components\inTemplate\widgets\Html::a('新增', ['create-express', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-success pull-right']);
?>
<?=\components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'label'=>'配送地区',
                'format'=>'raw',
                'value'=>function(FreightTemplate $model){

                    return Html::label('', '', ['class' => 'text-warning']);
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template'=>'{update} {delete}',
                /*'buttons'=>[
                    'sync-houses'=>function($url, $model, $key){
                        return Html::a('同步楼盘数据', ['sync-houses','projectId'=>$model->house_id], ['class' => 'btn btn-xs btn-success']);
                    }
                ],*/
            ],
        ],
    ])
])?>