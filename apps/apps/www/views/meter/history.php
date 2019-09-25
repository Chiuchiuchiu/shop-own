<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>往期记录</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <script src="/static/js/jquery-2.1.1.min.js"></script>
    <script src="/static/js/layer/layer.js"></script>
    <script src="/static/js/layer/Common.js"></script>
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <style type="text/css">
        body {
            font-family: 'Heiti SC Light','Microsoft YaHei', Gadget, sans-serif;
            font-size: 14px;
            line-height: 1.42857143;
            color: #4d4d4d;
        }
        .red {
            color: #ff2d2d;
        }
        .header {
            height: 45px;
        }
        .header-blue .headerFixed {background-color: #0098ec}
        .header-blue .headerFixed h1 {color: #fff;text-align: left;padding: 0 12px;}
        .headerFixed {width: 100%; height: 45px;  position:fixed;top: 0;left: 0;background:#fff;z-index:999;box-shadow: 0 1px 2px rgba(0,0,0,0.2)}
        .top-nav {text-align: center;}
        .top-nav a {display: inline-block;padding: 0 0px;line-height: 44px;}
        .top-nav a.on {border-bottom: 1px solid #ff9800;color: #ff9800;}
        .dis-tab {
            display: table;
            width: 100%;
        }
        .text-right {
            text-align: right;
            float: right;
        }
        .signlist li {margin-top: 10px;
            margin-left: 10px;
            margin-right: 10px;
            background-color: #fff;border-top: 1px solid #E2E2E2;border-bottom: 1px solid #eee;}

        .signlist li a {display: block; padding: 0 10px; }
        .signlist .s-head { padding: 10px 0;border-bottom: 1px solid #eee; }
        .signlist .s-body { padding: 10px 0;}
        .signlist li .detail { padding-left:0px; }
        .signlist li .detail p.vuew { color:#333; }

        .signlist li h3 { margin-bottom: 2px;color: #666;font-size: 14px; height: 25px; line-height: 25px; overflow: hidden;}
        .signlist li h3.only {line-height: 25px;font-size: 16px;}
        .signlist li p{ color: #666}
        .signlist li span.fr{ color: #bbb}
        .s-footer{  padding: 10px 0px;
            margin-left: 10px;
            margin-right: 10px;
            height: 40px;
            border-top: 1px solid #eee;
        }
        .s-footer p{ float: right; text-align: center;}
        .s-footer p.vuedate{
            width:150px; float: left;
        }
        .s-footer p a{
            font-size: 14px;
            color: #ffffff;
            margin-left: 5px;
        }
        .gray-6 {
            color: #ff9800;
        }
        #QrcodeDiv{
            display: none;
        }
        .bg-f1{
            background-color: #f1f1f5;
        }

    </style>

</head>
<body class="bg-f1">
<header class="header">
    <div class="headerFixed">
        <ul class="top-nav dis-tab">
            <li class="dis-cell"><a href="?type=0"<?php if($type==0){?> class="on" <?php }?>>全部(<?=$MeterCount1;?>)</a></li>
                <li class="dis-cell"><a href="?type=1"<?php if($type==1){?> class="on" <?php }?>>电表(<?=$MeterCount2;?>)</a></li>
                <li class="dis-cell"><a href="?type=2"<?php if($type==2){?> class="on" <?php }?>>冷水表(<?=$MeterCount3;?>)</a></li>

        </ul>
    </div>
</header>

<article class="signlist">
    <ul>
        <?php foreach ($MeterList as $Lib) { ?>
            <li>
                <a>
                <div class="dis-tab s-head">
                    <span class="dis-cell gray-6">设备类型：<?=$Lib->meter_type;?></span>
                    <span class="dis-cell text-right red">状态：
                        <?php if($Lib->status==4){
                            echo '已上报';
                        }?>
                    </span>
                </div>
                <div class="s-body clearfix">
                    <div class="detail">
                        <p class="name">设备编号<?=$Lib->meter_id;?></p>
                        <p class="name">抄表时间:<?=date('Y-m-d',$Lib->meter_time);?></p>
                        <p class="name">抄表读数:<?=$Lib->meter_data;?></p>
                    </div>
                </div></a>

            </li>
        <?php }?>
    </ul>
</article>
</body>
</html>

