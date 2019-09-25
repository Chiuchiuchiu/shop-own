<?php
/**
 *
 * @var \common\models\PmOrder $model
 */


?>
<div class="panel" id="order-pm-view">
    <div class="pm-order-cell">
        <p class="address"><?= $model->house->showName ?></p>
        <p class="address-small">(<?= $model->house->ancestor_name ?>)</p>
        <h4>
            <?php if($model->status != \common\models\PmOrder::STATUS_REFUND){?>
                <div class="chapter"></div>
            <?php }?>
            <span>付款金额</span>¥<?= number_format($model->total_amount, 2) ?></h4>
        <ul>
            <li><span>交易时间</span><?= date('Y-m-d H:i:s', $model->payed_at) ?></li>
            <li><span>状<em></em><em></em>态</span><?= $model->statusText ?></li>
            <li style="text-decoration: line-through">
                <?php if(isset($model->pmOrderDiscounts)){?>
                    <span>金额：</span>￥<?= bcadd($model->total_amount, $model->pmOrderDiscounts->discounts_amount, 2); ?>
                <?php } else {?>
                    <span>金额：</span>￥<?=number_format($model->total_amount,2)?>
                <?php }?>

            </li>
            <?php if(isset($model->pmOrderDiscounts)){?>

                <li>
                    <span>优惠：</span>
                    <?= number_format($model->pmOrderDiscounts->discounts_amount, 2) ?>
                </li>

            <?php } ?>
            <li><span>订单编号</span><?= $model->number ?></li>
        </ul>
        <h5>缴费项目</h5>
        <ul>
            <?php if (is_array($model->items)) foreach ($model->items as $item) { ?>

                <li class="item">
                    <span><?= $item->bill_date ?> <?= $item->charge_item_name ?></span>¥<?= number_format($item->amount, 2) ?>
                </li>

                <li class="item">
                    <span>用量/面积</span>
                    <?= $item->usage_amount ?>
                </li>

            <?php } ?>
        </ul>
        <div class="hr"></div>
        <div class="red-tips">
            如需开具发票请凭该电子凭证前往管理处索取
        </div>

    </div>

    <?php if(in_array($model->project_house_id, Yii::$app->params['allow_project_fpzz']['project_house_id']) && !(\common\models\SysSwitch::getValue('Tcis')) && Yii::$app->params['allow_project_fpzz']['allow_use']){?>
        <div class="btn-comfirm-submit">
            <?php if(!isset($model->pmOrderFpzz->status)){?>

                <?php if($model->discount_status < 1) {?>
                    <a href="/tcis/recipt?id=<?= $model->id ?>" id="btn-comfirm-submit" class="btn btn-a">开具发票</a>
                <?php }?>

            <?php } else {?>
                <a href="/tcis/detail?id=<?= $model->pmOrderFpzz->id ?>" id="btn-comfirm-submit" class="btn btn-a"><?= $model->pmOrderFpzz->getStatusListsText() ?></a>
            <?php }?>
        </div>
    <?php } else if(in_array($model->project_house_id, Yii::$app->params['allow_project_fpzz']['project_house_id']) && \common\models\SysSwitch::getValue('Tcis') && $model->total_amount >= Yii::$app->params['test_member_amount']){?>

        <div class="btn-comfirm-submit">
            <?php if(!isset($model->pmOrderFpzz->status)){?>

                <?php if($model->discount_status < 1){?>

                <a href="/tcis/recipt?id=<?= $model->id ?>" id="btn-comfirm-submit" class="btn btn-a">开具发票</a>
                <?php }?>

            <?php } else {?>
                <a href="/tcis/detail?id=<?= $model->pmOrderFpzz->id ?>" id="btn-comfirm-submit" class="btn btn-a"><?= $model->pmOrderFpzz->getStatusListsText() ?></a>
            <?php }?>
        </div>

    <?php }?>
</div>

<?php if(in_array($model->project_house_id, Yii::$app->params['cash_red_packet']['project_lists']) && $model->payed_at > 1502294400){?>

    <div id="cdj-read-packet-icon">
        <img src="../static/images/activity/RedPacketIcon.png" alt="">
    </div>

    <div id="cdj-RPBOX">

        <div id="cdj-rpbox-mask">

        </div>

        <div class="rpbox-cover">
            <img class="read-packet-boximage" src="../static/images/activity/red_packet.jpg" alt="">

            <span class="show-money">

            </span>

            <span class="show-text">

            </span>

            <span class="read-packet-btn">
                <img src="../static/images/activity/open-red.png" alt="">
            </span>
            <span class="close-read-packet">
                <img src="../static/images/icon/close.png" alt="">
            </span>
        </div>

    </div>

<?php } ?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $('#cdj-read-packet-icon').click(function (){
        $('#cdj-rpbox-mask,.rpbox-cover').show();
    });

    $('.close-read-packet').click(function (){
        $('#cdj-rpbox-mask,.rpbox-cover').hide();
    });

    $('.read-packet-btn').click(function (){
        $(this).unbind('click');
        $.getJSON('/prepay-lottery/default/hand-out-envelopes?projectId=<?= $model->project_house_id ?>&pmOrderId=<?= $model->id?>', function (res){
            if (res.code === 0) {
                $('.show-money').html(res.data.cash + '<i>元</i>');
                $('.show-text').html('请查收微信红包');
            } else {
                alert(res.message);
            }
        });
    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
