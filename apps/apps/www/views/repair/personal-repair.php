<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 15:45
 */
?>

<div class="panel" id="personal-repair">

    <div class="bill list">
        <i class="icon" data-go="/repair/choose-bill">待支付</i>
    </div>

    <div class="payed list">
        <i class="icon" data-go="/repair/payed-list">已支付</i>
    </div>

    <div class="auth list">
        <i class="icon" data-go="/repair/list?status=0&flowStyleID=w&site=2">个人报修历史</i>
    </div>

    <div class="repair list">
        <i class="icon" data-go="/repair?flowStyleID=w&site=2">提交上门维修</i>
    </div>
</div>