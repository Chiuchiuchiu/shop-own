<?php


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array */

$this->title = '编辑 ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '活动管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('_form', [
    'model' => $model,
    'RegionList'=>$RegionList
]) ?>