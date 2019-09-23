<?php
/**
 * @var $this \yii\web\View
 * @var $feedback \common\models\Feedback
 * @var $_user \apps\www\models\Member
 */
use common\models\Feedback;
use components\za\Html;

?>
    <div class="panel" id="feedback-form">
<?php \components\za\ActiveForm::begin(); ?>
    <div class="pure-form">
    <label>您需要</label>
    <div class="identity-choice inline">
        <div data-go="/repair">
            <div>报修</div>
            <i></i>
        </div>
        <div class="hover">
            <div>建议</div>
            <i></i>
        </div>
    </div>

    <label>请选择您的建议类型</label>
    <div class="form-group">
        <?php
        $map=[];
        $map[''] = '请选择类型';
        foreach(Feedback::TypeMap() as $key=>$v)
            $map[$key]=$v;
        echo Html::dropDownList(
            $feedback->formName() . '[type]',
            $feedback->type,
            $map,
            ['data-required' => true, 'data-label' => '建议类型']
        );
        ?>
        <label>建议类型</label>
    </div>
    <label>输入您个人联系信息</label>
    <div class="form-group">
        <?= Html::textInput($feedback->formName() . '[name]', $_user->name, ['placeholder' => '请输入真实姓名', 'data-required' => true]) ?>
        <label>您的姓名</label>
    </div>
    <div class="form-group">
        <?= Html::textInput($feedback->formName() . '[tel]', empty($_user->phone)?'':$_user->phone, ['placeholder' => '请输入真实手机号码', 'data-required' => true]) ?>
        <label>手机号码</label>
    </div>

    <?php echo Html::hiddenInput($feedback->formName() . '[house_id]', null, ['id' => 'house_id']); ?>
    <div class="form-group">
        <?php
        echo Html::dropDownList("", null,
            \yii\helpers\ArrayHelper::map(
                \common\models\Project::findAll(['status'=>1]),
                'house_id',
                'house_name'
            )
            , ['id' => 'project', 'data-required' => true]);
        ?>
        <label>小区</label>
    </div>

    <div id="houseChoose">

    </div>
    <label>输入您的建议</label>
    <div class="form-group">
        <?php
        echo Html::textarea($feedback->formName() . '[content]', $feedback->content, ['placeholder' => '详细描述', 'data-required' => true])
        ?>
        <div>
            <label>上传相关图片</label>
            <?= \components\inTemplate\widgets\Html::hiddenInput($feedback->formName() . '[pics]', $feedback->pics, ['id' => 'pics']) ?>
            <input type="file" id="upload" name="uploadfile" value="" style="display:none;"/>
            <ul id="upload-pics" class="pure-g upload-pics">
                <li class="pure-u-1-4 local-resize-btn" onclick="upload.click()"><span></span></li>
            </ul>

            <button type="submit" class="btn btn-block btn-bottom-all btn-primary">提交</button>
        </div>

        <?php \components\za\ActiveForm::end(); ?>
    </div>
    <?php
    \common\widgets\JavascriptBlock::begin();
    ?>

    <script>
        $(function () {
            $('#feedback-form').on('loaded', function () {
                var that = this;
                $('#project', that).on('change', function () {
                    $('#ui-loading').show();
                    $.getJSON('/house/query', {houseId: $(this).val()}, function (res) {
                        $('#houseChoose').html('');
                        $.each(res.data.structure, function (i, row) {
                            $('#houseChoose').append(
                                $('<div/>')
                                    .append(
                                        $('<select/>').attr('data-required',1).attr('data-i',i)
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
                        $('#ui-loading').hide();
                    });
                }).trigger('change');

                $('#houseChoose', that).on('change','select', function () {
                    var i = $(this).attr('data-i');
                    i++;
                    $('#houseChoose select[data-i]', that).each(function(){
                        if($(this).attr('data-i')>=i){
                            $(this).parent().show();
                            $(this).attr('data-required',1);
                            $('option',this).remove();
                        }
                    });
                    var target = $('#houseChoose select[data-i='+i+']');
                    $('#ui-loading').show();
                    var houseId=$(this).val();
                    $.getJSON('/house/query', {houseId: houseId}, function (res) {
                        target.append($('<option/>').attr('value', '').html('请选择'));
                        $.each(res.data.list, function (houseId, row) {
                            target.append($('<option/>').attr('value', row.house_id).html(row.house_name));
                        });
                        if(res.data.list.length==0){
                            $('input#house_id').val(houseId);
                            $('#houseChoose select[data-i]', that).each(function(){
                                if($(this).attr('data-i')>=i){
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
                        var obj = $('<li class="pure-u-1-4 uploading"><i class="mask"></i><span data-save="" style="background-image:url(data:image/jpg;base64,' + result.clearBase64 + ')"></span></li>');
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
                                    $('span', obj).attr('data-save', res.data.saveUrl);
                                    $('span', obj).attr('style', 'background-image:url(' + res.data.url + ')');
                                    obj.removeClass('uploading');
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
                            complete: function (XMLHttpRequest, textStatus) {
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
//                        app.showLoading();
                        $.post('/feedback', val, function (res) {
//                            app.hideLoading();
                            if (res.code === 0) {
                                app.go('/feedback/list')
                            } else {
                                app.tips().error(res.message);
                            }
                        }, 'json');
                    }
                    return false;
                })
            })
        });
    </script>
<?php
\common\widgets\JavascriptBlock::end();
?>