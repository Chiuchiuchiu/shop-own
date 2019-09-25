<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/24
 * Time: 16:58
 */

/* @var $model \common\models\PmOrderNewwindowPdf */
/* @var @email string */
?>
<div id="fpzz-send-again">
    <?php \components\za\ActiveForm::begin(['id' => 'send-again', 'action' => '/tcis/change-email-submit', 'method' => 'post']); ?>
    <div class="fpzz-sendagain-box">
        <div class="form-group">
            <?= \components\inTemplate\widgets\Html::hiddenInput('id', $model->id)?>
            <input name="email" value="<?= !empty($email) ? $email : '' ?>" placeholder="输入您的邮箱地址" data-required="true" type="email">
            <label for="">电子邮箱</label>
        </div>
    </div>

    <div class="fpzz-sendagain-boxbottom">
        <p>
            *说明：请确认当前邮箱地址，或输入新邮箱后，再点击提交，系统会给您重新发送电子发票。
        </p>
    </div>

    <button class="fpzz-sendagain-footer submit" id="submit" type="submit">提交</button>
    <?php \components\za\ActiveForm::end(); ?>
</div>

<div id="warning-tips">
    <div class="mask"></div>
    <div class="warning-content-box">
        <div class="warning-content-tips"></div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin()?>

<script type="text/javascript">

    $('#send-again').on('submit', function (){
        var data = $(this).serialize();
        if(app.formValidate($(this))){
            $.ajax({
                type: "POST",
                url: '/tcis/change-email-submit',
                data: data,
                beforeSend: function (){
                    $('.warning-content-tips').html('正在提交…………');
                    $('#warning-tips').show();
                },
                success: function (res){
                    if(res.code == 0){
                        $('.warning-content-tips').html('已发送至您的邮箱，请注意查收！');
                    } else {
                        $('.warning-content-tips').html(res.message);
                    }

                    $('#warning-tips').show();
                    hideMask('.mask');
                },
                error: function (error){
                    $('.warning-content-tips').html('服务出错！');
                    $('#warning-tips').show();
                    hideMask('.mask');
                },
                dataType: 'JSON'
            })
        }

        return false;
    });

    var hideMask = function (obj){
        $(obj).click(function (){
            $('#warning-tips').hide();
        });
    };



</script>

<?php \common\widgets\JavascriptBlock::end()?>
