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
        测试新的支付通道（<?= Yii::$app->params['bill_tips'] ?>）
    </div>
    <div class="bill-list">
        <ul id="bill-list">
            <?php foreach ($list as $key => $item) {
                $dateLength = strlen($key);
                ?>
                <li class="open" data-year="<?= $key ?>">
                    <p>
                        <i></i>
                        <span><?= $key ?></span>
                        <small>费用明细</small>
                    </p>
                    <span>
                            <em></em>
                        <?= number_format($item['totalAmount'], 2) ?>
                        </span>
                    <ul style="display: block;">
                        <?php foreach ($item['list'] as $k => $v) { ?>

                            <li data-item-id="<?= $v['chargeItemID'] ?>" data-date="<?= $v['shouldChargeDate'] ?>" data-id="<?= $v['contractNo'] ?>" data-amount="<?= $v['billAmount'] ?>">
                                <p>
                                    <small><?= $v['billDate'] ?></small>
                                    <?= $v['chargeItemName'] ?>

                                </p>
                                <span><?= number_format($v['billAmount'], 2) ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="button-fixed">
        <?php \components\za\ActiveForm::begin(['id' => 'bill-form']); ?>

        <input name="houseId" type="hidden" value="<?= $model->house_id ?>">
        <input id="contractNo" name="contractNo" data-required data-label="缴费账单" type="hidden" value="">
        <span class="select-all">全选</span>

        <span id="bill-discounts">
            <span style="font-size: 20px;padding: 10px;" data-amount="" data-credit-amount="" id="received-amount">实付：￥0.00</span>
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
                    */
            ?>

            var toPay = function (id) {
                $.post('/pay/wx-js-repair', {orderId: id}, function (res) {
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

                                app.go('/repair/payed-list');
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

            if (app.formValidate($(this))) {
                $('#shade-div').show();
                app.showLoading();
                $.post('/life-service/repair-bill-submit', val, function (res) {

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
            var v = [], a = 0, billDate = [], dataAmo=0;
            var isAll = true;

            $('#bill-list>li').each(function (i, row) {
                if ($(this).hasClass('checked')) {
                    $('li', this).each(function (i, item) {
                        v.push($(item).attr('data-id'));
                        billDate.push($(item).attr('data-date'));
                        a += parseFloat($(item).attr('data-amount'));
                    });
                } else {
                    isAll = false;
                }
            });

            $('.sk-item-price-origin').html(a.toFixed(2));

            $('#received-amount').html('实付：￥' + a.toFixed(2));
            $('#received-amount').attr('data-amount', dataAmo.toFixed(2));


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
