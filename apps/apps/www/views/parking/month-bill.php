<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/13
 * Time: 9:34
 */

/* @var $data array */
/* @var $payType string */

?>

<div id="parking-bill-s" class="panel">
    <div class="panel parking-temp-b-b">
        <div class="plate-numbers">
            <p><?= $data['plateno'] ?></p>
        </div>
        <div class="bill-amount">
            ￥<?= number_format($data['receivable'], 2) ?> 元
        </div>
        <div class="parking-times">

            <ul>
                <li>
                    <p>缴费数量：</p>
                    <span><?= $data['quantity'] ?>个月</span>
                </li>
                <li>
                    <p>当前卡最大使用日期：</p>
                    <span><?= $data['maxuseddate'] ?></span>
                </li>
                <li>
                    <p>缴费开始日期：</p>
                    <span><?= $data['effectdate'] ?></span>
                </li>
                <li>
                    <p>缴费结束日期：</p>
                    <span><?= $data['expiredate'] ?></span>
                </li>
            </ul>

        </div>

        <?php \components\za\ActiveForm::begin(['id' => 'pay-form']) ?>
            <input type="hidden" name="plateno" value="<?= $data['plateno'] ?>">
            <input type="hidden" name="payType" value="<?= 'm' ?>">
            <input type="hidden" name="quantity" value="<?= $data['quantity'] ?>">
            <input type="hidden" name="parkingid" value="<?= $data['parkingid'] ?>">
        <?php \components\za\ActiveForm::end() ?>

        <div class="btn-sub">
            <button class="btn btn-block btn-primary btn-pay-b">支付</button>
        </div>
    </div>

</div>

<div id="shade-div">
    <div class="mask"></div>
    <div class="c-panel">

    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>
<script type="text/javascript">
    $('.btn-pay-b').click(function (){
        var val = $('#pay-form').serialize();
        var goPay = function (orderId){
            $.post('/pay/wx-js-parking', {orderId: orderId}, function (res){
                app.hideLoading();
                $('#shade-div').hide();
                if(res.code == 0){
                    wx.chooseWXPay({
                        timestamp: res.data.timeStamp,
                        nonceStr: res.data.nonceStr,
                        package: res.data.package,
                        signType: res.data.signType,
                        paySign: res.data.paySign,
                        success: function (res) {
                            location.href = '/parking-order/order-view?id=' + orderId;
                            return;
                        },
                        cancel: function () {
                            app.tips().warning("支付取消");
                        }
                    });
                } else {
                    app.tips().warning(res.message);
                }
            }, 'json')
        };
        if(app.formValidate($('#pay-form'))){
            $('#shade-div').show();
            app.showLoading();

            $.ajax({
                type: 'POST',
                url: '/parking/bill-submit',
                data: val,
                success: function (res){
                    if (res.code === 0) {
                        goPay(res.data.id);
                    } else {
                        app.hideLoading();
                        $('#shade-div').hide();
                        app.tips().error(res.message);
                        console.log(res);
                    }
                },
                dataType: 'json'
            });

            console.log(val);
        }

    });
    wx.ready(function (){
        wx.hideAllNonBaseMenuItem();
    });
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>
