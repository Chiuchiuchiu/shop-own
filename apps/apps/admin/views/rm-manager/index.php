<?php

/* @var $this yii\web\View */
/* @var $dataProvider  \yii\data\ActiveDataProvider */
$this->title = "分公司账户管理";
$this->params['breadcrumbs'][] = $this->title;
use \components\inTemplate\widgets\RBACButton;

?>
<?= RBACButton::widget([
    'route' => 'rm-manager/create',
    'option' => ['class' => "btn btn-w-m btn-success pull-right"],
]) ?>
<?= \components\inTemplate\widgets\IBox::widget([
    'content' => \components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'ID',
            'real_name',
            'email',
            [
                'label' => '所属项目',
                'value' => function (\apps\rm\models\RmManager $item) {
                    return $item->region->name;
                }
            ],
            [
                'label' => '用户组',
                'value' => function (\apps\rm\models\RmManager $item) {
                    return $item->group->name;
                }
            ],
            [
                'label' => "最后登录时间",
                'value' => function (\apps\rm\models\RmManager $item) {
                    $res = $item->lastLoginLog;
                    return $res ? date("Y-m-d H:i:s", $res->time) : "从未登录";
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => '{update} {delete}',
            ],
        ]
    ]),
]) ?>
