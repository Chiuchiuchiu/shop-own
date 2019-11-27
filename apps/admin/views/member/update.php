<?php

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = '编辑 ' . $model->nick_name;
$this->params['breadcrumbs'][] = ['label' => '会员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>

<?= $this->render('_form', [
    'model' => $model,
]); ?>
