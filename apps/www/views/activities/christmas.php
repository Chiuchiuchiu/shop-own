<div id="christmas" class="container">

    <div class="music">
        <img src="../static/images/activity/christmas/music_christmas.png" id="music" class="play">
    </div>

    <div class="page page0 cur">
        <marquee behavior='scroll' direction=left style="color:white;">财到家订阅号认证百万大奖送送送</marquee>

        <div class="page0-img1">

            <img src="../static/images/activity/christmas/bg3.png?3" class="happy">
            <p></p>
        </div>
        <img src="../static/images/activity/christmas/deep.png" class="deep">
        <img src="../static/images/activity/christmas/moutain.png" class="moutain">
    </div>

    <div class="page page1">
        <marquee behavior='scroll' direction=left style="color:white;">财到家订阅号认证百万红包送送送</marquee>


        <div class="no1">
            <p style="text-align: center;">各位亲爱的业主:</p>
            <p style="text-align: center;">万众期待的中奥物业业主专属活动已经上线</p>
            <p style="text-align: center;">财到家为感谢您一路的关注与支持</p>
            <p style="text-align: center;">业主福利正在发放中</p>
            <p style="text-align: center;">快来瓜分
                <span style="background:white;color:black;font-weight: bold;">百万红包</span>吧!</p>
        </div>

        <!-- <img src="../static/images/activity/christmas/snow_people2.jpg" class="deep" style="width: 6rem;height: 8rem;bottom:4rem;left:3rem;"> -->
        <img src="../static/images/activity/christmas/snow_people.png" class="snow_people" style="display: none;">
        <img src="../static/images/activity/christmas/deep.png" class="deep">
        <img src="../static/images/activity/christmas/moutain.png" class="moutain">


    </div>

    <div class="page page2">
        <marquee behavior='scroll' direction=left style="color:white;">财到家订阅号认证百万红包送送送</marquee>
        <div class="no2">
            <p class="no2_p1">重磅来袭</p>
            <p class="no2_p2">认证送红包，最高得200</p>
        </div>
        <img src="../static/images/activity/christmas/deep.png" class="deep">
        <img src="../static/images/activity/christmas/moutain.png" class="moutain">

    </div>

    <div class="page page3">
        <marquee behavior='scroll' direction=left style="color:white;">财到家订阅号认证百万红包送送送</marquee>
        <div class="page3_div">

            <div class="page3_div_p" style="display: none;">
                <br> &nbsp;&nbsp;活动内容：用户达成房产认证活动条件，即可随机领取1~200元红包。红包通过在线方式领取，领取后存入“业主中心—我的账户—我的优惠券”查看 。
                <br>&nbsp;&nbsp;活动规则：活动期间，同一用户在线完成房产认证，只可领取1次红包。同一房产认证用户的业主、家庭成员、租户，均视为同一用户。
                <br>&nbsp;&nbsp;本次活动最终解释权归财到家所有。
            </div>
            <button data-go="/house" class="button" style="display: none;">
                <a href="">去认证 </a>
            </button>

        </div>
        <img src="../static/images/activity/christmas/deep.png" class="deep">
        <img src="../static/images/activity/christmas/moutain.png" class="moutain">

    </div>

    <img class="xiangxiatishi" src="../static/images/activity/christmas/point.png" style="z-index: 2;">

