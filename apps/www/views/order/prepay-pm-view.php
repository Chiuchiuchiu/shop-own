<?php
/**
 *
 * @var \common\models\PrepayPmOrder $model
 */

?>
<div class="panel" id="order-prepay-pm-view">
    <div class="pm-order-cell">
        <p class="address"><?=$model->house->showName?></p>
        <h4>        <div class="chapter"></div>
            <span>付款金额</span>¥<?= number_format($model->total_amount, 2) ?></h4>
        <ul>
            <li><span>名<em></em><em></em>称</span>物业费预缴</li>
            <li><span>交易时间</span><?= date('Y-m-d H:i:s', $model->payed_at) ?></li>
            <li><span>状<em></em><em></em>态</span>已支付</li>
            <li><span>预付月数</span><?= $model->num ?></li>
            <li><span>订单编号</span><?= $model->number ?></li>
        </ul>
        <div class="hr"></div>
        <div class="red-tips">
            *如需开具发票，请在新物业账单生成后凭当前电子凭证前往物业管理处开具发票
        </div>
    </div>
    <a id="prepay-lottery" href="/prepay-lottery/" data-origin="1"></a>
</div>
