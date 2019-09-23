<?php


/* @var $this yii\web\View */
/* @var $model common\models\ButlerAuth */
/* @var $dateTime */
/* @var $projectsArray */

$this->title = '编辑授权号: ' . $model->account;
$this->params['breadcrumbs'][] = ['label' => '授权号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
echo $this->render('_form', [
    'model' => $model,
    'projectsArray' => $projectsArray
]);