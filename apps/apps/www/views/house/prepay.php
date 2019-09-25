<?php
/**
 * @var $house \common\models\MemberHouse
 * @var $houseList
 * @var $model \common\models\MemberHouse
 */
?>
<div class="panel" id="prepay">
    <div class="bill-tips">
        <?= $house->house->showName ?>
    </div>
    <div class="amount" data-amount="<?= $amount ?>">
        <label>每月物业费用预估</label>
        <p>¥ <span><?= number_format($amount * 6, 2, '.', '') ?></span></p>
    </div>

    <div class="pure-g choose">
        <div class="pure-u-11-24 c hover" data-num="6"><span>6</span>个月</div>
        <div class="pure-u-2-24"></div>
        <div class="pure-u-11-24 c" data-num="3"><span>3</span>个月</div>
        <div class="pure-u-11-24 c" data-num="9"><span>9</span>个月</div>
        <div class="pure-u-2-24"></div>
        <div class="pure-u-11-24 c" data-num="12"><span>12</span>个月</div>
    </div>
    <div class="agreement"><i class="hover"></i><span>我同意<a href="javascript:void(0)">《用户协议》</a>并预缴</span></div>
    <?php \components\za\ActiveForm::begin(['id' => 'payForm']); ?>
    <?php echo \components\za\Html::hiddenInput('num', 6, ['id' => 'inputNum']); ?>
    <button class="btn-bottom-all btn btn-primary">充值并预缴</button>
    <?php \components\za\ActiveForm::end(); ?>
    <div id="houseSelect">
        <h4>选择你要预缴的房产</h4>
        <ul>
            <?php foreach ($houseList as $model): ?>
                <li value="<?= $model->house->house_id ?>">
                    <a href="/house/prepay?id=<?= $model->house_id ?>" data-origin="1">
                        <?= $model->house->showName ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <button class="btn btn-primary btn-empty">关闭</button>
    </div>
    <div class="show-agreement">
            <iframe src=""></iframe>
            <button class="btn btn-primary">关闭</button>
    </div>
</div>
<?php \common\widgets\JavascriptBlock::begin() ?>
<script>
    $(function () {
        $('#prepay').on('loaded', function () {
            $('.choose>.c', this).on('click', function () {
                $(this).siblings().removeClass('hover');
                $(this).addClass('hover');
                var amount = $('.amount[data-amount]').attr('data-amount');
                var num = $(this).attr('data-num');
                $('.amount p span').html((parseFloat(amount) * parseInt(num)).toFixed(2));
                $('#inputNum').val(num);
            })
        });
        $('.agreement a').on('click', function () {
            $('.show-agreement').addClass('show');
            $('.show-agreement iframe').attr('src', '/default/agreement');

        });
        $('.show-agreement .btn').on('click', function () {
            $('.show-agreement').removeClass('show');
        })
        var toPay = function (id) {
            $.post('/pay/prepay-wx-js', {orderId: id}, function (res) {
                if (res.code === 0) {
                    wx.chooseWXPay({
                        timestamp: res.data.timeStamp,
                        nonceStr: res.data.nonceStr,
                        package: res.data.package,
                        signType: res.data.signType,
                        paySign: res.data.paySign,
                        success: function (res) {
                            app.go('/order/prepay-pm-view?id=' + id);
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
        $('.agreement i').on('click', function () {
            if ($(this).hasClass('hover')) {
                $(this).removeClass('hover');
            } else {
                $(this).addClass('hover');
            }
        })
        $('#payForm').on('submit', function () {
            if ($('.agreement .hover').length == 0) {
                app.tips().error("您必须同意《用户协议》");
                return false;
            }
            if (app.formValidate(this)) {
                app.showLoading();
                $.post('', $(this).serialize(), function (res) {
                    app.hideLoading();
                    toPay(res.data.id);
                }, 'json');
            }
            return false;
        })
        $('#prepay .bill-tips').on('click', function () {
            $('#houseSelect').show();
        })
        $('#houseSelect .btn').on('click', function () {
            $('#houseSelect').hide();
        })
    })
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>
