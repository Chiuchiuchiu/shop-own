<?php
/**
 *
 * @var \common\models\ParkingOrder $model
 */


?>
<div class="panel" id="order-pm-view">
    <div class="pm-order-cell">
        <p class="address"><?= $model->typeText ?></p>
        <h4>
            <?php if($model->status != \common\models\PmOrder::STATUS_REFUND){?>
                <div class="chapter"></div>
            <?php }?>
            <span>付款金额</span>¥<?= number_format($model->amount, 2) ?></h4>
        <ul>
            <li><span>交易时间</span><?= date('Y-m-d H:i:s', $model->payed_at) ?></li>
            <?php if($model->type == \common\models\ParkingOrder::TYPE_M){?>
                <li><span>数<em></em><em></em>量</span><?= $model->quantity ?></li>
                <li><span>缴费开始日期</span><?= date('Y-m-d', $model->effect_date) ?></li>
                <li><span>缴费结束日期</span><?= date('Y-m-d', $model->expire_date) ?></li>
            <?php }?>
            <li><span>状<em></em><em></em>态</span><?= $model->statusText ?></li>
            <li><span>订单编号</span><?= $model->number ?></li>
        </ul>

        <div class="hr"></div>

    </div>

</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    wx.ready(function (){
        wx.hideAllNonBaseMenuItem();
    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
