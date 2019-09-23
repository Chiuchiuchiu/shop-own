<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/01/31
 *
 * @var string $message
 */

?>

<div id="parking-empty">
    <div class="empty-status">
        <i></i>
        <?= isset($message) ? $message : '未认证房产' ?>
    </div>

    <div class="back-site">
        <a href="/house?" class="btn btn-block btn-primary">返回业主中心</a>
    </div>

</div>
