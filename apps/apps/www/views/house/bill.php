<?php
/**
 *
 * @var \common\models\MemberHouse $model
 * @var array $weChatJs
 * @var string $autoPay
 * @var string $billType
 * @var array $list
 * @var string $useNewPay
 */
$this->title = isset($model->house->project->house_name) ? $model->house->project->house_name : '到家科技';
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

            <?php if(\common\models\SysSwitch::getValue('testMember') && Yii::$app->params['test_show_bill']){?>

                <li class="checked">
                    <p>
                        <i></i>
                        02月<span>2017</span>
                        <small>费用账单</small>
                    </p>
                    <span>
                        <em></em>
                        <?= number_format(Yii::$app->params['test_member_amount'], 2) ?>
                    </span>
                    <ul>
                        <li data-id="45-33507028-1" data-amount="<?= number_format(Yii::$app->params['test_member_amount'], 2) ?>">
                            <p>
                                <small>20170201-20170228</small>
                                梯灯公摊费
                                <small>面积/用量：107.5700</small>
                            </p>
                            <span><?= number_format(Yii::$app->params['test_member_amount'], 2) ?></span>
                        </li>
                    </ul>
                </li>

                <?php } else {?>

                <?php foreach ($list as $key => $item) {
                    $dateLength = strlen($key);
                    $date = strtotime($key);
                    ?>
                    <li class="<?= $date < time() ? 'checked' : '' ?> open">
                        <p>
                            <i></i>
                            <?= $dateLength == 4 ? '' : date('m月', $date) ?><span><?= $dateLength == 4 ? $key : date('Y', $date) ?></span>
                            <small>费用账单</small>
                        </p>
                        <span>
                            <em></em>
                                <?= number_format($item['totalAmount'], 2) ?>
                        </span>
                        <ul style="display: block;">
                            <?php foreach ($item['list'] as $one) { ?>
                                <li data-id="<?= $one['ContractNo'] ?>" data-amount="<?= $one['BillAmount'] ?>">
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

            <?php }?>

        </ul>
    </div>
    <div class="button-fixed">
        <?php \components\za\ActiveForm::begin(['id' => 'bill-form']); ?>
        <input name="houseId" type="hidden" value="<?= $model->house_id ?>">
        <input id="contractNo" name="contractNo" data-required data-label="缴费账单" type="hidden" value="">
        <input type="hidden" name="billType" value="<?= isset($billType) ? $billType : 'all'?>">
        <span class="select-all">全选</span>
        <span id="total-amount">0.00</span>
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
                                $('.checked').remove();
                                $('#contractNo').val('');
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

            <?php } elseif($useNewPay == 's') {?>

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
            var orderby = 1;
            if (autoPay) {
                autoPay = false;
            } else
                contractNo();
            $('#bill-list>li').each(function (i, e) {
                if (orderby > 1 && $(e).hasClass('checked')) {
                    orderby = 3;
                }
                if (orderby < 3 && !$(e).hasClass('checked')) {
                    orderby = 2;
                }
            });
            if (orderby == 3) {
                app.tips().error("请您按账单顺序缴款");
                return false;
            }
            if (app.formValidate($(this))) {
                $('#shade-div').show();
                app.showLoading();
                $.post('/house/bill-submit', val, function (res) {
                    if (res.code === 0) {
                        toPay(res.data.id);
                    } else {
                        app.hideLoading();
                        $('#shade-div').hide();
                        app.tips().error(res.message);
                        console.log(res);
                    }
                }, 'json')
            }
            return false;
        });
        var contractNo = function () {
            var v = [], a = 0;
            var isAll = true;
            $('#bill-list>li').each(function (i, row) {
                if ($(this).hasClass('checked')) {
                    $('li', this).each(function (i, item) {
                        v.push($(item).attr('data-id'));
                        a += parseFloat($(item).attr('data-amount'));
                    })
                } else {
                    isAll = false;
                }
            });
            $('#total-amount').html(a.toFixed(2));
            $('#contractNo').val(v.join(','));
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
        })
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
        <?php if($autoPay) {?>
        $('#bill-list>li').removeClass('checked');
        $('#contractNo').val('<?=$autoPay?>');
        var autoPayId = '<?php echo $autoPay?>';
        autoPayId = autoPayId.split(",");
        var a=0;
        $.each(autoPayId,function(i,e){
            a += parseFloat($('#bill-list li[data-id='+e+']').attr('data-amount'));
        });
        $('#total-amount').html(a.toFixed(2));
        $('#bill-form').submit();
        <?php }?>
    });
</script>
<?php
\common\widgets\JavascriptBlock::end();
?>
