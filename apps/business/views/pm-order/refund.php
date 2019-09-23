<?php


/* @var $this yii\web\View */
/* @var $model common\models\PmOrder */
/* @var $dateTime  */
/* @var $projectsArray */

$this->title = '退款申请';
$this->params['breadcrumbs'][] = ['label' => '退款申请', 'url' => ['order-lists']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['order-lists']]);
echo $this->render('_form', ['model' => $model, 'orderInfo' => $orderInfo]);