<?php

/* @var $this yii\web\View */
/* @var $newDataProvider \yii\data\ActiveDataProvider */
?>
<div class="panel" id="life-service-mobile">
    <form>
        <input type="hidden" name="_csrf" value="<?=\Yii::$app->request->csrfToken?>">
        <div class="pure-form">
            <div class="form-group">
                <label>输入您的手机号</label>
                <input data-require type="text" name="mobile" value="" placeholder="请输入手机号码">
            </div>
        </div>
        <div class="an">
            <input name="amount" type="hidden" id="amount" value="50">
            <div class="flex">
                <div data-amount="10">
                    <span>10元</span>
                </div>
                <div data-amount="20">
                    <span>20元</span>
                </div>
                <div data-amount="30">
                    <span>30元</span>
                </div>
            </div>
            <div class="flex">
                <div data-amount="50" class="active">
                    <span>50元</span>
                </div>
                <div data-amount="100">
                    <span>100元</span>
                </div>
                <div data-amount="300">
                    <span>300元</span>
                </div>
            </div>
        </div>
        <div style="margin-top: 20px">
            <button class="btn btn-block btn-primary">充值</button>
        </div>
    </form>
</div>

    <div id="shade-div">
        <div class="mask"></div>
        <div class="c-panel">

        </div>
    </div>

<?php \common\widgets\JavascriptBlock::begin();?>
<script>
    $('#life-service-mobile').on('loaded',function(){
        $('.an .flex>div',this).bind('click',function(){
            $('.an .flex>div').removeClass('active');
            $(this).addClass('active');
            $('#amount').val($(this).attr('data-amount'))
        });
        $('form',this).bind('submit',function(){
            var d = $(this).serialize();
            if(!/1[0-9]{10}/.test($('[name="mobile"]').val())){
                app.tips().error('请输入正确手机号');
                return false;
            }
            var toPay = function (id) {
                $.post('/pay/wx-js-mobile', {orderId: id}, function (res) {
                    app.hideLoading();
                    $('#shade-div').hide();
                    if (res.code === 0) {
                        wx.chooseWXPay({
                            timestamp: res.data.timeStamp,
                            nonceStr: res.data.nonceStr,
                            package: res.data.package,
                            signType: res.data.signType,
                            paySign: res.data.paySign,
                            success: function (res) {
                                alert("充值成功，请稍后留意到账！");
                                app.go('/');
                            },
                            cancel: function () {
                                app.tips().warning("支付取消");
                            }
                        });
                    } else {
                        app.tips().error("系统出错,请稍后再试");
                        console.error(res);
                    }
                }, 'json')
            };

            $.ajax({
                type: "POST",
                url: '/life-service/mobile',
                data: d,
                beforeSend: function(){
                    $('#shade-div').show();
                    app.showLoading();
                },
                success: function(res){
                    if(res.code==0)
                        toPay(res.data.id);
                    else{
                        // alert(res.message);
                        app.hideLoading();
                        $('#shade-div').hide();
                        app.tips().error(res.message);
                    }
                },
                error: function(){
                    app.hideLoading();
                    $('#shade-div').hide();
                    app.tips().error(res.message);
                },
                dataType: 'json'
            });


            return false;
        });
    })
</script>
<?php \common\widgets\JavascriptBlock::end();?>