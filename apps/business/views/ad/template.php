<?php

use yii\helpers\Html;
?>
<body class="bg-f1">



<!--<div class="property-ann" style="line-height: 3em;margin-top: 0.5em;">-->
<!--    <section class="notice" style="display: flex;">-->
<!--        <label style="font-size: 16px;font-weight: bold;color: red;margin-left: 1em;">公告：</label><div class="clearfix">因服务升级，缴费、房产认证业务暂停使用！</div>-->
<!--    </section>-->
<!--</div>-->

<!-- --------------------广告位-------------------------------- -->
<style>
    .advertising{
        width:100%;
        height:300px;
        margin-bottom:5px;
        background-color:red;

    }
    .advertising_left{
        float:left;
        width:50%;
        height:300px;
    }
    .advertising_left img{
        height:100%;
        width:100%;
    }
    .advertising_right{
        float:right;
        width:50%;
        height:300px;
    }
    .advertising_top{
        height:50%;
    }
    .advertising_top img{
        width:100%;
        height:100%;
    }
    .advertising_bottom{
        height:50%;
    }
    .advertising_bottom img {
        width:100%;
        height:100%;
    }
    .advertising_bottom ul{
        height:100%;
    }
    .advertising_bottom ul li {
        float:left;
        width:50%;
        height:100%;
    }
    .advertising_bottom ul li img{
        height:100%;
    }
    .advertising_three{
        height:250px;
        width:100%;
        margin-bottom:5px;
    }
    .advertising_three img{
        width:100%;
        height:100%;
    }
    .advertising_four{
        width:100%;
        height:160px;
    }
    .advertising_four ul li {
        float:left;
        width:33.33%;
    }
    .advertising_four ul li img{
        width:100%;
        height:100%;
    }
    .advertising_five{
        height:300px;
        width:100%;
        margin-bottom:5px;
    }
    .advertising_five .text_left{
        float:left;
        width:50%;
        height:100%;
    }
    .text_left ul,
    .text_right ul{
        height:100%;
        width:100%;
    }
    .text_left ul li,
    .text_right ul li{
        height:50%;
        width:100%;
    }
    .text_left ul li img,
    .text_right ul li img{
        width:100%;
        height:100%;
    }
    .advertising_five .text_right{
        float:left;
        width:50%;
        height:100%;
    }
    .advertising_six{
        width:100%;
        height:300px;
        margin-bottom:5px;
    }
    .advertising_six ul{
        height:100%;
        width:100%;
    }
    .advertising_six ul li {
        height:50%;
        width:100%;
    }
    .advertising_six ul li img {
        height:100%;
        width:100%;
    }
    .advertising_seven{
        width:100%;
        height:200px;
        margin-bottom:5px;
    }
    .advertising_seven ul {
        height:100%;
        width:100%;
    }
    .advertising_seven ul li {
        float:left;
        width:50%;
        height:100%;
    }
    .advertising_seven ul li img{
        width:100%;
        height:100%;
    }
    .advertising_eight{
        width:100%;
        height:250px;
        margin-bottom:5px;
    }
    .eight_lfet{
        float:left;
        height:100%;
        width:35%;
    }
    .eight_lfet img{
        width:100%;
        height:100%;
    }
    .eight_right{
        float:left;
        width:65%;
        height:100%;
    }
    .eight_top{
        height:50%;
        width:100%;
    }
    .eight_top img{
        height:100%;
        width:100%;
    }
    .eight_bottom{
        height:50%;
        width:100%;
    }
    .eight_bottom ul{
        height:100%;
        width:100%;
    }
    .eight_bottom ul li {
        float:left;
        width:50%;
        height:100%;
    }
    .eight_bottom ul li img{
        width:100%;
        height:100%;
    }
    .advertising_ten{
        width:100%;
        height:250px;
        background-color:red;
    }
