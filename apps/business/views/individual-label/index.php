<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 14:39
 */

/* @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = '标签列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= \yii\helpers\Html::a('新增标签', ['create'], ['class' => 'btn btn-success pull-left']) ?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \components\inTemplate\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        'class',
        [
            'class' =>\components\inTemplate\widgets\RBACActionColumn::className(),
            'template'=>'{update}',
            'buttons'=>[

            ]
        ],
    ],
])
]); ?>