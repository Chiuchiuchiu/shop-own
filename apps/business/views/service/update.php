<?php

/* @var $this yii\web\View */
/* @var $model \common\models\Project */
/* @var $rbac apps\admin\models\RBAC */
/* @var array $projectInfo */
/* @var array $categoryInfo */

$this->title = $model->projectInfo->house_name . $model->name;
$this->params['breadcrumbs'][] = ['label' => '编辑便民电话', 'url' => ['telephone']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= \components\inTemplate\widgets\BackBtn::widget() ?>
<?=
$this->render('_form', [
    'model' => $model,
    'projectInfo' => $projectInfo,
    'categoryInfo' => $categoryInfo,
]); ?>