</style>
<!-- ------------模块1-------------- -->
<div class="advertising">
    <div class="advertising_left">
        <img src="/static/images/text/one.png" alt="">
    </div>
    <div class="advertising_right">
        <div class="advertising_top">
            <img src="/static/images/text/two.png" alt="">
        </div>
        <div class="advertising_bottom">
            <img src="/static/images/text/three1.png" alt="">
        </div>
    </div>
</div>

<!-- ------------模块2-------------- -->
<div class="advertising">
    <div class="advertising_top">
        <img src="/static/images/text/two.png" alt="">
    </div>
    <div class="advertising_bottom">
        <ul>
            <li>
                <img src="/static/images/text/three1.png" alt="">
            </li>
            <li>
                <img src="/static/images/text/one.png" alt="">
            </li>
        </ul>
    </div>
</div>

<!-- ------------模块3-------------- -->
<div class="advertising_three">
    <img src="/static/images/text/two.png" alt="">
</div>

<!-- ------------模块4-------------- -->
<div class="advertising_four">
    <ul>
        <li>
            <img src="/static/images/text/one.png" alt="">
        </li>
        <li>
            <img src="/static/images/text/five.png" alt="">
        </li>
        <li>
            <img src="/static/images/text/one.png" alt="">
        </li>
    </ul>
</div>

<!-- ------------模块5-------------- -->
<div class="advertising_five">
    <div class="text_left">
        <ul>
            <li>
                <img src="/static/images/text/one.png" alt="">
            </li>
            <li>
                <img src="/static/images/text/two.png" alt="">
            </li>
        </ul>
    </div>
    <div class="text_right">
        <ul>
            <li>
                <img src="/static/images/text/two.png" alt="">
            </li>
            <li>
                <img src="/static/images/text/one.png" alt="">
            </li>
        </ul>
    </div>
</div>

<!-- ------------模块6-------------- -->
<div class="advertising_six">
    <ul>
        <li>
            <img src="/static/images/text/two.png" alt="">
        </li>
        <li>
            <img src="/static/images/text/three.jpg" alt="">
        </li>
    </ul>
</div>

<!-- ------------模块7-------------- -->
<div class="advertising_seven">
    <ul>
        <li>
            <img src="/static/images/text/two.png" alt="">
        </li>
        <li>
            <img src="/static/images/text/three.jpg" alt="">
        </li>
    </ul>
</div>

<!-- ------------模块8-------------- -->
<div class="advertising_eight">
    <div class="eight_lfet">
        <img src="/static/images/text/five.png" alt="">
    </div>
    <div class="eight_right">
        <div class="eight_top">
            <img src="/static/images/text/two.png" alt="">
        </div>
        <div class="eight_bottom">
            <ul>
                <li>
                    <img src="/static/images/text/one.png" alt="">
                </li>
                <li>
                    <img src="/static/images/text/six.png" alt="">
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- ------------模块9-------------- -->
<div class="advertising_eight">
    <div class="eight_lfet">
        <img src="/static/images/text/five.png" alt="">
    </div>
    <div class="eight_right">
        <div class="eight_bottom">
            <ul>
                <li>
                    <img src="/static/images/text/one.png" alt="">
                </li>
                <li>
                    <img src="/static/images/text/six.png" alt="">
                </li>
            </ul>
        </div>
        <div class="eight_top">
            <img src="/static/images/text/two.png" alt="">
        </div>
    </div>
</div>
<!-- ------------模块10-------------- -->
<div class="advertising_ten">
    <div class="eight_lfet">
        <img src="/static/images/text/five.png" alt="">
    </div>
    <div class="eight_right">
        <div class="eight_bottom">
            <ul>
                <li>
                    <img src="/static/images/text/one.png" alt="">
                </li>
                <li>
                    <img src="/static/images/text/six.png" alt="">
                </li>
                <li>
                    <img src="/static/images/text/six.png" alt="">
                </li>
                <li>
                    <img src="/static/images/text/one.png" alt="">
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- -------------------广告位end-------------------------------->

<script type="text/javascript">


</script>
