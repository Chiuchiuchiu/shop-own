<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/22
 * Time: 11:38
 */

/**
 * @var $this \yii\web\View
 * @var $pmOrder  \common\models\PmOrder
 * @var $pmOrderFpzz  \common\models\PmOrderFpzz
 * @var $memberExt  \apps\www\models\MemberExt
 * @var $id int
 * @var $tips string
 */
use components\za\Html;

?>
    <div class="panel" id="recipt-form">

        <div class="tips-message">
            <?= $tips ?><br />
            1、开企业抬头发票，请准确填写"单位名称"及对应的“纳税人识别号”
        </div>

<?php \components\za\ActiveForm::begin(['action' => 'save']); ?>
    <div class="pure-form">
    <label>选择你要开具的发票类型</label>
    <div class="invoice-choice inline">
        <div class="hover">
            <div>电子发票</div>
            <i></i>
        </div>
        <div data-name="p-invoice">
            <div>纸质发票</div>
            <i></i>
        </div>

        <?= Html::hiddenInput($pmOrderFpzz->formName() . '[type]', 1, ['placeholder' => '请选择你要开具的发票类型', 'data-required' => true, 'id' => 'fpzzType']) ?>
    </div>

    <label>发票详情</label>

    <div id="recipt-mid">
        <table class="table">
            <tbody>
            <tr>
                <td class="recipt-td" colspan="3">发票抬头：</td>
            </tr>

            <tr>
                <td colspan="3" class="invoice-title-box">
                    <input type="hidden" class="invoice-ty" name="invoice-ty" value="1">
                    <label for="personal">
                        <span class="radio-invoice-hover" data-id="1">个人</span>
                    </label>
                    <label for="b-unit">
<!--                        <span class="radio-invoice" data-id="2">单位</span>-->
                    </label>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <?php
                    echo Html::input('text', $pmOrderFpzz->formName() . '[user_name]', '', ['placeholder' => '请在此处填写发票抬头', 'data-required' => true, 'required' => 'required', 'id' => 'member-name', 'readonly' => 'readonly','style' => 'width: 100%']);
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <?php
                    echo Html::input('text','register_id', '', ['placeholder' => '请在此处填写纳税人识别号', 'id' => 'm-register-id', 'style' => 'width: 100%']);
                    ?>
                </td>
            </tr>

            <tr>
                <td class="" width="100%" colspan="3">发票内容：</td>
            </tr>

            <tr id="loading-gif">
                <td colspan="2">
                    <img src="../static/images/icon/loading.gif" width="100%" alt="">
                </td>
            </tr>

            <tr id="fpzz-amount">
                <td class="recipt-td">发票金额</td>
                <td colspan="2">
                    <span id="recipt-total-amount">0.00元</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <?= Html::hiddenInput($pmOrderFpzz->formName() . '[pm_order_id]', $id, ['placeholder' => '', 'data-required' => true,]) ?>

    <label>收件信息</label>
    <div class="form-group">
        <?php
        echo Html::input('email', $pmOrderFpzz->formName() . '[email]', isset($memberExt->email) ? $memberExt->email : '',['placeholder' => '输入您的邮箱地址', 'data-required' => true])
        ?>
        <label for="">邮箱</label>

    </div>

    <div class="form-group p-none">
        <label for="">所在地区</label>
        <div class="form-group">
            <textarea readonly><?= $pmOrder->house->ancestor_name?></textarea>
        </div>
    </div>

    <div class="form-group p-none">
        <?php
        echo Html::input('phone', $pmOrderFpzz->formName() . '[phone]', isset($memberExt->phone) ? $memberExt->phone : '',['placeholder' => '输入您的联系方式', 'data-required' => false])
        ?>
        <label for="">联系方式</label>

    </div>

    <button type="submit" id="comfirm" class="btn btn-block btn-bottom-all btn-primary">提交</button>
    <?php \components\za\ActiveForm::end(); ?>

    <div id="typeSelect">
        <div class="mask"></div>
        <div class="c-panel" style="z-index: 99">
            <div class="t">
                <table>
                    <tbody>
                    <tr>
                        <td class="invoice-lable"><lable>发票类型:</lable></td>
                        <td><span id="invoice-type">纸质发票</span></td>
                    </tr>
                    <tr class="e-invoice-display" style="display: none;">
                        <td><lable>电子邮箱:</lable></td>
                        <td><span id="user-e-email"></span></td>
                    </tr>
                    <tr class="e-invoice-display" style="display: none;">
                        <td><lable>发票抬头:</lable></td>
                        <td><span id="gfmc"></span></td>
                    </tr>
                    <tr class="e-invoice-display" style="display: none;">
                        <td><lable>税号:</lable></td>
                        <td><span id="gfnsrsbh"></span></td>
                    </tr>
                    <tr id="p-invoice-display" style="display: none;">
                        <td><lable>联系人:</lable></td>
                        <td><span id="member-name-p"></span></td>
                    </tr>
                    <tr>
                        <td rowspan="2" colspan="2">
                            <p>*请确认邮箱无误，电子发票将在系统开具后发送至您的邮箱，请注意查收</p>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="btn-left">
                    <a href="javascript:void(0);" class="btn btn-empty btn-a">取消</a>
                </div>

                <div class="btn-right">
                    <a href="javascript:void(0);" id="btn-comfirm-submit" class="btn btn-comfirm-submit btn-a">确认提交</a>
                </div>

            </div>

        </div>
    </div>

    <div id="warning-tips">
        <div class="mask"></div>
        <div class="warning-content-box">
            <div class="warning-content">无账单明细！！！！！！</div>
            <div class="warning-back-box">
                <a href="/order/pm-list">返回</a>
                <a href="javascript:void(0);" class="reload">关闭</a>
            </div>
        </div>
    </div>

