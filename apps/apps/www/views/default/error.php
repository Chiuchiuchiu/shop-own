<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = (isset($name) && $name) ? $name : "error";
?>
<div id="parking-empty">
    <div class="empty-status">
        <i></i>
        <?= isset($message) ? $message : '未找到页面' ?>
    </div>

    <div class="back-site">
        <a href="/?" class="btn btn-block btn-primary">返回首页</a>
    </div>

</div>
