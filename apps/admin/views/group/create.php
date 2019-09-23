<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model apps\backend\models\ManagerGroup */
/* @var $rbac */
/* @var $userGroup */

$this->title = '新建角色';
$this->params['breadcrumbs'][] = '系统设置';
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manager-group-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'rbac'=>$rbac,
        'userGroup' => $userGroup,
    ]) ?>

</div>
