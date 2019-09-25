<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $projects array */

$this->title = '新建广告';
$this->params['breadcrumbs'][] = ['label' => '广告图片', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
    'projects' => $projects,
    'dateTime' => $dateTime,
    'adlist' => $adlist,
]) ?>

