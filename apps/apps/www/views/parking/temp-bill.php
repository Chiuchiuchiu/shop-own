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

<div id="parking-bill-s">
    <div class="panel parking-temp-b-b">
        <div class="plate-numbers">
            <p><?= $data['plateno'] ?></p>
        </div>
        <div class="bill-amount">
            实付：￥<?= number_format($data['payAmount'], 2) ?> 元
        </div>
        <div class="parking-times">

            <ul>
                <li>
                    <p>入场时间：</p>
                    <span><?= $data['entrytime'] ?></span>
                </li>
                <li>
                    <p>停车时长：</p>
                    <span><?= $data['parkingtimes'] ?></span>
                </li>
                <li>
                    <p>金额：</p>
                    <span style="color: #F8B500">￥<?= number_format($data['paidamt'], 2) ?>元</span>
                </li>
                <li>
                    <p>折扣：</p>
                    <span style="color: #F8B500">￥<?= number_format($data['discount'], 2) ?>元</span>
                </li>
                <li>
                    <p>停车场：</p>
                    <span><?= $data['parkingname'] ?></span>
                </li>
            </ul>

        </div>

        <?php \components\za\ActiveForm::begin(['id' => 'pay-form']) ?>
            <input type="hidden" name="plateno" value="<?= $data['plateno'] ?>">
            <input type="hidden" name="payType" value="<?= $payType ?>">
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
                if(res.code === 0){
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
                dataType: 'json',
                success: function (res){
                    if (res.code === 0) {
                        goPay(res.data.id);
                    } else {
                        app.hideLoading();
                        $('#shade-div').hide();
                        app.tips().error(res.message);
                        console.log(res);
                    }
                }
            });

            console.log(val);
        }

    });
    wx.ready(function (){
        wx.hideAllNonBaseMenuItem();
    });
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>
