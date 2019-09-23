<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/25
 * Time: 14:16
 *
 * @var $model \apps\www\models\Member
 */

$title = '手机号';
?>

<div class="panel" id="member-mobile">
    <div class="m-text-center">

        <?php if(!empty($model->phone)){?>

            <h5 class="height-4">您已绑定的手机号码</h5>
            <h3 class="height-3">
                <?= substr($model->phone, 0, 3) . '*****' . substr($model->phone, -3) ?>
            </h3>
            <p class="height-2">
                <button data-go="/member/verify-phone">更换</button>
            </p>

        <?php } else {?>

            <p class="mobile-empty">
                <button data-go="/auth/mobile?goUrl=/member/mobile">绑定</button>
            </p>

        <?php }?>

    </div>
</div>
