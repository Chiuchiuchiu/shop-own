<?php

/* @var $this yii\web\View */
/* @var $model common\models\Shop */
/* @var array $categoryInfo */
/* @var $ShopOfficialFileModel common\models\ShopOfficialFile*/

$this->title = '新增商铺';
$this->params['breadcrumbs'][] = ['label' => '商铺管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>

<?= $this->render('_form', [
    'model' => $model,
]); ?>

