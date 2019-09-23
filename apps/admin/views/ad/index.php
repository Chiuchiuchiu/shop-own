<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = '广告管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/ad')]); ?>
<div class="form-group">
    <label class="control-label col-sm-3">查找</label>
    <div class="col-sm-6">
        <div class="input-group m-b">
            <input type="text" name="search" placeholder="标题" value="<?= $search ?>" class="form-control">
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
<?= Html::a('添加广告', ['create'], ['class' => 'btn btn-success pull-right']) ?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
            'id',
        [
            'label'=>'图片',
            'format' => 'raw',
            'value' => function(\common\models\Ad $model){
                if(!empty($model->pic)){
                    return '<img src="'.Yii::getAlias($model->pic).'" width="auto" height="100px">';
                }else{
                    return '-';
                }

            }
        ],
        'title',
        [
            'label'=>'链接地址',
            'value' => function(\common\models\Ad $model){
                return $model->getUrlType($model->type);
            }
        ],
        [
            'label'=>'广告位置',
            'value' => function(\common\models\Ad $model){
                return $model->getTypeText();
            }
        ],
        [
            'label'=>'开始时间',
            'value' => function(\common\models\Ad $model){
                return date("Y-m-d",$model->start_time);
            }
        ],
        [
            'label'=>'结束时间',
            'value' => function(\common\models\Ad $model){
                return date("Y-m-d",$model->end_time);
            }
        ],
        'url',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {delete}'
        ]
    ],
])
]); ?>
