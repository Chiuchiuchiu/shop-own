<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '文章分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= Html::a('新建文章分类', ['create'], ['class' => 'btn btn-success pull-right']) ?>
<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        'statusText',
        [
            'class' =>\components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{open} {list?id} {update} {delete}',
            'buttons'=>[
                'list'=>function($key,$model,$url){
                return Html::buttonA('文章列表',['/article','id'=>$model->id]);
                },
                'open'=>function($key,$model,$url){
                    return Html::buttonA('访问栏目','http://'.\Yii::$app->params['domain.www'].'/article/list?id='.$model->id,[
                        'target'=>'_blank'
                    ]);
                },
            ]
        ],
    ],
])
]); ?>
