<?php
/**
 * @var $this \yii\web\View
 * @var $repair \common\models\Repair
 * @var $_user \apps\www\models\Member
 * @var $memberHouse  \common\models\MemberHouse
 * @var $flowStyleID
 */
use components\za\Html;

?>
<style>
    .pure-form input[readonly], .pure-form select[readonly], .pure-form textarea[readonly] {
        background-color: #fff;
        color: #777;
        border-color: #ccc;
    }

    .del-img{
        padding: 10px 20px;
        background-size: 46% !important;
        background: url() center no-repeat;
    }
</style>
    <div class="panel" id="repair-form">
<?php \components\za\ActiveForm::begin(); ?>
    <div class="pure-form">
    <label>设备信息</label>
    <div class="form-group">
        <?= Html::textInput( $MeterHouse->formName().'[surname]', $Items->ownername, ['readonly'=>'readonly','placeholder' => '请输入真实姓名']) ?>
        <label>户主姓名</label>
    </div>
    <div class="form-group">
        <?= Html::textInput( $MeterHouse->formName().'[uid]', $Items->uid, ['readonly'=>'readonly','placeholder' => '']) ?>
        <label>仪表编号</label>
    </div>
    <div class="form-group">
        <?= Html::textInput( $MeterHouse->formName().'[ancestor_name]', $Items->ancestor_name, ['readonly'=>'readonly','placeholder' => '', 'data-required' => true]) ?>
        <label></label>
    </div>

    <div class="form-group">
        <?= Html::textInput( $MeterHouse->formName().'[last_meter_time]', date('Y-m-d',$Items->last_meter_time), ['readonly'=>'readonly','placeholder' => '上期抄表', 'data-required' => true]) ?>
        <label>上期抄表</label>
    </div>
    <div class="form-group">
        <?= Html::textInput( 'last_meter_data', round($Items->last_meter_data,0), ['readonly'=>'readonly','placeholder' => '请输入真实姓名', 'data-required' => true]) ?>
        <label>上期读数</label>
    </div>
    <label>输入本期的数据</label>
    <div class="form-group">
        <?= Html::textInput( 'meter_data', '', ['type'=>'tel','placeholder' => '请输入本期读数', 'data-required' => true]) ?>
        <label>本期读数</label>
    </div>
    <div class="form-group">
        <?= Html::textInput( 'data_confirm', '', ['type'=>'tel','placeholder' => '请确认本期读数', 'data-required' => true]) ?>
        <label>确认读数</label>
    </div>
    <div class="form-group">
        <div>
            <label>上传相关图片</label>

            <?= Html::hiddenInput( 'UploadUid', $uid) ?>
            <?= Html::hiddenInput( 'meter_house_id', $Items->id) ?>
            <?= Html::hiddenInput( 'meter_id', $Items->meter_id) ?>
            <?= Html::hiddenInput( 'meter_type', $Items->meter_type) ?>

            <?= \components\inTemplate\widgets\Html::hiddenInput($MeterHouse->formName().'[pics]','', ['id' => 'pics']) ?>
            <input type="file" id="upload" name="uploadfile" value="" style="display:none;"/>
            <ul id="upload-pics" class="pure-g upload-pics">
                <li class="pure-u-1-4 local-resize-btn" onclick="upload.click()"><span></span></li>
            </ul>

            <button type="submit" class="btn btn-block btn-bottom-all btn-primary">提交抄表数据</button>
        </div>

        <?php \components\za\ActiveForm::end(); ?>
    </div>
    <?php
    \common\widgets\JavascriptBlock::begin();
    ?>
    <script>
        $(function () {
            $('#repair-form').on('loaded', function () {


                $('#upload').localResizeIMG({
                    width: 400,
                    quality: 1,
                    success: function (result) {
                        console.log(result);
                        var obj = $('<li class="pure-u-1-4 uploaded-img uploading"><i class="mask"></i><i class="del-icon"></i><span data-save="" style="background-image:url(data:image/jpg;base64,' + result.clearBase64 + ')"></span></li>');
                        $('#upload-pics .local-resize-btn').before(obj);
                        if ($('#upload-pics li').length > 3) {
                            $('#upload-pics .local-resize-btn').hide();
                        }
                        var t = setInterval(function () {
                            var h = $('.mask', obj).height();
                            $('.mask', obj).css({bottom: 0});
                            $('.mask', obj).height(h * 0.99);
                        }, 60);
                        var submitData = {
                            base64_string: result.clearBase64,
                            UploadUid:'<?=$uid;?>'
                        };
                        $.ajax({
                            type: "POST",
                            url: "/default/upload",
                            data: submitData,
                            dataType: "json",
                            beforeSend:function(){

                            },
                            success: function (res) {
                                if (0 == res.code) {
                                    $('.del-icon', obj).attr('data-save', res.data.saveUrl);
                                    $('span', obj).attr('data-save', res.data.saveUrl);
                                    $('span', obj).attr('style', 'background-image:url(' + res.data.url + ')');
                                    obj.removeClass('uploading');
                                    $(obj).on('click', '.del-icon', delImage);
                                    clearTimeout(t);
                                    if ($('#upload-pics li').length > 3) {
                                        $('#upload-pics .local-resize-btn').hide();
                                    }
                                    var d = [];
                                    $('#upload-pics li').each(function (i, e) {
                                        save = $('span', e).attr('data-save');
                                        if (save)
                                            d.push(save);
                                    });
                                    $('#pics').val(d.join(","));
                                    return false;
                                } else {
                                    return false;
                                }
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) { //上传失败
                                alert(XMLHttpRequest.status);
                                alert(XMLHttpRequest.readyState);
                                alert(textStatus);
                            }
                        });
                    }
                });

                $('form').on('submit', function () {
                    if (app.formValidate(this)) {
                        var val = $(this).serialize();
                        app.showLoading();
                        $.post('/meter/save', val, function (res) {
                            app.hideLoading();
                            if (res.code === 0) {
                                app.tips().success(res.message);
                                setTimeout(function() {location.href = '/meter/index';},1000);
                            } else {
                                app.tips().error(res.message);
                            }
                        }, 'json');
                    }
                    return false;
                })

                function delImage(){
                    console.log($(this).attr('data-save'));
                    $(this).parent().remove();
                    var d = [];
                    $('#upload-pics li').each(function (i, e) {
                        save = $('span', e).attr('data-save');
                        if (save)
                            d.push(save);
                    });
                    $('#pics').val(d.join(","));
                    if ($('#upload-pics li').length > 3) {
                        $('#upload-pics .local-resize-btn').hide();
                    } else {
                        $('#upload-pics .local-resize-btn').show();
                    }
                }
            })
        });
    </script>
<?php
\common\widgets\JavascriptBlock::end();
?>