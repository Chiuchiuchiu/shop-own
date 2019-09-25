<?php

/* @var $this yii\web\View */
/* @var $newDataProvider \yii\data\ActiveDataProvider */
?>
    <div class="panel" id="life-service-mobile">
        <form>
            <input type="hidden" name="_csrf" value="<?=\Yii::$app->request->csrfToken?>">
            <div class="pure-form">
                <div class="form-group">
                    <label>输入测试金额</label>
                    <input data-require type="text" name="amount" value="" placeholder="请输入测试金额">
                </div>
            </div>
            <div style="margin-top: 20px">
                <button class="btn btn-block btn-primary">充值</button>
            </div>
        </form>
    </div>
<?php \common\widgets\JavascriptBlock::begin();?>
    <script>
        $('#life-service-mobile').on('loaded',function(){
            $('form',this).bind('submit',function(){
                var d = $(this).serialize();
                var toPay = function (id) {
                    $.post('/swift-pass-pay/wx-js', {orderId: id}, function (res) {
                        if (res.code === 0) {
                            WeixinJSBridge.invoke(
                                'getBrandWCPayRequest',{
                                    "appId" : res.data.appId, //公众号名称，由商户传入
                                    "timeStamp": res.data.timeStamp, //戳，自1970 年以来的秒数
                                    "nonceStr" : res.data.nonceStr, //随机串
                                    "package" : res.data.package,
                                    "signType" : res.data.signType, //微信签名方式:
                                    "paySign" : res.data.paySign  //微信签名,
                                },
                                function(wxRes){
                                    if(wxRes.err_msg == "get_brand_wcpay_request:ok" ) {
                                        app.go('/order/success');  //'/order/pm-view?id=' + id
                                        return false;
                                    }
                                    if(wxRes.err_msg == "get_brand_wcpay_request:cancel"){
                                        app.tips().warning("支付取消");
                                        return false;
                                    }
                                    if(wxRes.err_msg == "get_brand_wcpay_request:fail"){
                                        app.tips().warning("支付失败");
                                        return false;
                                    }
                                }
                            );

                        } else {
                            app.tips().error("系统出错,请稍后再试");
                            console.error(res);
                        }
                    }, 'json')
                };
                $.post('/test/pay-demo-submit',d,function(res){
                    if(res.code==0){
                        toPay(res.data.id);
                    } else {
                        app.tips().error(res.message);
                    }
                },'json');

                return false;
            });
        })
    </script>
<?php \common\widgets\JavascriptBlock::end();?>