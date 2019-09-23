<?php


/* @var $this yii\web\View */
/* @var $model common\models\Notice */
/* @var $dateTime  */
/* @var $projectsArray */

$this->title = '新建授权号';
$this->params['breadcrumbs'][] = ['label' => '授权号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
echo $this->render('_form', [
    'model' => $model,
    'projectsArray' => $projectsArray,
]);