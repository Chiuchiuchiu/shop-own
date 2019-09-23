<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>中秋答题送红包</title>
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <script src="/static/js/jquery-2.1.1.min.js"></script>
    <script src="/static/js/layer/layer.js"></script>
    <style type="text/css">
        body {
            margin-top: 0px;
            margin-bottom: 0px;
            background-color:#AE0026;
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

        .subject{
            margin:10px;
            display: none;
        }
        .subject-title {
            color: #FFD150;
            letter-spacing: 2px;
            font-size: 26px;
        }
        i.radio{
            background-image: url(/static/images/icon_radio.png);
            width: 30px;
            height: 30px;
            display: inline-block;
            position: relative;
            background-size: 220%;
            background-repeat: no-repeat;
            top: 3px;
        }
        i.radio.active{
            background-position: 100%;

        }
        i.checkbox{
            background-image: url(/static/images/icon_checkbox.png);
            width: 30px;
            height: 30px;
            display: inline-block;
            position: relative;
            background-size: 220%;
            background-repeat: no-repeat;
            top: 3px;
        }
        .subject-answer-container > div {
            padding: 12px 12px 0 20px;
            color: #FFFFFF;
            font-size: 20px;
            letter-spacing: 3px;
        }
        .step-container {
            width: 100%;
            bottom: 0;
            z-index: 20;
            opacity: 1;
            line-height:80px;
            position:absolute;
            text-align: center;
        }
        .prev {
            width: 40%;
            height: 40px;
            border: none;
            background-color: #FFFFFF;
            color: #FFD150;
            font-size: 16px;
            border-radius: 12px;
            margin: 0 10px;
        }
        .next {
            width: 40%;
            height: 40px;
            border: none;
            background-color: #FFD150;
            color: white;
            font-size: 16px;
            border-radius: 12px;
            margin: 0 10px;
        }
        .back {
            width: 50%;
            height: 40px;
            border: none;
            background-color: #FFD150;
            color: white;
            font-size: 16px;
            border-radius: 12px;
            margin: 0 10px;
        }
        .submit-form {
            width: 40%;
            height: 40px;
            border: none;
            background-color: #FFD150;
            color: white;
            font-size: 16px;
            border-radius: 12px;
            margin: 0 10px;
        }
        .qr-div {
            text-align: center;width: 100%;height: 100%;
        }
        .qr-div img {
            width: 50%;height: 50%
        }
    </style>
</head>
<body>
<div class="bg-head">
    <img src="/static/images/welcome/min-autumn-head.png">
</div>
<div style="margin-top: 10%">
    <div class="qr-div" style="display: none;">
        <img src="<?= $qrCode; ?>">
        <div class="subject-title" style="margin: 8% 0px">
            <b>更多优惠请关注</b><br/>
            <button class="back" onclick="location.href='/'" style="margin: 8% 0px">返回首页</button>
        </div>
    </div>
    <div class="form-div">
        <form id="Personal" name="Personal" action="/min-autumn/qa-save" method="post">
            <?php
            $i=0;
            foreach($list as $v){
                $i++;
            ?>
                <div id="subjectItem<?=$i;?>" class="subject" <?php if($i <= 1){ echo ' style="display: block;"';}?>>
                    <div class="subject-title"><b><?=$i;?>、<?=$v->title;?></b>
                        <input type="hidden" id="SubjectID" name="SubjectID[]" value="<?=$v->id?>">
                        <input type="hidden" id="subject_text<?=$v->id;?>" name="subject_text<?=$v->id;?>" value="">
                    </div>
                    <div class="subject-answer-container">
                        <?php foreach (json_decode($v->answer, true) as $key => $val){ ?>
                            <div dataid="<?= $v->id;?>"><i class="radio" index="<?= $key ?>"></i> <b><?= $val ?></b> </div>
                        <?php } ?>
                    </div>
                </div>
            <?php }
            ?>

            <input type="hidden" id="QuestionSubmit" value="1">
            <input type="hidden" name="house_id" value="<?= $houseId ?>">
            <input type="hidden" id="TotalCount" value="<?= $totalCount ?>">

        </form>


        <div class="step-container resizeFix">
            <button type="button" class="prev" onclick="Myprev()"><b>上一页</b></button>
            <button type="button" class="next" onclick="Mynext()"><b>下一页</b></button>
            <button class="submit-form" style="display: none;" type="button"><b>提交</b></button>
        </div>
    </div>
</div>

</body>
<script>
    $(function(){
        $('.submit-form').on('click',function(){
            var _form = $('form');

            //loading
            var ii = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });

            $.ajax({
                type: 'GET',
                dataType:"json",
                url: _form.attr('action'),
                data: _form.serialize(),
                timeout: 3000, //超时时间：30秒
                success: function (res) {

                    layer.close(ii);

                    if(res.code == 0){
                        $('.form-div').hide();
                        $('.qr-div').show();
                    }

                    layer.msg(res.message, {time:5000});
                }
            });
        });

        $('i.radio').parent().on('click',function(){
            var self = $(this);
            var dataid = self.attr('dataid');

            self.parent().find('i.radio').removeClass('active');
            self.find('i.radio').addClass('active');
            var val = self.find('i.radio').attr('index');
            $('#subject_text'+dataid).val(val);

            var TotalI =0;
            var subjectI =0;
            $('input[name="SubjectID[]"]').each(function(){
                TotalI++;
                var subjectId =  $("#subject_text"+$(this).val()).val();
                if(subjectId!==''){
                    subjectI++;
                }
            });

        });
    });

    var total = $("#TotalCount").val();

    //上一页按钮 zhaowenxi
    function Myprev() {

        var questionNumber = parseInt($("#QuestionSubmit").val());

        if(questionNumber > 1){

            questionNumber--;

            $(".subject").hide();

            $("#subjectItem"+questionNumber).show();

            $("#QuestionSubmit").val(questionNumber);

            $('.submit-form').hide();

            $('.next').show();
        }
    }

    //下一页按钮 zhaowenxi
    function Mynext(){

        var questionNumber = parseInt($("#QuestionSubmit").val());

        if(total > questionNumber){

            questionNumber++;

            $(".subject").hide();

            $("#subjectItem" + questionNumber).show();

            $("#QuestionSubmit").val(questionNumber);
        }

        if(total == questionNumber){

            $('.submit-form').show();
            $('.next').hide();

        }
    }
</script>

</html>