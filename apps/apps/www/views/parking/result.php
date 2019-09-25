<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/13
 * Time: 11:34
 *
 * @var $msg string
 * @var $aChart string
 * @var $href string
 */

?>

<div id="parking-empty">
    <div class="empty-status">
        <i></i>
        <?= isset($msg) ? $msg : '未找到该车牌'?>
    </div>

    <div class="back-site">
        <a href="<?= isset($href) ? $href : "/parking/" ?>" class="btn btn-block btn-primary"><?= isset($aChart) ? $aChart : '返回支付'?></a>
    </div>

</div>

