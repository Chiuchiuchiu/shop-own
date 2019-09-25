<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/9
 * Time: 16:40
 */

/* @var $newDataProvider \yii\data\ActiveDataProvider */
/* @var array $xgCode */
/* @var array $chCode */
/* @var string $chCodeAllowedMaxTime */

?>

<div class="panel" id="coupon-list">
    <div class="coupon-list-top">
        <div id="title-back" class="status">
            <&nbsp;
        </div>
        <div class="top-name">
            我的优惠券
        </div>
    </div>

    <?php if($chCode){?>

        <div class="list-cell">
        <div class="xg-product-t">
            <p>
                物业缴费优惠券 <small style="color: red">(2017.12.24 ~ <?= $chCodeAllowedMaxTime ?>)</small>
            </p>
        </div>

        <div class="coupon-info">

            <?php foreach($chCode as $row){?>
                <?php /* @var $row \common\models\MemberPromotionCode */?>
                <div>
                    <ul class="coupon-list-li">
                        <li class="coupon-li-s-4 decr-amount-left">
                            <span class="decr-amount">￥<?= number_format($row->amount, 2) ?></span>
                        </li>
                        <li class="coupon-li-lg">
                            <?= $row->house->showName ?>
                        </li>

                        <?php if($row->status == 1){?>
                            <li class="coupon-li-s-4">
                                <span id="coupon-copy-code" class="coupon-copy-code">已使用</span>
                            </li>
                        <?php } else {?>
                            <li class="coupon-li-s-4" data-go="/house/choose-bill">
                                <span id="coupon-copy-code" class="coupon-copy-code">去缴费</span>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            <?php }?>

        </div>

    </div>

    <?php }?>

    <?php if($xgCode){?>
        <div class="list-cell">
            <div class="xg-product-t">
                <p>
                    小狗电器优惠券
                </p>
            </div>

            <div class="coupon-info">


                    <?php foreach ($xgCode as $model) { ?>
                        <?php /* @var $model \common\models\MemberPromotionCode */?>
                        <div>
                            <ul class="coupon-list-li">
                                <li class="coupon-li-s-4">
                                    <img src="<?= Yii::$app->params['xg-lists'][$model->xg_product_code]['img'] ?>" alt="<?= $model->xg_product_code ?>">
                                </li>
                                <li class="coupon-li-s-4">
                                    <?= Yii::$app->params['xg-lists'][$model->xg_product_code]['title'] ?>
                                </li>
                                <li class="coupon-li-s-4">
                                    <input id="coupon-xg-code-<?= $model->xg_product_code ?>" type="text" value="<?= $model->promotion_code ?>" readonly />
                                </li>
                                <li class="coupon-li-s-4">
                                    <span id="coupon-copy-code" class="coupon-copy-code" aria-label="复制成功" data-clipboard-target="#coupon-xg-code-<?= $model->xg_product_code ?>">复制</span>
                                </li>
                            </ul>
                        </div>
                    <?php } ?>


            </div>

        </div>
    <?php }?>

</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $(function (){
        $('#title-back').on('click', function (){
            app.go('/member');
        });

        var clipboard = new Clipboard('.coupon-copy-code');
        clipboard.on('success', function(e) {
            var msg = e.trigger.getAttribute('aria-label');
            alert(msg);
            e.clearSelection();
        });
    });

</script>

<?php \common\widgets\JavascriptBlock::end();?>
