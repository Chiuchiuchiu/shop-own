<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/23
 * Time: 14:16
 */

/* @var \common\models\MemberHouse $model */
/* @var integer $house_id */

?>

<?php if(empty($model)) {?>

    <div class="empty-status"><i></i>暂无相关内容</div>

<?php } else {?>

    <div class="panel tac" id="auth-result">
        <div class="success-title">
            认证成功
        </div>

        <div class="icon icon-success-blue"></div>
        <h4>
            恭喜您，认证成功，请点击领取按钮领取奖品
        </h4>
    </div>

    <div id="auth-success-footer">
        <button data-go="/default">返回首页</button>
        <button data-go="/activities/check-receive?house_id=<?= $house_id ?>" class="auth-prize">
            <i class="auth-icon-dragon"></i>
            领取奖品
        </button>
    </div>

<?php } ?>