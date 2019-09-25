<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Butler */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '管家管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_form', [
        'model' => $model,
    ]);