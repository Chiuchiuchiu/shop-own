<?php
/**
 * @var $type string
 * @var $model \common\models\MemberHouse
 * @var $identity integer
 * @var $houseId integer
 * @var $show bool
 */
?>
<style>
    #cdj-RPBOX .rpbox-cover .read-packet-btn{
        width: 13em;
        height: 4em;
        display: block;
        transform: translate3d(-50%, -50%, 0);
        position: absolute;
        left: 50%;
        top: 80%;
    }
    #cdj-RPBOX .rpbox-cover .show-money {
        display: block;
        position: absolute;
        left: 50%;
        transform: translate3d(-50%, -50%, 0);
        top: 46%;
        font-size: 2.8em;
        color: #FFDB16;

    #line_03{
        width:600px;
    }
</style>
<div class="panel tac" id="auth-result">

    <div class="icon icon-success"></div>
    <h2>提交成功</h2>
    <p></p>

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
        <img class="read-packet-boximage" src="../static/images/activity/activity_red.png" alt="">
        <span class="show-money">

        </span>
        <span class="show-text"></span>
        <span class="read-packet-btn">
            <img class="open_btn_before" src="../static/images/activity/open_red.png" alt="">
            <img class="open_btn_after" onclick="location.href='/house/choose-bill'" src="../static/images/activity/open_red_after.png" alt="" style="display: none;">
        </span>

        <span class="close-read-packet">
            <img src="../static/images/icon/close.png" alt="">
        </span>
    </div>

</div>

<div style="height: 6em">

</div>

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

            $(".read-packet-boximage").attr("src", "../static/images/activity/activity_open.png");
            $('.show-money').html('5<i>元</i>');

            $('.open_btn_before').hide();
            $('.open_btn_after').show();
        });

    </script>

<?php \common\widgets\JavascriptBlock::end(); ?>