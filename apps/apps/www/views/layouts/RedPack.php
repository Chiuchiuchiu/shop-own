<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;

$asset = \apps\www\assets\AppAsset::register($this);
$this->registerJs("requirejs.config({baseUrl:'" . $asset->baseUrl . "/js'});require(['app']);");
$title = $this->title;
if(empty($title) && $this->params['_project'] instanceof \common\models\Project){
    $title = $this->params['_project']->house_name;
}
$this->beginPage();
?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <title><?=empty($title)?'财到家': Html::encode($title) ?></title>
        <?= Html::csrfMetaTags() ?>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="full-screen" content="true">
        <meta name="x5-fullscreen" content="true">
        <meta name="360-fullscreen" content="true">
        <meta content="telephone=no" name="format-detection" />
        <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
        <meta name="keywords" content="财到家">
        <meta name="Description" content="粽情粽意—端午节中奥温情回馈业主，10000份粽子免费送！">
        <?php $this->head() ?>
    </head>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <?php $this->endBody() ?>
    </html>
<?php $this->endPage() ?>