<?php

\common\widgets\JavascriptBlock::begin();
?>
    <script type="text/javascript">

        $('#recipt-form').on('loaded', function () {
            var orderId = <?= $pmOrder->id ?>

            $('.invoice-choice>div').on('click',function(){
                $('.invoice-choice>div').removeClass('hover');
                $(this).addClass('hover');
                var dataName = $(this).attr('data-name');
                if(dataName == 'p-invoice'){
                    $('.p-none').show();
                    $('input[type="phone"]').attr('data-required', true);
                    $('#fpzzType').val(2);
                } else{
                    $('.p-none').hide();
                    $('input[type="phone"]').removeAttr('data-required');
                    $('#fpzzType').val(1);
                }
            });

            $('.invoice-title-box>label').on('click', 'span', function (){
                var cl = $(this).attr('class');
                var ty = $(this).attr('data-id');

                if(cl == 'radio-invoice'){
                    $(this).removeClass('radio-invoice').addClass('radio-invoice-hover');
                    $(this).parent().siblings().children('span').removeClass('radio-invoice-hover').addClass('radio-invoice');
                }

                if(ty == '1'){
                    $('#m-register-id').removeAttr('required');
                    $('#m-register-id').hide();
                } else {
                    $('#m-register-id').show();
                    $('#m-register-id').attr('required', 'required');
                }

                $('.invoice-ty').val(ty);

            });

            $('form').on('submit', function () {
                if (app.formValidate(this)) {
                    var email = $('input[type="email"]').val();
                    var invoiceTypeName = '';
                    var gfnsrsbh = '';
                    var invoiceType = $('#fpzzType').val();
                    var memberName = trim($('#member-name').val());
                    var itV = $('.invoice-ty').val();
                    var mR = trim($('#m-register-id').val());
                    $('#m-register-id').val(mR);
                    $('#member-name').val(memberName);

                    if(memberName.length < 1){
                        $('.warning-content').html('请填写发票抬头');
                        $('#warning-tips').show().fadeOut(2000);
                        return false;
                    }

                    if(itV > 1){
                        if(checkCreditCodeOr15(mR) || checkCreditCodeOr18(mR)){
                            gfnsrsbh = mR;
                        } else {
                            $('.warning-content').html('请填写纳税人识别号');
                            $('#warning-tips').show().fadeOut(2000);
                            return false;
                        }
                    }

                    switch(invoiceType){
                        case '1':
                            invoiceTypeName = '电子发票';
                            $('#user-e-email').html(email);
                            $('#gfmc').html(memberName);
                            $('#gfnsrsbh').html(gfnsrsbh);
                            $('#p-invoice-display').hide();
                            $('.e-invoice-display').show();
                            break;
                        case '2':
                            invoiceTypeName = '纸质发票';
                            $('.e-invoice-display').hide();
                            $('#p-invoice-display').show();
                            break;
                    }
                    $('#invoice-type').html(invoiceTypeName);
                    $('#typeSelect').show();
                }

                return false;
            });

            $('#btn-comfirm-submit').bind('click',function(){
                var val = $('form').serialize();
                $('.warning-content').html('正在提交…………');
                $('.warning-back-box').hide();
                $('#warning-tips').show();
                $.post('save', val, function (res) {
                    if (res.code === 0) {
                        $('.warning-content').html('提交成功…………');
                        setTimeout("app.go('/tcis/success')", 2000);
                    } else {
                        $('.warning-content').html(res.message);
                        $('.warning-back-box').show();
                        $('#warning-tips').show();
                    }
                }, 'json');

            });

            $('.btn-a').bind('click',function(){
                $('#typeSelect').hide();
            });

            $('.reload').click(function (){
                reload();
            });

            queryInv(orderId);

        });

        function getFpzzItem(orderId){
            var html = '';
            $.ajax({
                url: '/tcis/build-fpzz-item',
                type: 'POST',
                data: {order_id:orderId},
                success: function (res){
                    $('#loading-gif').hide();
                    if(res.code == 0 && res.data.lists.length > 0){
                        $.each(res.data.lists, function (index, value){
                            html += '<tr class="fpzz-charg-item"><td class="fpzz-title">' + value.spmc + '</td><td class="fpzz-bill-date">'+ value.ggxh +'</td><td class="order-item-bill">'+ value.origin_amount +'</td></tr>';
                        });

                        $(html).insertBefore('#fpzz-amount');
                        $('#recipt-total-amount').html(res.data.amount + '元');
                        $('#member-name').val(res.data.accountName);
                        $('#member-name-p').html(res.data.accountName);
                        $('#comfirm').show();
                    } else {
                        $('.warning-content').html(res.message);
                        $('#warning-tips').show();
                    }
                },
                error: function (error){
                    $('.warning-content').html('抱歉！服务出错……');
                    $('#warning-tips').show();
                },
                dataType:'json'
            })
        }

        function reload(){
            // window.location.href=window.location.href;
            $('#warning-tips').hide();
        }

        function trim(str){ //
            return str.replace(/(^\s*)|(\s*$)/g, "");
        }

        function checkCreditCodeOr15(value){
            var reg = /^\d{6}[0-9A-Z]{9}$/;
            if(reg.test(value)){
                return true;
            }

            return false;
        }

        function checkCreditCodeOr18(value){
            var reg = /^[0-9A-Z]{2}\d{6}[0-9A-Z]{10}$/;
            if(reg.test(value)){
                return true;
            }

            return false;
        }

        function queryInv(orderId) {
            $.getJSON('/tcis/newwindow-query-inv', {order_id:orderId}, function (res){
                $('#loading-gif').hide();
                if(res.code == 0){
                    getFpzzItem(res.data.orderId);
                } else {
                    $('.warning-content').html(res.message);
                    $('#warning-tips').show();
                }
            });
        }

    </script>
<?php
\common\widgets\JavascriptBlock::end();
?>