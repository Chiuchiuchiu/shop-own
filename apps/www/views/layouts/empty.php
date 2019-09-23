<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;

$title = $this->title;
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="full-screen" content="true">
    <meta name="x5-fullscreen" content="true">
    <meta name="360-fullscreen" content="true">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
    <?= Html::csrfMetaTags() ?>
    <title><?=empty($title)?'财到家': Html::encode($title) ?></title>
    <?php $this->head() ?>
</head>
<body class="">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
