<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 15:30
 */
/* @var \common\models\IndividualLabels $model */

$this->title = '新增标签';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);

?>

<?=
    $this->render('_form', ['model' => $model,])
?>