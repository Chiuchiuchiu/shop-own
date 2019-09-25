<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>财到家欢迎您</title>
    <link rel="stylesheet" href="/static/css/swiper.min.css">
</head>
<style>
    *{
        margin: 0;
        padding: 0;
    }
    a{
        text-decoration: none;
        color: #000;
        font-style: normal;
    }
    .swiper-container {
        position: fixed;
        width: 100%;
        height:100%;
    }
    .swiper-slide img{
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    .swiper-pagination-bullet{
        width:10px;
        height: 10px;
    }
    .swiper-pagination-bullet-active{
        width: 34px;
        border-radius: 17px;
    }
    .swiper-container-horizontal>.swiper-pagination-bullets,
    .swiper-pagination-custom,
    .swiper-pagination-fraction{
        bottom: 60px;
    }
    .setTime{
        position: absolute;
        top:8px;
        right: 25px;
        font-size: 14px;
        width: 24px;
        height: 24px;
        border-radius: 12px;
        border: 1px solid #8a8a8a;
        text-align: center;
        line-height: 26px;
    }
    .swiper-slide i{
        position: absolute;
        top: 10px;
        right: 35px;
        font-size: 14px;
        font-style: 400;
        width: 50px;
        height: 26px;
        border: 1px solid #ddd;
        text-align: center;
        line-height: 26px;
        border-radius: 12px;
        color:#FABE00;
    }
    .firstPage{
        position: absolute;
        bottom: 80px;
        right: 34%;
        width: 100px;
        height: 30px;
    }
    .showClass{
        display: none;
    }
    .icon_logo{
        height: 50px;
        background-color: #fff;
        position: absolute;
        bottom: 0;
        width: 100%;

    }
    .icon_logo img{
        width: 32px;
        height: 32px;
        background-size: 100%;
        margin: 5px 0 0 15px;

    }
    .icon_logo span{
        display: inline-block;
        font-size: 14px;
        position: absolute;
        top: 12px;
        left: 52px;
        color: #FABE00;
        font-weight: 700;
    }
    .icon_logo > i > a:active {
        color: #000; /*鼠标按下的颜色变化*/
    }
</style>
<body>
<!-- 欢迎页面 -->
<div class="swiper-container">
    <div class="swiper-wrapper">
        <?php foreach ($newDataProvider  as $model) { ?>
            <div class="swiper-slide">
                <a href="<?=  $model['url']  ?>"><img src="<?= Yii::getAlias($model['pic']) ?> "  ></a>
                <span class="setTime"></span>
                <!-- 点击跳过进入主页 -->
                <div class="icon_logo">
                    <img src="/static/images/welcome/cdj_logo.png" alt="">
                    <span>开启财到家</span>
                    <i><a href="/default/index">跳过</a></i>
                </div>
            </div>
            <?php
        }
        ?>

    </div>
    <!-- 如果需要分页器 -->
    <div class="swiper-pagination"></div>
</div>
<script src="/static/js/swiper.min.js"></script>
<script src="/static/js/jquery-3.4.1.min.js"></script>
<script>
    var n = 2;
    $(".setTime").html(n);

    var mySwiper = new Swiper('.swiper-container', {

        direction: 'horizontal', // 垂直切换选项
        //  loop: true, // 循环模式选项
        speed:300,
        //  autoplay:true,//自动轮播且时间
        // autoplay: {
        // delay: 3000,
        // stopOnLastSlide: false,
        // disableOnInteraction: true,
        // },

        // 如果需要分页器
        pagination: {
            el: '.swiper-pagination',
        },

    });
    // 跳过 直接进去主页
    trim();
    function trim(){
        var height = $(window).height();
        console.log(height);
        var height1 = window.screen.availHeight;
        console.log(height1);

        var settime = setInterval(function () {
            if(n == 0){
                clearInterval(settime);
                window.location.href = "/default/index"; // 填写跳转的地址
            }else{
                n--;
                $(".setTime").text(n)
            }
        },1000)
    }

    //轮播图最后一张的时候移除。showClass
    var arrey = document.querySelectorAll("img")
    // console.log(arrey);
    for(var i = 0;i<arrey.length;i++){
        if(i==arrey.length-1){
            $('.showClass').eq(i).removeClass('showClass');
        }else{
            $(".showClass").eq(i).addClass("showClass")
        }
    }
</script>
</body>
</html>