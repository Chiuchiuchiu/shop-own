<?php
/**
 * @var $type string
 * @var $model \common\models\MemberHouse
 * @var $identity integer
 * @var $houseId integer
 */
?>
<style>
    #cdj-RPBOX .rpbox-cover .show-money {
        display: block;
        position: absolute;
        left: 50%;
        -webkit-transform: translate3d(-50%, -50%, 0);
        transform: translate3d(-50%, -50%, 0);
        top: 23%;
        font-size: 3em;
        color: white;
    }
    #cdj-RPBOX .rpbox-cover .show-text {
        display: block;
        position: absolute;
        left: 50%;
        top: 48%;
        -webkit-transform: translate3d(-50%, -50%, 0);
        transform: translate3d(-50%, -50%, 0);
        font-size: 1em;
        color: #ffffff;
    }
    #cdj-RPBOX .rpbox-cover .show-text-yes {
        display: none;
        position: absolute;
        left: 50%;
        top: 54%;
        -webkit-transform: translate3d(-50%, -50%, 0);
        transform: translate3d(-50%, -50%, 0);
        font-size: 1em;
        color: #ffffff;
    }
    .btn{
        border-radius: 10px;
    }
    #cdj-RPBOX .rpbox-cover .read-packet-btn {
        width: 15em;
        height: 15em;
        display: block;
        -webkit-transform: translate3d(-50%, -50%, 0);
        transform: translate3d(-50%, -50%, 0);
        position: absolute;
        left: 50%;
        top: 78%;
    }
    #cdj-RPBOX .rpbox-cover .read-packet-boximage {
        width: 100%;
        height: auto;
    }
    .show-text-yes img
    {
        width: 100%;
    }
</style>
<div>
<div class="panel tac" id="auth-result">

    <div class="icon icon-success"></div>
    <h2>认证成功</h2>
    <p></p>
    <div class="back-site" data-go="/house/choose-bill">
        <a href="javascript:void(0);" class="btn btn-block btn-primary">去缴费</a>
    </div>

</div>

<div id="cdj-read-packet-icon">
    <img src="../static/images/activity/RedPacketIcon_qixi.png" alt="">
</div>

<div id="cdj-RPBOX">

    <div id="cdj-rpbox-mask">

    </div>

    <div class="rpbox-cover">
        <img class="read-packet-boximage" src="../static/images/activity/red_packet_qixi1.jpg" alt="">

        <span class="show-money">

        </span>

        <span class="show-text" style="text-align: center;">
<?=$Str;?>
        </span>
        <span class="show-text-yes" style="text-align: center;">
            <img data-go="/house/choose-bill"  src="/static/images/activity/qxi.png" alt="">
        </span>

        <span class="read-packet-btn">
            <img src="../static/images/icon/open-red-close.png" alt="">

        </span>

        <button data-go="/coupon" class="btn btn-primary go-coupon">我的优惠券</button>

        <span class="close-read-packet">
            <img src="../static/images/icon/close.png" alt="">
        </span>
    </div>

</div>

<div style="height: 6em">

</div>

    <canvas id="c" style="position: absolute;z-index: -1;text-align: center;"></canvas>
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
$('.read-packet-boximage').attr('src','/static/images/activity/red_packet_qixi_yes.jpg');
                    $('.show-money').html('<i>￥'+res.data.cash +'元</i>');
                    $('.show-text').hide();
                    $('.show-text-yes').show();
                    $('.read-packet-btn').hide();
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