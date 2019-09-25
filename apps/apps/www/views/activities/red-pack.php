<?php
/**
 * @var $type string
 * @var $model \common\models\MemberHouse
 * @var $identity integer
 * @var $houseId integer
 * @var $showTips bool
 */
?>
<div class="panel tac" id="auth-result">

    <div class="icon icon-success"></div>
    <h2>认证成功</h2>
    <p></p>

    <?php if($showTips){ ?>
    <p class="j-tips">
        Tips：微信后台回复“福利”，点击抽奖链接，随机抽取精美礼品一份
    </p>
    <?php } ?>

    <div class="back-site" data-go="/house/choose-bill">
        <a href="javascript:void(0);" class="btn btn-block btn-primary">去缴费</a>
    </div>

</div>

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
        <span class="show-text"></span>
        <span class="read-packet-btn">
            <img src="../static/images/activity/open-red.png" alt="">
        </span>

        <button data-go="/coupon" class="btn btn-primary go-coupon">我的优惠券</button>

        <span class="close-read-packet">
            <img src="../static/images/icon/close.png" alt="">
        </span>
    </div>

</div>

<div style="height: 6em">

</div>

    <?php \common\widgets\JavascriptBlock::begin(); ?>

    <script type="text/javascript">
        $('#cdj-rpbox-mask,.rpbox-cover').show();
        $('#cdj-read-packet-icon').click(function (){
            $('#cdj-rpbox-mask,.rpbox-cover').show();
        });

        $('.close-read-packet').click(function (){
            $('#cdj-rpbox-mask,.rpbox-cover').hide();
        });

        $('.read-packet-btn').click(function (){
            $(this).unbind('click');
            $.ajax({
                type: 'GET',
                url: '/prepay-lottery/default/receive-red-envelope?houseId=<?= $houseId ?>',
                data: '',
                success: function (res){
                    if (res.code === 0) {
                        $('.show-money').html(res.data.cash + '<i>元</i>');
                        $('.show-text').html(res.data.message);
                        $('.go-coupon').show();
                    } else {
                        $('.show-text').html(res.message);
                    }
                },
                error: function (){
                    $('.show-text').html('请求服务出错，请稍后重试！');
                },
                dataType: 'json'
            });
        });

    </script>

<?php \common\widgets\JavascriptBlock::end(); ?>