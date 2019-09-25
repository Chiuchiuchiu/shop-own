<?php
/**
 * @var $_user \apps\www\models\Member
 * @var $zsy
 */
?>
<div class="panel" id="member">
    <ul class="list">
        <li data-go="/member/mobile">
            <i class="icon-member-mobile"></i>
            手机号：<?= substr($_user->phone, 0, 3) . '*****'. substr($_user->phone, -3)?>
        </li>
        <li>
            <i class=""></i>
            昵称：<?= $_user->nickname ?>
        </li>
        <li>
            <i class=""></i>
            姓名：<?= $_user->name ?>
        </li>
    </ul>
</div>
