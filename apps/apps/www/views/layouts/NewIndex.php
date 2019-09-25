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
    <link rel="stylesheet" href="/static/css/base.css">
    <link rel="stylesheet" href="/static/css/style.css">
    <link rel="stylesheet" href="at.alicdn.com/t/font_8d5l8fzk5b87iudi.css">
    <link rel="stylesheet" href="/static/css/newIndex.css?034">
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/script.js"></script>
    <script type="text/javascript" src="/static/js/TouchSlide.1.1.js"></script>
    <title><?=empty($title)?'财到家': Html::encode($title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
