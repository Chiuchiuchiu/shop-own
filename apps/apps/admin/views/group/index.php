<?php


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?=\components\inTemplate\widgets\RBACButton::widget([
    'route'=>'group/create',
    'option'=>['class'=>"btn btn-w-m btn-primary pull-right"]
])?>
<?=\components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className()
            ],
        ],
    ])
])?>