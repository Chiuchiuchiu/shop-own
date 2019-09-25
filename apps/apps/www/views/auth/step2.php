<?php
/**
 * @var $this \yii\web\View
 * @var $newWindow
 * @var $model \common\models\MemberHouse
 * @var integer $memberId
 * @var integer $checkId
 * @var integer $checkPhone
 */

?>
<div class="panel" id="auth-step-2">
    <?php
    \components\za\ActiveForm::begin();
    ?>
    <div class="pure-form">
        <label>补全业主预留手机号后4位</label>
        <div class="number-input">
            <p class="tip">点击下框输入号码</p>
            <p class="input">
                <span><?= substr($checkPhone, 0, -4) ?></span>
                <input type="text" style="display: inline-block" maxlength="4" name="phone" id="phone" data-label="手机号码" required>
            </p>
        </div>
        <label>补全业主预留身份证后4位(字母需大写)</label>
        <div class="number-input">
            <p class="tip">点击下框输入号码</p>
            <p class="input">
                <span><?= substr($checkId, 0, 8) ?>******</span>
                <input type="text" style="display: inline-block" maxlength="4" name="idNumber" id="idNumber" data-label="身份证" value="" required>
            </p>
        </div>

        <div style="height: 6em;">

        </div>

        <a class="btn btn-block btn-primary">提交认证</a>
    </div>
    <?php
    \components\za\ActiveForm::end();
    ?>
</div>

<?php
\common\widgets\JavascriptBlock::begin();
?>

<script>

    $('.btn-primary').click(function(){
        var forms = $('form');
        var idNumber = $('#idNumber').val();
        var phone = $('#phone').val();

        var data = $(forms).serialize();
        $.ajax({
            type: "POST",
            url: "",
            data: data,
            beforeSend: function (){
                app.showLoading();
            },
            success: function (res){
                app.hideLoading();
                if(res.code===0){
                    if(res.data.goUrl){
                        app.go(res.data.goUrl);
                        return;
                    }

                    app.go('/auth/result?type=success');
                }else{
                    app.tips().error(res.message);

                    if(res.data.goUrl){

                        setTimeout(function (){
                            app.go(res.data.goUrl);
                        }, 1000);
                    }
                }
            },
            error: function (){
                app.hideLoading();
                app.tips().error('服务繁忙！');
            },
            dataType: 'json'
        });

        return false;
    });

</script>
<?php
\common\widgets\JavascriptBlock::end();
?>
