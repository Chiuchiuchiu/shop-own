<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '编辑题目';
$this->params['breadcrumbs'][] = ['label' => '中秋题目', 'url' => ['answer-list']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'data' => $data,
]) ?>

