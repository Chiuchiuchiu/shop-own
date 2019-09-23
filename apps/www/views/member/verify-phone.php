<?php
/**
 * @var $_user \apps\www\models\Member
 * @var $locationUrl string|null
 */
?>
<div class="panel" id="auth-mobile">

    <div class="pd">
        <div class="info">
            <h4><?php echo $_user->name ? $_user->name : $_user->nickname ?></h4>
            <p>更换手机号需要验证原手机号！</p>
        </div>
        <div class="pure-form">
            <?php \components\za\ActiveForm::begin() ?>
            <div class="form-group phone">
                <input type="text" name="" disabled value="<?= substr($_user->phone, 0, 3) . '*****' . substr($_user->phone, -3) ?>" placeholder="请输入手机号码">
            </div>
            <div class="form-group flex">
                <div>
                    <input type="text" required name="phone" placeholder="请输入原手机号">
                </div>
                <input type="hidden" name="getUrl" value="<?= '' ?>">
            </div>
            <div class="form-group" style="margin-top: 3em;">
                <button class="btn btn-primary btn-block">确 定</button>
            </div>
            <?php \components\za\ActiveForm::end() ?>
        </div>
    </div>
</div>
<?php \common\widgets\JavascriptBlock::begin()?>
<script>
    $(function(){

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
                            app.go('/member/change-mobile');
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