<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model apps\backend\models\ManagerGroup */
/* @var $rbac */
/* @var $userGroup */

$this->title = '编辑角色: ' . $model->name;
$this->params['breadcrumbs'][] = '系统设置';
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?=\components\inTemplate\widgets\BackBtn::widget()?>

    <?= $this->render('_form', [
        'model' => $model,
        'rbac'=>$rbac,
        'userGroup' => $userGroup,
    ]) ?>
