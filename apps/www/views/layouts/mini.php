<?php
/**
 * @author HQM 2018/11/22
 */
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> 财到家</title>
    <link rel="stylesheet" href="/static/css/base.css"/>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <?php $this->head() ?>

    <?php if(isset($this->blocks['cssFile'])):?>
        <?= $this->blocks['cssFile']; ?>
    <?php endif; ?>

</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php if(isset($this->blocks['jsFile'])):?>
    <?= $this->blocks['jsFile']; ?>
<?php endif; ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
