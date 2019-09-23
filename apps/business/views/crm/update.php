<?php

/* @var $this yii\web\View */
/* @var $model \common\models\ButlerVisitIndicators */
/* @var $rbac apps\business\models\RBAC */

$this->title = '编辑 ' . $model->butler->nickname;
$this->params['breadcrumbs'][] = ['label' => '指标', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= \components\inTemplate\widgets\BackBtn::widget() ?>
<?= $this->render('_form', [
    'model' => $model,
]); ?>
