<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">

    <title><?=$Items->title;?>活动报名</title>
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <script src="/static/js/jquery-2.1.1.min.js"></script>
    <script src="/static/js/layer/layer.js"></script>
    <script src="/static/js/layer/Common.js"></script>

    <script type="application/javascript">
        $(function(){
            $('.btn-submit').on('click',function(){
                var _form = $('form');
                $.ajax({
                    type: 'GET',
                    url: _form.attr('action'),
                    timeout: 3000, //超时时间：30秒
                    data: _form.serialize(),
                    dataType:'json',
                    success: function (data) {
                        if(data.code=='0'){

                            layer.msg(data.msg, {time:2000});
                           setTimeout(function() {location.href = data.url;},2000);
                        }else{
                            layer.msg(data.msg, {time:3000});
                        }
                    },
                    error: function (data) {
                      //  timeoutChoose();
                    }
                });
            });
           function  timeoutChoose() {
               var _form = $('form');
               $.ajax({
                   type: 'POST',
                   url: _form.attr('action'),
                   timeout: 3000, //超时时间：30秒
                   data: _form.serialize(),
                   dataType:'json',
                   success: function (data) {
                       if(data.code=='0'){

                           layer.msg(data.msg, {time:2000});
                           setTimeout(function() {location.href = data.url;},2000);
                       }else{
                           layer.msg(data.msg, {time:3000});
                       }
                   },
                   error: function (data) {
                       timeoutChoose();
                   }
               });

           }
        });

    </script>
    <style type="text/css">
        body{
            max-width:640px;
            margin-left:auto;
            margin-right:auto;
        }
        .layout-head {
            position: relative;
            height: 186px;
            background-image: url(/static/images/welcome/bg.png);
            background-repeat: no-repeat;
            background-size: cover;
        }
        .header-top{
        }
        .header-top div img{
            width: 100%;
        }
        .agreeH3{
            text-align: center;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
            font-size: 35px;
            margin-top: 40px;
            color: #f96938;
            text-shadow: 1px 1px 1px #ffffff;
            font-family: 'Heiti SC Light', 'Microsoft YaHei', Gadget, sans-serif;
            font-weight: bold;
        }


        .agreeboy {
            width: 82%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 15px;
            font-size: 14px;
            margin-bottom: 25px;
            color: #ffffff;
            text-shadow: 1px 1px 1px #242f56;
            line-height: 210%;

        }
        .agreeFooter{
            margin-left: auto;
            margin-right: auto;
            width: 82%;
        }
        .agreeFooter a{
            width: 100%;
            display: block;
            color: #fff;
            line-height: 35px;
            height: 35px;
            text-align: center;
            border: 1px solid #4083b1;
            background-color: #4083b1;
            text-shadow: 1px 1px 1px #242f56;
            border-radius: 15px;
        }

    </style>
</head>
<body style="background-color:<?=$Items->bg_color;?>;">
<section class="header-top">
    <div><img src="<?=Yii::getAlias($Items->pic);?>"></div>
</section>
<section>
    <div class="agreeH3">恭喜～登记成功!</div>
    <div class="agreeboy">关于本次活动的最新通知会在我们的订阅号第一时间展示，请您随时关注，谢谢！</div>
    <div class="agreeFooter" >
        <a class="btn-blue btn-blue-solid"  style="border: 1px solid <?=$Items->btn_color;?>;background-color: <?=$Items->btn_color;?>;" href="/">返回业主中心</a>
    </div>
</section>
</body>
</html>