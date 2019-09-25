
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>活动评价</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <style>

        .header{ height: 45px;}
        .headerFixed {width: 100%; height: 45px;  position:fixed;top: 0;left: 0;background:#fff;z-index:999;box-shadow: 0 1px 2px rgba(0,0,0,0.2)}
          .header .Changbnt.Chart {background-position:0 -21px;}
        .header h1{ font-size: 16px; font-weight: normal; width:100%;line-height:45px; text-align:center; color: #666; position: relative;z-index: 8; }
        .header h1 span { position: relative; margin: 0 10px 0 2px}
        .header h1 .jt-s {width: 12px;position: absolute; top: 9px; left: 0;}
        .headerFixed.on h1 .jt-s {transform: rotate(0deg);-webkit-transform: rotate(0deg);}
        .header .TcartNum span { width:12px; height:12px; line-height:12px; text-align:center; position:absolute; top:-5px; right:-7px; font-size:10px; color:#fff; background:#e5006e;
            -moz-border-radius: 100%;
            -webkit-border-radius:100%;
            border-radius: 100%;
        }
        .header .topLeft { display:block; width:45px; height:45px; position:absolute;left:0; top:0;z-index: 10;background: url(/static/images/back.png) no-repeat center center;background-size: 12px auto}
        .header .topRight { display:block; width:45px; height:45px; position:absolute;right:0; top:0;z-index: 10; }
        .header .topLeft:active,.header .topRight:active { background: #01b492;}
        .header .topLeft img,.header .topRight img { width: 23px; margin: 8px auto 0 auto; display: block;}
        .icon-home {
            background: url(/static/images/home.png) no-repeat center center;
            background-size: 22px auto;
        }
        .header-blue .headerFixed {background-color: #0098ec}
        .header-blue .headerFixed h1 {color: #fff;text-align: left;padding: 0 12px;}
        .con-section{
            background: #fff;
            z-index: 999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border-radius: 5px;
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .con-box-text{
            width:80%;
            margin-left: auto;
            margin-right: auto;
            padding-bottom: 35px;
        }
        .con-logo{
            text-align: center;
        }
        .con-logo img{
            width: 45%;
            margin-top: 25px;
        }

        .con-booy{
            margin-left: 15px;
            margin-right: 15px;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .con-title{
            font-size: 18px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            margin-bottom: 15px;
        }
        .con-Text{
            font-size: 14px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            margin-bottom: 15px;
            color: #fd7716;
        }
        .con-site{
            height: 1px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .con-site span{
            position: relative;
            top: -12px;
            background: #fff;
            padding: 0 20px;
        }
        .con-div{
            line-height: 35px;
            height: 35px;
            text-align: center;
        }
        .con-comment{
            line-height:30px;
            margin-bottom: 30px;
        }
        .con-sub{
            width: 100%;
        }
        .comment{
            padding-top: 30px;
            padding-bottom: 15px;
        }
        .CommentTag ul{
            line-style:none;
            margin: 0px;
        }
        .CommentTag ul li{
            float: left;
            width: 49%;
        }
        .CommentTag ul li a{
            border: 1px solid #ddd;
            border-radius: 20px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 5px;
            padding-bottom: 5px;
            line-height: 45px;
            height: 45px;
        }

        .CommentTag ul li.on a{
            border: 1px solid #fd7716;
            border-radius: 20px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 5px;
            padding-bottom: 5px;
            line-height: 45px;
            height: 45px;
            color: #fd7716;
        }

        .con-textarea{
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .con-textarea textarea{
            width: 100%;
            border-radius:5px;
            border: 1px solid #ddd;
            padding: 7px;
            margin-top: 10px;
        }
        .con-sub a{
            width: 100%;
            border-radius: 10px;
            border: 0px;
            background-color: #fd7716;
            font-size: 16px;
            color: #ffffff;
            height: 35px;
            line-height: 35px;
            display: block;
            text-align: center;
        }
        /* star */
        #star{
            position:relative;
            width:160px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 10px;
            height:24px;}
        #star ul,#star span{float:left;display:inline;height:24px;line-height:24px;}
        #star ul{margin:0 10px;}
        #star li{float:left;width:28px;cursor:pointer;text-indent:-9999px;background:url(/static/images/star.png) no-repeat;}
        #star li.on{background-position:0 -28px;}

    </style>

</head>
<body class="bg-f1">
<header class="header">
    <div class="headerFixed">
        <a href="/" class="topLeft"></a>
        <!-- <h1>购物车</h1> -->
        <!-- 标题 -->
        <h1><?=$Activity->title;?></h1>

        <a href="/" class="topRight icon-home"></a>
    </div>
</header>

<section class="con-section">
    <form name="personal" id="personal" method="POST" action="/activity/comment-save">
    <div  class="con-box-text">
        <div class="con-logo"><img src="/static/images/logo.png"></div>
        <div class="con-title">您已经评价过啦
        <input type="hidden" id="starNumber" name="starNumber" value="5">
        <input type="hidden" id="sign_up_id" name="sign_up_id" value="<?=$SignUp->id?>">
        </div>
        <div class="con-site"><span>感谢您的参与</span></div>
        <div class="comment">
            <div id="star">
                <ul>

                    <?php
                    for ($x=1; $x<=5; $x++) {
                        if($x>$SignUp->star_number){
                            echo '<li><a href="javascript:;">'.$x.'</a></li>';
                        }else{
                            echo '<li class="on"><a href="javascript:;">'.$x.'</a></li>';
                        }
                    }
                    ?>

                </ul>
            </div>
        </div>
        <div class="con-Text" id="CommentText"><?php
            if($SignUp->star_number==5){
                echo '非常满意';
            }elseif($SignUp->star_number==4){
                echo '满意';
            }elseif($SignUp->star_number==3){
                echo '一般';
            }elseif($SignUp->star_number==2){
                echo '不满意';
            }elseif($SignUp->star_number==1) {
                echo '非常不满意';
            }?></div>

        <div class="con-comment"><?=$SignUp->comment;?></div>


        <div class="con-sub"><a href="/" class="btn-submit">返回业主中心</button></div>
    </div>
    </div>
    </form>
</section>

</body>
</html>