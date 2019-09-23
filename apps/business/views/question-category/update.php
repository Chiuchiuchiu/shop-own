<?php


/* @var $this yii\web\View */
/* @var $model common\models\PropertyAnnouncement */
/* @var $categoryMap array */

$this->title = '编辑问卷' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '调研问卷', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
    'QuestionCategory'=>$QuestionCategory
]) ?>

