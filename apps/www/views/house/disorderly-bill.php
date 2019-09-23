<?php
/**
 *
 * @var \common\models\MemberHouse $model
 * @var \common\models\MemberPromotionCode $memberPromotion
 * @var \common\models\PmChristmasBillItem $pmChristmasBillItem
 * @var array $weChatJs
 * @var string $autoPay
 * @var string $billType
 * @var array $list
 * @var string $useNewPay
 */
$this->title = isset($model->house->project->house_name) ? $model->house->project->house_name : '财到家';
?>
<div class="panel full" id="house-bill">
    <div class="top-path">
        <table>
            <thead>
            <tr>
                <td>
                    <img src="<?= isset($model->house->project->icon) ? Yii::getAlias($model->house->project->icon) : '' ?>">
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <i></i>
                    <?= $model->house->showName ?>
                </td>
            </tr>
            <tr>
                <td style="padding: 9px;font-size: 12px;">
                    (<?= $model->house->ancestor_name ?>)
                </td>
            </tr>
            </tbody>

        </table>
    </div>
    <div class="bill-tips">
        您有以下费用需要缴（<?= Yii::$app->params['bill_tips'] ?>）
    </div>
    <div class="bill-list">
        <ul id="bill-list">

            <?php foreach ($list as $key => $item) {
                $dateLength = strlen($key);
                $date = strtotime($key);
                ?>
                <li class="open" data-year="<?= $key ?>">
                    <p>
                        <i></i>
                        <span><?= $key ?></span>
                        <small>费用账单</small>
                    </p>
                    <span>
                            <em></em>
                        <?= number_format($item['totalAmount'], 2) ?>
                        </span>
                    <ul style="display: block;">
                        <?php foreach ($item['list'] as $one) { ?>
                            <li data-item-id="<?= $one['ChargeItemID'] ?>" data-date="<?= $one['ShouldChargeDate'] ?>" data-id="<?= $one['ContractNo'] ?>" data-amount="<?= $one['BillAmount'] ?>">
                                <p>
                                    <small><?= $one['BillDate'] ?></small>
                                    <?= $one['ChargeItemName'] ?>
                                    <small>面积/用量：<?= isset($one['Amount']) ? $one['Amount'] : 0 ?></small>
                                    <small>上期读数：<?= $one['LastReadDegree'] ?></small>
                                    <small>本期读数：<?= $one['CurrentReadDegree'] ?></small>

                                    <?php if(isset($one['showBillFines'])){?>

                                        <small>本期欠费：<?= $one['currentAmountOf'] ?>元</small>
                                        <small>滞纳金：<?= $one['BillFines'] ?>元</small>

                                    <?php } ?>

                                </p>
                                <span><?= number_format($one['BillAmount'], 2) ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

        </ul>
    </div>

    <div class="button-fixed">
        <?php \components\za\ActiveForm::begin(['id' => 'bill-form']); ?>
        <input type="hidden" name="discountCoupon" value="<?= !empty($memberPromotion) ? $memberPromotion : 0?>" id="discountCoupon">
        <input type="hidden" name="allowRelief" id="allowRelief" value="<?= empty($pmChristmasBillItem) ? 'y' : 'f' ?>">
        <input type="hidden" name="bountyAmount" id="bountyAmount">
        <input type="hidden" name="useBountyAmount" id="useBountyAmount" value="">


        <input name="houseId" type="hidden" value="<?= $model->house_id ?>">
        <input id="contractNo" name="contractNo" data-required data-label="缴费账单" type="hidden" value="">
        <input type="hidden" name="billType" value="<?= isset($billType) ? $billType : 'all'?>">
        <span class="select-all">全选</span>

        <span id="bill-discounts">
            <span data-amount="" data-credit-amount="" id="received-amount">实付：￥0.00</span>
            <span class="del-amount"><small>原价￥</small><del class="sk-item-price-origin">0.00</del></span>
        </span>

        <button type="submit" id="submit-order" class="btn btn-primary btn-disable">立即缴费</button>
        <?php \components\za\ActiveForm::end(); ?>
    </div>
</div>

<div id="shade-div">
    <div class="mask"></div>
    <div class="c-panel">

    </div>
</div>

<?php
\common\widgets\JavascriptBlock::begin();
?>
<script>
    $('#house-bill').bind('loaded', function () {
        <?php if($NWerror): ?>
            app.tips().error("账单接口维护中，请稍后再试");
        <?php endif; ?>

        <?php if($model->status !== \common\models\MemberHouse::STATUS_ACTIVE){ ?>
        app.tips().warning('请先完成业主认证');
        setTimeout(function () {
            app.go('/auth')
        }, 0);
        return;
        <?php }?>
        var autoPay = <?=$autoPay == null ? 'false' : 'true'?>;
        $('#bill-form').bind('submit', function (e) {
            <?php /**
         * 支付方法
         */ ?>

            <?php if($useNewPay == 'w'){?>

            var toPay = function (id) {
                $.post('/pay/wx-js', {orderId: id}, function (res) {
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
                                $('#discountCoupon').val('');
                                $('#bountyAmount').val('');
                                $('#useBountyAmount').val('t');
                                $('.checked').remove();
                                $('#contractNo').val('');
                                $('#allowRelief').val('f');

                                app.go('/order/success');  //'/order/pm-view?id=' + id
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

            <?php } elseif($useNewPay == "s") {?>

            var toPay = function (id) {
                $.post('/swift-pass-pay/wx-js', {orderId: id}, function (res) {
                    app.hideLoading();
                    $('#shade-div').hide();
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

            <?php }elseif($useNewPay == 'm'){ ?>
                var toPay = function (id) {
                    $.post('/min-sheng/wx-js', {orderId: id}, function (res) {
                        console.log(res)return;
                        app.hideLoading();
                        $('#shade-div').hide();
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
            <?php } ?>

            var val = $(this).serialize();
            if (autoPay) {
                autoPay = false;
            } else {
                contractNo();
            }

            if (app.formValidate($(this))) {
                $('#shade-div').show();
                app.showLoading();
                $.post('/house/christmas-bill-submit', val, function (res) {
                    if (res.code === 0) {
                        toPay(res.data.id);
                    } else {
                        app.hideLoading();
                        $('#shade-div').hide();
                        app.tips().error(res.message);
                        if(res.data.goUrl){
                            app.go(res.data.goUrl);
                            return;
                        }
                        console.log(res);
                    }
                }, 'json')
            }
            return false;
        });

        var contractNo = function () {
            var v = [], a = 0, billDate = [], skOri=0, tempBill=0, msaSum=0, dataAmo=0;
            var isAll = true, chargeItemIdAmount = 0, managefee = 0;
            var msA = parseInt($('#discountCoupon').val());
            var htmlTips = '';
            var useBountyAmount = $('#useBountyAmount').val();
            var allowRelief = $('#allowRelief').val();
            var chargeItemIDs = [1, 4, 5, 14, 15, 16, 17, 18, 19, 21, 65, 121];

            $('#bill-list>li').each(function (i, row) {
                if ($(this).hasClass('checked')) {
                    $('li', this).each(function (i, item) {
                        v.push($(item).attr('data-id'));
                        billDate.push($(item).attr('data-date'));
                        a += parseFloat($(item).attr('data-amount'));
                        if($.inArray(parseInt($(item).attr('data-item-id')), chargeItemIDs) >= 0){
                            chargeItemIdAmount += parseFloat($(item).attr('data-amount'));
                        }
                    });
                } else {
                    isAll = false;
                }
            });

            $('.sk-item-price-origin').html(a.toFixed(2));


            /*if(allowRelief === 'y'){
                if(chargeItemIdAmount > 6000){
                    msaSum = 200;
                } else if(chargeItemIdAmount >= 5000 && chargeItemIdAmount < 6000){
                    msaSum = 150;
                } else if(chargeItemIdAmount >= 4000 && chargeItemIdAmount < 5000){
                    msaSum = 100;
                } else if(chargeItemIdAmount >= 3000 && chargeItemIdAmount < 4000){
                    msaSum = 80;
                } else if(chargeItemIdAmount >= 2000 && chargeItemIdAmount < 3000){
                    msaSum = 50;
                } else if(chargeItemIdAmount >= 1000 && chargeItemIdAmount < 2000){
                    msaSum = 20;
                } else if(chargeItemIdAmount > 0) {
                    msaSum = 5;
                }
            }*/

            /*console.log('物业服务费：' + chargeItemIdAmount.toFixed(2));
            console.log('优惠券额：' + msaSum);
            console.log('红包金额：' + msA);*/

            //如果物业服务费金额存在，并且奖励金大于 0，则计算优惠券额总数，否则将不计算优惠券额总数 删除&& msaSum > 0
            if(chargeItemIdAmount > 0){
                managefee = chargeItemIdAmount - (msA + msaSum);
                managefee = parseFloat(managefee).toFixed(2);
            }

            if(managefee > 0){
                tempBill = msA + msaSum;
            }

            /*console.log('减免后的金额'+managefee);
            console.log('临时存放金额：' + tempBill);*/

            skOri = a - tempBill;

            // $('#bountyAmount').val(msaSum);
            $('#received-amount').html('实付：￥' + skOri.toFixed(2));
            $('#received-amount').attr('data-amount', dataAmo.toFixed(2));


            $('#contractNo').val(v.join(','));

            if (managefee > 0) {
                if(msA > 0){
                    htmlTips += ' 优惠券：￥' + msA;
                }
                htmlTips += ' 奖励金：￥'+msaSum;
                $('.bill-tips').html('减免：'+ htmlTips);
            } else {
                $('.bill-tips').html('您有以下费用需要缴（在银行划扣期间不显示托收费用）');
            }

            if (a > 0) {
                $('#submit-order').removeClass('btn-disable')
            } else {
                $('#submit-order').addClass('btn-disable')
            }

            if (isAll) {
                $('.select-all').addClass('hover').html('取消全选')
            } else {
                $('.select-all').removeClass('hover').html('全选')
            }


        };
        contractNo();
        $('.select-all').on('click', function () {
            if ($(this).hasClass('hover')) {
                $('#bill-list li').removeClass('checked');
                $(this).removeClass('hover');
            } else {
                $('#bill-list li').addClass('checked');
                $(this).addClass('hover');

            }
            contractNo();
        });
        $('#bill-list').on('click', 'li p', function () {
            if ($(this).parent().hasClass('checked')) {
                $(this).parent().removeClass('checked');
            } else {
                $(this).parent().addClass('checked');
            }
            contractNo();
        });
        $('#bill-list').on('click', 'li>span', function () {
            var o = $(this).parent();
            if (o.hasClass('open')) {
                o.removeClass('open');
                $('ul', o).slideUp();
            } else {
                o.addClass('open');
                $('ul', o).slideDown();
            }
        });

    });
</script>
<?php
\common\widgets\JavascriptBlock::end();
?>
