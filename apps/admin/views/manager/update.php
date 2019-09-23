<?php

/**
 * @var $this  yii\web\View
 * @var $model  \apps\admin\models\Manager
 */
$this->title = "编辑-".$model->real_name;
$this->params['breadcrumbs'][]=["label"=>"账户管理",'url'=>'?r=user/'];
$this->params['breadcrumbs'][]=$this->title;
?>
<?=\components\inTemplate\widgets\BackBtn::widget()?>

<?=\components\inTemplate\widgets\IBox::widget(['content'=>$this->render('_form',['model'=>$model])])?>
