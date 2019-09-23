<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/23
 * Time: 14:16
 */

/* @var \common\models\MemberHouse $model */
/* @var string $type */

$type = isset($type) ? $type : '';

?>

<div class="panel tac" id="activity-error">
    <div class="error-title">
        领取失败
    </div>
    <div class="icon icon-error"></div>

    <?php if ($type == 'no-data') {?>
        <div class="ac-error-p">
            <p>1.认证业主身份后，即可领取粽子一份</p>
            <p>2.已认证业主，缴费即可领取粽子一份</p>
            <p>3.每套房产仅可领取一次</p>
            <p>*活动最终解释权归活动方所有</p>
        </div>
    <?php } elseif ($type == 'end') {?>
        <p>活动已结束</p>
    <?php } else {?>
        <p>该房产已领取</p>
    <?php } ?>

</div>

<div id="activity-result-footer">
    <button data-go="/default">返回首页</button>
</div>