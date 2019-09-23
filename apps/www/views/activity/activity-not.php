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

        .form .form-ctr{
            padding-left: 20px;
            padding-right: 20px;
            padding-top:15px;

        }
        .form .form-ctr input{
            font-size: 14px;
            border-radius: 8px;
            border: 0px solid #1ea0fa;
            height: 40px;
            line-height: 40px;
            width: 100%;
            padding: 10px;
        }

        .form .form-ctr textarea{
            font-size: 14px;
            border-radius: 8px;
            border: 0px solid #1ea0fa;
            line-height:30px;
            width: 100%;
            padding: 10px;
        }

        .form .btn-submit {
            width: 90%;
            height: 50px;
            font-size: 18px;
            margin: 40px auto 0;
            display: block;

        }
        .btn-blue-solid {
            color: #fff;
            border: 1px solid #1ea0fa;
            background-color: #1ea0fa;
            width: 100%;
            height: 43px;
            font-size: 18px;
            text-shadow: 1px 1px 1px #242f56;
            border-radius: 100px;
            margin-bottom: 33px;
            line-height: 43px;
        }
        .send-btn {
            width: 85px;
            height: 30px;
            font-size: 13px;
            color: #1ea0fa;
            padding: 0;
            line-height: 2.2;
            border: 1px solid #1ea0fa;
            border-radius: 2px;
            margin-left: 10px;
        }
        .checked{
            color: #ffffff;
            text-shadow: 1px 1px 1px #242f56;
        }
        .agree {
            font-size: 14px;
            color: #808080;
            line-height: 45px;
            text-align: center;
        }
        .agree a {
            background: transparent;
            text-decoration: none;
            color: #1ea0fa;
        }
        .activitynot{
            margin-top: 15px;
            margin-left: auto;
            margin-right: auto;
            width: 80%;
        }
        .activitynot img{
            width: 100%;
        }
    </style>
</head>
<body style="background-color:<?=$Items->bg_color;?>;">
<section class="header-top">
<div><img src="<?=Yii::getAlias($Items->pic);?>"></div>
</section>
<div class="activitynot">
   <img src="/static/images/welcome/activitynot.png">
</div>

</body>
</html>