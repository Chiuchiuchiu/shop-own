<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '新建文章';
$this->params['breadcrumbs'][] = ['label' => '文章内容', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'categoryMap'=>$categoryMap
]) ?>

