<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Butler */
/* @var $projectsArray */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '电子发票', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_form', [
        'model' => $model,
        'projectsArray' => $projectsArray,
    ]);