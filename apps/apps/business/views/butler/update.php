<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Butler */
/* @var array $projectsArray */

$this->title = '管家编辑';
$this->params['breadcrumbs'][] = ['label' => '管家管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
echo \components\inTemplate\widgets\BackBtn::widget(['url'=>['index']]);
echo $this->render('_form', [
        'model' => $model,
        'projectsArray' => $projectsArray,
    ]);
