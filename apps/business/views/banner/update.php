<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $projects array */

$this->title = '编辑 ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Banner图片', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
    'projects' => $projects,
]) ?>