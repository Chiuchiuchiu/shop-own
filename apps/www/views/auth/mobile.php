<?php
/**
 * @var $_user \apps\www\models\Member
 * @var $locationUrl string|null
 */
?>
<div class="panel" id="auth-mobile">
    <div class="banner"></div>
    <div class="pd">
        <div class="info">
            <img src="<?= Yii::getAlias($_user->headimg) ?>">
            <h4><?php echo $_user->name ? $_user->name : $_user->nickname ?></h4>
            <p>亲缴费前，需要核实您信息噢！</p>
        </div>
        <div class="pure-form">
            <?php \components\za\ActiveForm::begin() ?>
            <div class="form-group phone">
                <input type="text" name="phone" placeholder="请输入手机号码">
            </div>
            <div class="form-group flex">
                <div>
                    <input type="text" name="code" placeholder="验证码">
                </div>
                <div>
                    <button id="getCode" type="button" data-wait="0" class="btn btn-empty">发送验证码</button>
                </div>
                <input type="hidden" name="getUrl" value="<?= $locationUrl ?>">
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block">确 定</button>
            </div>
            <?php \components\za\ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php \common\widgets\JavascriptBlock::begin()?>
<script>
    $(function(){
        function trim(str){ //
            return str.replace(/(^\s*)|(\s*$)/g, "");
        }

        $('#getCode').on('click', function () {
            var phone = $('input[name=phone]').val();
            phone = trim(phone);
            if (!/^1[0-9]{10}$/.test(phone)) {
                $('input[name=phone]').addClass('has-error');
                app.tips().error("请输入正确的手机号码");
                return 0;
            }
            if ($(this).attr('data-wait') > 0) {
                app.tips().warning("请稍后再尝试发送");
                return 0;
            }
            app.showLoading();
            $.getJSON('/auth-code?phone=' + phone, function (res) {
                app.hideLoading();
                if (res.code === 0) {
                    $('#getCode').attr('data-wait', 60);
                    waitSms();
                } else {
                    app.tips().warning(res.message);
                }
            });
        });
        var waitSms = function () {
            var o = $('#getCode'), t = parseInt(o.attr('data-wait'));
            t--;
            if (t <= 0) {
                o.html('重新获取');
                t = 0;
            } else {
                o.html('等待' + t + '秒');
            }
            o.attr('data-wait', t);
            if (t > 0)
                setTimeout(waitSms, 1000)
        };
        $('form').on('submit', function () {
            if (app.formValidate(this)) {
                var val = $(this).serialize();
                app.showLoading();
                $.post('', val, function (res) {
                    app.hideLoading();
                    if (res.code === 0) {
                        if(res.data.goUrl){
                            window.location.href=res.data.goUrl;
                        }else{
                            app.go('/auth/');
                        }
                    } else {
                        app.tips().error(res.message);
                    }
                }, 'json')
            }
            return false;
        })
    })
</script>
<?php \common\widgets\JavascriptBlock::end()?>