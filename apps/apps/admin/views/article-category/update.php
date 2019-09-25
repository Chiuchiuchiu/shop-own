<?php


/* @var $this yii\web\View */
/* @var $model common\models\ArticleCategory */

$this->title = '更新 ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '文章分类', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

