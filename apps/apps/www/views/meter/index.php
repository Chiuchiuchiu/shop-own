 <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">

        <title><?= $cdj_header_tip?> | 水电抄表</title>
        <link rel="stylesheet" href="/static/css/base.css" />
        <link rel="stylesheet" href="/static/css/style.css" />
        <script src="/static/js/jquery-2.1.1.min.js"></script>
        <script src="/static/js/layer_mobile/layer.js"></script>
        <style type="text/css">
            .bg-f1 {
                background-color: #f1f1f5;
            }
            *, body, div, p, h1, h2, h3, h4, h5, h6, ul, ol, li, form, fieldset, input, select, textarea, blockquote, th, td, dl, ul, dt, dd, nav {
                word-break: break-all;
                margin: 0;
                padding: 0;
            }
            body {
                margin: 0 auto;
                min-width: 320px;
                max-width: 640px;
                min-height: 480px;
            }
            body {
                font-family: 'Heiti SC Light','Microsoft YaHei', Gadget, sans-serif;
                font-size: 14px;
                line-height: 1.42857143;
                color: #4d4d4d;
            }
            .userHeader {
                height: 180px;
                position: relative;
                background-image: url(/static/meter/topbg.png);
                background-repeat: no-repeat;
                background-position: initial;
                background-size: cover;
            }
            .userHeader h3 {
                margin-bottom: 10px;
                font-size: 16px;
                color: #ffffff;
                text-shadow: 1px 1px 1px #292828;
                padding-top: 150px;
                text-align: center;
            }
            .mainMenu{
                width: 100%;
                height: 36px;
                background-color: #FFFFFF;
            }
            .mainMenu ul{
                list-style: none;
            }
            .mainMenu ul li{
                width: 49%;
                height: 36px;
                line-height: 36px;
                float: left;
                text-align: center;
            }
            .mainMenu ul li.new{
                width: 50%;
                height: 36px;
                line-height: 36px;
                float: left;
                text-align: center;
            }
            .mainMenu ul li.linet{
                width: 1px;
                height: 24px;
                margin-top: 5px;
                background-color: #CDCDCD;
                float: left;
                text-align: center;
            }
            .mainMenu ul li a{
                width: 100%;
                height: 36px;
                font-size: 14px;
                padding-left: 24px;
                color:#5b5a59;
            }
            .mainMenuicon1{
                background-image: url(/static/meter/icon4.png);
                background-repeat: no-repeat;
                background-size: contain;
            }
            .mainMenuicon2{
                background-image: url(/static/meter/icon5.png);
                background-repeat: no-repeat;
                background-size: contain;
            }


            .mainBody{
                margin: 8px;
                background-color: #FFFFFF;
                height: 180px;
            }
            .mainBody ul{
                list-style: none;
            }
            .mainBody ul li{
                float: left;
                width: 50%;
            ;
            }
            .mainBody ul li.left01{
                background-color: #ffffff;
                height: 200px;
                border-right: 0.5px solid #CDCDCD;
            }
            .mainBody ul li.left01 a{
                margin-left: auto;
                margin-right: auto;
                width:  100px;
                margin-top: 30px;
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }
            .mainBody ul li.left01 a .title01{
                display: block;
                height: 20px;
                margin-bottom: 15px;
                font-size: 16px;
                text-align: center;
                width: 100%;
            }
            .mainBody ul li.left01 a .title02{
                display: block;
                width: 100%;
                text-align: center;
                height: 25px;
                color: #b9b9b9;
                font-size: 12px;
            }
            .mainBody ul li.left01 a div span{
                font-size: 16px;
                float: left;
                margin-left: 5px;
            }

            .mainBody ul li.left01 a div b{
                background-color: red;
                color: #ffffff;
                width: 15px;
                height: 15px;
                line-height: 15px;
                margin-top: 3px;
                font-weight:  initial;
                font-size: 12px;
                display:  block;
                float:  left;
                border-radius: 50%;
            }
            .mainBody ul li.left01 a i{
                background-image: url(/static/meter/icon1.png);
                width: 90px;
                height: 90px;
                background-repeat: round;
                display: block;
            }


            .mainBody ul li.right01{
                height: 100px;
                background-color: #ffffff;
                border-bottom: 0.5px solid #CDCDCD;
            }
            .mainBody ul li.right01 a{
                margin-left: auto;
                margin-right: auto;
                margin-top: 30px;
                width:  160px;
                height: 60px;
                padding-left: 10px;
                display: block;
                margin-bottom: 30px;
            }
            .mainBody ul li.right01 a div.text{
                float: left;
                width: 80px;
                height: 60px;
            }
            .mainBody ul li.right01 a div.text h3{
            }
            .mainBody ul li.right01 a div.text span{
                color: #b9b9b9;
                font-size: 12px;
            }
            .mainBody ul li.right01 a div.icon{
                background-image: url('/static/meter/icon2.png');
                background-repeat: round;
                float: left;
                width: 45px;
                height: 45px;
                display: block;
            }
            .mainBody ul li.right02{
                background-color: #ffffff;
                height: 100px;
            }
            .mainBody ul li.right02 a{
                margin-bottom: 10px;
                padding-left:10px;
                margin-top: 30px;
                margin-left: auto;
                margin-right: auto;
                width:  160px;
                height: 40px;
                display: block;
            }
            .mainBody ul li.right02 a div.text{
                float: left;
                width: 80px;
                height: 40px;
            }
            .mainBody ul li.right02 a div.text h3{
            }
            .mainBody ul li.right02 a div.text span{
                color: #b9b9b9;
                font-size: 12px;
            }
            .mainBody ul li.right02 a div.icon{
                background-image: url('/static/meter/icon3.png');
                background-repeat: round;
                float: left;
                width: 40px;
                height:40px;
                display: block;
            }



        </style>
    </head>
<body class="bg-f1">

<section class="userHeader">

    <h3 class="name">业主:<?=$member->nickname;?></h3>
</section>
<section class="mainMenu">
    <div>
        <ul>
            <li class="new"><a href="/meter/history" class="mainMenuicon1">往期记录(<?=$MeCount;?>)</a></li>
            <li class="linet"></li>
            <li><a href="https://v.xiumi.us/board/v5/2DFCV/99472574" class="mainMenuicon2">抄表说明</a></li>
        </ul>
    </div>
</section>
<section>
    <div  class="mainBody">
        <ul>
            <li class="left01">
                <a href="/meter/device?type=0">
                    <div class="title01"><span>抄表任务</span>
                        <?php if($MeterCount>0){?>
                        <b><?=$MeterCount;?></b>
                        <?php }?>
                    </div>
                    <i></i>
                </a>
            </li>
            <li class="right01">
                <a href="/meter/device?type=1">
                    <div class="text">
                        <h3>抄电表</h3>
                        <span>共有<?=$MeterCount1?>台电表</span>
                    </div>
                    <div class="icon"></div>
                </a>
            </li>
            <li class="right02">
                <a href="/meter/device?type=2">
                    <div class="text">
                        <h3>抄水表</h3>
                        <span>共有<?=$MeterCount2?>台冷水表</span>
                    </div>
                    <div class="icon"></div>
                </a>
            </li>
        </ul>
    </div>
</section>
<script>
    $(function(){
        <?php if(!$hasHouse): ?>
            layer.open({
                content: "该功能仅对 <b><?= $projectName ?></b> 认证业主用户开放",
                btn: ['去认证', '首页'],
                shadeClose: false,
                yes: function(index){
                    location.href = "/auth";
                },
                no: function(index){
                    location.href = "/";
                }
            });

        <?php endif; ?>
    })
</script>
</body>
</html>
