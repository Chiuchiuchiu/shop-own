<?php
/**
 * @var $this \yii\web\View
 * @var $_model \common\models\SearchNotices
 * @var $_user \apps\www\models\Member
 * @var $memberHouse  \common\models\MemberHouse
 * @var $flowStyleID
 * @var integer $project
 * @var int $site
 */
use common\models\SearchNotices;
use components\za\Html;

?>
    <div class="panel" id="repair-form">

<?php \components\za\ActiveForm::begin(); ?>
    <div id="title-back" class="status " style="margin-top: 10px;">
        <p style="text-align:center;font-size: 16px;"><span>寻物启事</span></p>

    </div>
    <div class="pure-form">
    <label> </label>
    <div class="form-group">
        <?= Html::textInput(  '[title]', $_model->title, ['placeholder' => '请填写标题', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>标题</label>
    </div>
    <div class="form-group">
        <?= Html::textInput(  '[lose_address]', empty($_model->lose_address)?'':$_model->lose_address, ['placeholder' => '请填写丢失地点', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>丢失地点</label>
    </div>

    <label><span style="color: #ff2222">*</span>详细描述</label>
    <div class="form-group">
        <?= Html::textarea(  '[describtions]', empty($_model->describtions)?'':$_model->describtions, ['placeholder' => '','data-required' => true,]) ?>
    </div>
    <div class="form-group">
        <?= Html::textInput(  '[linkman]', empty($_model->linkman)?'':$_model->linkman, ['placeholder' => '请填写联系人', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>联系人</label>
    </div>
    <div class="form-group">
        <?= Html::textInput(  '[tel]', empty($_user->phone)?'':$_user->phone, ['placeholder' => '请填写联系电话', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>联系电话</label>
    </div>

    <div class="form-group">
        <div>
            <label>上传相关图片</label>
            <?= \components\inTemplate\widgets\Html::hiddenInput(  '[pics]', $_model->pics, ['id' => 'pics']) ?>
            <input type="file" id="upload" name="uploadfile" value="" style="display:none;"/>
            <ul id="upload-pics" class="pure-g upload-pics">
                <li class="pure-u-1-4 local-resize-btn" onclick="upload.click()"><span></span></li>
            </ul>
        </div>
    </div>

    <div style="height: 6em;line-height: 6em;">

    </div>

    <button type="submit" class="btn btn-block btn-bottom-all btn-primary">提交</button>
    <?php \components\za\ActiveForm::end(); ?>


    <?php
    \common\widgets\JavascriptBlock::begin();
    ?>

    <script>
        $(function () {
            $('#repair-form').on('loaded', function () {
                var that = this;
                var siteData = 2;

                if($('#houseShow').length > 0 && siteData == 2){
                    $('#houseChoose').hide();
                    $('#projectHouse').hide();
                } else {
                    $('#houseChoose').hide();
                }


                $('#houseShow').on('click',function(){
                    if(window.confirm("要修改地址吗？")){
                        $('#houseChoose').show();
                        $('#houseShow').hide();
                        $('#projectHouse').show();
                    }
                });

                $('#project', that).on('change', function () {
                    $.getJSON('/house/query', {houseId: $(this).val()}, function (res) {
                        $('#houseChoose').html('');
                        $.each(res.data.structure, function (i, row) {
                            $('#houseChoose').append(
                                $('<div/>')
                                    .append(
                                        $('<select/>').attr('data-i',i)
                                    )
                                    .append(
                                        $('<label/>').html(row)
                                    ).addClass('form-group')
                            );
                        });
                        $('#houseChoose select:last').attr('data-last',1);
                        $('#houseChoose select[data-i=0]').append($('<option/>').attr('value', '').html('请选择'));
                        $.each(res.data.list, function (houseId, row) {
                            $('#houseChoose select[data-i=0]').append($('<option/>').attr('value', row.house_id).html(row.house_name));
                        });
                    });
                }).trigger('change');

                $('#houseChoose', that).on('change', 'select', function () {
                    var i = $(this).attr('data-i');
                    i++;
                    $('#houseChoose select[data-i]', that).each(function () {
                        if ($(this).attr('data-i') >= i) {
                            $(this).parent().show();
                            $(this).attr('data-required', 1);
                            $('option', this).remove();
                        }
                    });
                    var target = $('#houseChoose select[data-i=' + i + ']');
                    $('#ui-loading').show();
                    var houseId = $(this).val();
                    $.getJSON('/house/query', {houseId: houseId}, function (res) {
                        target.append($('<option/>').attr('value', '').html('请选择'));
                        $.each(res.data.list, function (houseId, row) {
                            target.append($('<option/>').attr('value', row.house_id).html(row.house_name));
                        });
                        if (res.data.list.length == 0) {
                            $('input#house_id').val(houseId);
                            $('#houseChoose select[data-i]', that).each(function () {
                                if ($(this).attr('data-i') >= i) {
                                    $(this).parent().hide();
                                    $(this).removeAttr('data-required');
                                }
                            });
                        }
                        $('#ui-loading').hide();
                    });
                });

                $('#upload').localResizeIMG({
                    width: 400,
                    quality: 1,
                    success: function (result) {
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
                            base64_string: result.clearBase64
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

                    if(confirm('是否要提交？')){
                        if (app.formValidate(this)) {
                            var val = $(this).serialize();
                            app.showLoading();
                            $.post('/new-repair', val, function (res) {
                                app.hideLoading();
                                if (res.code === 0) {
                                    if(res.data.goUrl){
                                        app.go(res.data.goUrl);
                                        return;
                                    } else {
                                        app.go('/new-repair/list?status=0');
                                    }

                                } else {
                                    app.tips().error(res.message);
                                }
                            }, 'json');
                        }
                    }

                    return false;
                })
            });

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

        });
    </script>

<?php
\common\widgets\JavascriptBlock::end();
?>