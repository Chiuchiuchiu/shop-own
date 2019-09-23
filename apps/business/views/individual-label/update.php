<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 15:30
 */
/* @var \common\models\IndividualLabels $model */

$this->title = '更新';
$this->params['breadcrumbs'][] = $this->title;

?>

<?=
    $this->render('_form', ['model' => $model,])
?>