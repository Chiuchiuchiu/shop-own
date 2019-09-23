<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Project */
/* @var $rbac apps\admin\models\RBAC */
/* @var array $projectRegion */

$this->title = '编辑 ' . $model->house_name;
$this->params['breadcrumbs'][] = ['label' => '项目', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= \components\inTemplate\widgets\BackBtn::widget() ?>
<?= $this->render('_form', [
    'model' => $model,
    'projectRegion' => $projectRegion,
]); ?>
