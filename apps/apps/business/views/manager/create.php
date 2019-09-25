<?php

/**
 * @var $this  yii\web\View
 * @var $model  \apps\admin\models\Manager
 */
$this->title = "新同事";
$this->params['breadcrumbs'][]=["label"=>"账户管理",'url'=>\yii\helpers\Url::toRoute('user')];
$this->params['breadcrumbs'][]=$this->title;
?>
<?=\components\inTemplate\widgets\BackBtn::widget()?>
<?=\components\inTemplate\widgets\IBox::widget(['content'=>$this->render('_form',['model'=>$model])])?>