</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    var html = document.documentElement;
    var hWidth = html.getBoundingClientRect().width;
    html.style.fontSize = hWidth / 15 + "px";


    var startX,
        endX,
        disX,
        startY,
        endY,
        disY,
        height = $(window).height(),
        width = $(window).width();

    setInterval(function () {
        $('.page0').find('.happy').fadeIn('slow');
    }, 1000);
    setInterval(function () {
        $('.page0').find('.moutain').fadeIn('slow');
    }, 2000);
    setInterval(function () {
        $('.page0').find('.deep').fadeIn('slow');
    }, 3000);
    setTimeout(function () {
        $('.xiangxiatishi').fadeIn('slow');
    }, 3000);

    $('.page0').on('touchstart', function (e) {
        startX = e.originalEvent.targetTouches[0].pageX;

    })
    $('.page0').on('touchmove', function (e) {
        endX = e.originalEvent.targetTouches[0].pageX;
        disX = startX - endX;
        disX = disX > 0 ? 'L' : 'R';
    })
    $('.page0').on('touchend', function (e) {
        if (disX == 'L') {
            $('.xiangxiatishi').css('display', 'none');
            $(this).css('display', 'none');
            $(this).next().fadeIn('slow');
            setInterval(function () {
                $('.page1').find('.no1').fadeIn('slow');
            }, 1000);
            setInterval(function () {
                $('.page1').find('.moutain').fadeIn('slow');
            }, 2000);
            setInterval(function () {
                $('.page1').find('.deep').fadeIn('slow');
            }, 3000);
            setInterval(function () {
                $('.page1').find('.snow_people').fadeIn('slow');
            }, 4000);
            setInterval(function () {
                $('.xiangxiatishi').fadeIn('slow');
            }, 3000);

            $('.no1').animate({
                'height': 19 + 'rem',
                'width': 13 + 'rem'
            }, 1000);

        }
    });

    $('.page1').on('touchstart', function (e) {
        startX = e.originalEvent.targetTouches[0].pageX;

    })
    $('.page1').on('touchmove', function (e) {
        endX = e.originalEvent.targetTouches[0].pageX;
        disX = startX - endX;
        disX = disX > 0 ? 'L' : 'R';
    })
    $('.page1').on('touchend', function (e) {
        if (disX == 'L') {
            $('.xiangxiatishi').css('display', 'none');
            $(this).css('display', 'none');
            $(this).next().fadeIn('slow');
            setInterval(function () {
                $('.page2').find('.no2').fadeIn('slow');
            }, 1000);
            setInterval(function () {
                $('.page2').find('.moutain').fadeIn('slow');
            }, 2000);
            setInterval(function () {
                $('.page2').find('.deep').fadeIn('slow');
            }, 3000);
            setInterval(function () {
                $('.xiangxiatishi').fadeIn('slow');
            }, 3000);


            $('.no2').animate({
                'left': 0,
                'top': 5 + 'rem',
                'width': 15 + 'rem',
                'height': 15 + 'rem'
            }, 500);
            $('.page2').animate({
                'transform': 'rotate(' + 360 + 'deg)'
            }, 500);

        } else if (disX == 'R') {
            $(this).css('display', 'none');
            $(this).prev().fadeIn("slow");
            setInterval(function () {
                $('.xiangxiatishi').fadeIn('slow');
            }, 1000);
        }
    });

    $('.page2').on('touchstart', function (e) {
        startX = e.originalEvent.targetTouches[0].pageX;

    })
    $('.page2').on('touchmove', function (e) {
        endX = e.originalEvent.targetTouches[0].pageX;
        disX = startX - endX;
        disX = disX > 0 ? 'L' : 'R';
    })
    $('.page2').on('touchend', function (e) {
        if (disX == 'L') {
            $('.xiangxiatishi').css('display', 'none');
            $(this).css('display', 'none');
            $(this).next().fadeIn('slow');
            setInterval(function () {
                $('.page3').find('.page3_div_p').fadeIn('slow');
            }, 1000);
            setInterval(function () {
                $('.page3').find('.button').fadeIn('slow');
            }, 2000);
            setInterval(function () {
                $('.page3').find('.moutain').fadeIn('slow');
            }, 3000);
            setInterval(function () {
                $('.page3').find('.deep').fadeIn('slow');
            }, 4000);
            setInterval(function () {
                $('.xiangxiatishi').fadeIn('slow');
            }, 3000);

        } else if (disX == 'R') {
            $(this).css('display', 'none');
            $(this).prev().fadeIn("slow");
            setInterval(function () {
                $('.xiangxiatishi').fadeIn('slow');
            }, 1000);
        }
    });

    $('.page3').on('touchstart', function (e) {
        startX = e.originalEvent.targetTouches[0].pageX;

    })
    $('.page3').on('touchmove', function (e) {
        endX = e.originalEvent.targetTouches[0].pageX;
        disX = startX - endX;
        disX = disX > 0 ? 'L' : 'R';
    });

    setInterval(function () {
        $('.xiangxiatishi').animate({
            'right': 0
        }, "slow");
        $('.xiangxiatishi').animate({
            'right': -2 + 'rem'
        }, "slow");
    }, 1000);

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>