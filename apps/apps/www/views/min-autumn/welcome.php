<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">

    <title>中秋</title>
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <script src="/static/js/jquery-2.1.1.min.js"></script>
    <script src="/static/js/layer/layer.js"></script>
    <script src="/static/js/layer/Common.js"></script>
    <style type="text/css">
        body{
            max-width:100%;
            margin-left:auto;
            margin-right:auto;
            background-color: #AE0026;
        }
        .bg-head{
            width: 100%;
            padding-bottom:60%;
        }
        .bg-head img{
            position:absolute;
            top:0;
            width:100%;
            margin:auto;

        }
        .changeData {margin-top: 8px;margin-bottom: 40px;}
        .changeData label {display: block; padding:12px 12px;border-bottom: 1px solid #ddd;position: relative;}
        .changeData .form { padding:20px;}
        .changeData .form-span { float: left;width: 70px;color: #FFD150;letter-spacing: 1.5px;}
        .changeData .form-ctr {padding-left: 80px;}
        .changeData input,.changeData select {color: #FFFFFF;border: none;background: none;width: 100%;font-size: 14px;}
        ::-webkit-input-placeholder {
            color:    #FFFFFF;
        }
        .form .btn-submit {
            width: 90%;
            height: 50px;
            font-size: 18px;
            margin: 40px auto 0;
            display: block;

        }
        .btn-blue-solid {
            color: #AE0026;
            border: 1px solid #FFD150;
            background-color: #FFD150;
            width: 100%;
            height: 43px;
            font-size: 18px;
            border-radius: 100px;
            margin-bottom: 33px;
            line-height: 43px;
        }
        .agree {
            font-size: 14px;
            color: #FFFFFF;
            line-height: 45px;
            text-align: center;
        }
        .agree a {
            background: transparent;
            text-decoration: none;
            color: #1ea0fa;
            letter-spacing: 1px;
        }
        .agree a > b {
            color: #FFD150;

        }
    </style>
    <script type="application/javascript">
        $(function(){
            $('.btn-submit').on('click',function(){

                //loading
                var ii = layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });

                var _form = $('form');

                var serialize = _form.serialize();

                $.ajax({
                    type: 'POST',
                    url: _form.attr('action'),
                    timeout: 3000, //超时时间：30秒
                    data: serialize,
                    dataType:'json',
                    success: function (data) {
                        if(data.code=='0'){

                            layer.msg(data.message, {time:2000});
                            setTimeout(function() {location.href = data.url;},2000);
                        }else{
                            layer.close(ii);
                            layer.msg(data.message, {time:3000});
                        }
                    },
                    error: function (data) {
                        layer.close(ii);
                        layer.msg('提交超时，请重新提交', {time:2000});
                    }
                });
            });
        });
    </script>
</head>
<body>
<div class="bg-head">
    <img src="/static/images/welcome/min-autumn-head.png">
</div>
<section class="changeData">
    <form name="personal" id="personal" method="get" action="/min-autumn/perfect">
        <div class="form">
            <label class="clearfix">
                <span class="form-span"><b>住宅地址</b></span>
                <div class="form-ctr">
                    <select name="house_id" id="house_id">
                        <?php foreach ($HouseArr as $v){?>
                        <option value="<?=$v['house_id']?>"  ><?=$v['ancestor_name']?></option>
                        <?php }?>
                    </select>
                </div>
            </label>
            <label class="clearfix">
                <span class="form-span"><b>业主姓名</b></span>
                <div class="form-ctr"><input id="surname" name="surname" type="text" placeholder="请填写真实姓名" value=""></div>
            </label>

            <label class="clearfix">
                <span class="form-span"><b>联系电话</b></span>
                <div class="form-ctr"><input id="telephone" name="telephone" type="text" placeholder="请填写手机号码" value="<?=$member->phone;?>"></div>
            </label>


            <div class="btn-submit">
                <button class="btn-blue btn-blue-solid" type="button"><b>开 始 答 题</b></button>
            </div>
            <div class="agree">
                <span class="checked">本次活动由 <a href="http://51homemoney.com"><b>中奥财到家</b></a> 提供</span>
            </div>
        </div>
    </form>
</section>
</body>
</html>
