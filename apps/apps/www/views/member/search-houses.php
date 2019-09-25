<?php
/**
 * @var array $houseList
 * @var integer $number
 * @var string $houseIds
 * @var string $customerName
 * @var string $memberPhone
 */
?>
<div class="panel" id="house-manager">
    <div class="banner"></div>

    <?php if(!empty($houseList)){?>
        <div class="house-list">
            <div class="pure-form">
                <label>我是</label>
                <div class="identity-choice">
                    <div data-val="1">
                        <div>业主</div>
                        <i></i>
                    </div>
                    <div data-val="2">
                        <div>租户</div>
                        <i></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">填写真实姓名（提交后无法修改） <b style="color: red;">*</b></label>
                    <input class="member-name" style="width: 100%;border-color: #F8B500;ime-mode:disabled" required type="text" placeholder="填写真实姓名" value="<?= $customerName ?>">
                </div>
            </div>
            <ul>
                <?php foreach ($houseList as $key => $row) { ?>
                    <li ">
                        <div>
                            <p><?= $row['houseName'] ?></p>
                        </div>
                        <div class="del"><?= $row['ex'] ?></div>
                    </li>
                <?php } ?>
            </ul>
            <?php \components\za\ActiveForm::begin(['id' => 'house-form', 'method' => 'POST']); ?>
                <input name="houseIds" class="houseIds" type="hidden" value="<?= $houseIds ?>">
                <input type="hidden" name="memberName" class="m-name" value="">
                <input type="hidden" name="identity" class="identity" value="1">
            <?php \components\za\ActiveForm::end(); ?>
        </div>

        <?php if($number > 0){?>
            <a style="margin-top:2em" class="btn btn-block btn-primary">添加新房产 <small><?= $number ?>套</small></a>
        <?php }?>

    <?php } else {?>
        <div class="empty-status"><i></i>您的手机号【<?= $memberPhone ?>】未在物业系统登记，未查询到相关房产</div>
        <div style="display: flex;">
            <a href="/house/" style="margin-top: 20px;" class="btn btn-block btn-disable">业主中心</a>
            <a href="/?" class="btn btn-block btn-primary">首页</a>
        </div>

    <?php }?>
</div>

<div id="warning-tips">
    <div class="mask"></div>
    <div class="warning-content-box">
        <div class="warning-content"></div>
        <div class="warning-back-box" style="display: none;">
            <a class="close-hidden">关闭</a>
            <a class="comf-step" data-id="">确定</a>
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin() ?>
    <script>
        $('#house-manager').on('loaded', function () {
            $('.btn-block').click(function (){
                var memberName = $('.member-name').val();
                memberName = memberName.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "");
                if(memberName.length < 1){
                    $('.warning-content').text('请填写真实姓名！');
                    $('.warning-back-box').hide();
                    $('#warning-tips').show().fadeOut(4000);
                    return;
                }

                $('.warning-content').text('请确认您的真实姓名【'+memberName+'】');
                $('.member-name').val(memberName);
                $('#warning-tips,.warning-back-box').show();

            });

            $('.close-hidden').click(function (){
                $('#warning-tips').hide();
            });

            $('.comf-step').on('click', function (){
                var memberName = $('.member-name').val();
                $('.m-name').val(memberName);
                var data = $('#house-form').serialize();
                $('.houseIds').val('');
                $.ajax({
                    type: "POST",
                    url: "/member/save-houses",
                    data: data,
                    beforeSend: function (){
                        $('.warning-content').html("正在提交，请不要关闭");
                        $('.warning-back-box').hide();
                    },
                    success: function (res){
                        if(res.code == 0){
                            if(res.data.goUrl){
                                $('.warning-content').html("添加成功，正在跳转房产中心……");
                                setTimeout(function () {
                                    location.href = res.data.goUrl;
                                }, 2000);
                            }
                        } else {
                            $('.warning-content').html(res.message);
                            $('#warning-tips').show().fadeOut(4000);
                        }
                    },
                    error: function (res){
                        app.tips().error('系统服务异常');
                    },
                    dataType: 'json'
                });


            });

            $('.identity-choice>div').on('tap click', function () {
                var val = $(this).attr('data-val');
                $('input[name=identity]').val(val);
                $(this).siblings().removeClass('hover');
                $(this).addClass('hover');
            });
            $('.identity-choice>div:eq(0)').click();
        });
    </script>
<?php \common\widgets\JavascriptBlock::end() ?>