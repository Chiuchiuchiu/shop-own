<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '新建问卷';
$this->params['breadcrumbs'][] = ['label' => '调研问卷', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'dateTime' => $dateTime
]) ?>

