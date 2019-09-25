<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
$this->title = $name;
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>
    <p>
        服务器拒绝了你的请求，并向你抛出了一个错误！
    </p>
    <p>
        如果你对这个错误有任何异议，请联系帅气的技术哥哥. Thank you.
    </p>
    <p>
        <button class="btn btn-primary" onclick="javascript:history.back(-1);">返回上一级</button>
    </p>
</div>
