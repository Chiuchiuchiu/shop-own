<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">

    <title>调查问卷</title>
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <script src="/static/js/jquery-2.1.1.min.js"></script>
    <script src="/static/js/layer/layer.js"></script>
    <script src="/static/js/layer/Common.js"></script>
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
        .wjx-stage-head {
            position: absolute;
            left: 31px;
            right: 31px;
            bottom: 0;
        }
        .wjx-stage-head a {
            position: relative;
            height: 45px;
            font-size: 16px;
            color: #b2b2b2;
            text-align: left;
            line-height: 2.2;
            border-bottom: 1px solid #dcdcdc;
        }
        .wjx-stage-head a.current {
            color: #1ea0fa;
            border-color: #1ea0fa;
        }
        dl {
            margin-top: 0;
            margin-bottom: 0px;
        }
        label {
            display: inline-block;
            margin-bottom: 5px;
            font-weight:inherit;
        }

        .pull-right {
            width: 60%;
            float: right !important;
        }
        .pull-left {
            width: 40%;
            text-align: center;
            float: left !important;
        }
        .changeData {background: #fff; margin-top: 8px;margin-bottom: 40px;}
        .changeData label {display: block; padding:12px 12px;border-bottom: 1px solid #ddd;position: relative;}
        .changeData .form { padding:20px;}
        .changeData .form-span { float: left;width: 70px;}
        .changeData .form-ctr {padding-left: 80px;}
        .changeData input,.changeData select {border: none;background: none;width: 100%;font-size: 14px;}
        .changeData .input-radio{
            width:21px;
        }
        .changeData .input-sms{
            width:60px;
        }

        .changeData .btn-code {position: absolute;top: 50%;right: 0; margin: -17px 10px 0 0 ;}
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
        .question-title{
            font-size: 18px;
            font-weight: bolder;
            letter-spacing: 1px;
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
<body style="background-color:#FFF;">
<section>
    <header class="layout-head">
        <div class="wjx-logo"></div>
        <dl class="wjx-stage-head">
            <a class="pull-left question-title" style="color: #4d4d4d;" href="#">业主问卷调研</a>
        </dl>
    </header>
</section>
<section class="changeData">
    <form name="personal" id="personal" method="get" action="/question/perfect">
        <div class="form">
            <label class="clearfix">
                <span class="form-span">物业单位</span>
                <div class="form-ctr">
                    <select name="house_id" id="house_id">
                        <?php foreach ($HouseArr as $v){?>
                        <option value="<?=$v['house_id']?>"  ><?=$v['ancestor_name']?></option>
                        <?php }?>
                    </select>
                </div>
            </label>
            <label class="clearfix">
                <span class="form-span">业主姓名</span>
                <div class="form-ctr"><input id="surname" name="surname" type="text" placeholder="请填写真实姓名" value=""></div>
            </label>

            <label class="clearfix">
                <span class="form-span">联系电话</span>
                <div class="form-ctr"><input id="telephone" name="telephone" type="text" placeholder="请填写手机号码" value="<?=$member->phone;?>"></div>
            </label>


            <div class="btn-submit">
                <input type="hidden" name="question_project_id" id="question_project_id" value="<?=$question_project_id;?>">
                <input type="hidden" name="butler_id" id="butler_id" value="<?=$butler_id;?>">
                <button class="btn-blue btn-blue-solid" type="button">开始答题</button>
            </div>
            <div class="agree">
                <span class="checked">本次调研项目由 <a href="http://51homemoney.com">中奥财富到家</a> 提供</span>
<!--                <a target="_blank" href="javascript:;"> 调查问卷服务条款</a>-->
            </div>
        </div>
    </form>
</section>
</body>
</html>
