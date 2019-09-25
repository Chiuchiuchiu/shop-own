<?php
/**
 * @var $this \yii\web\View
 * @var $repair \common\models\Repair
 * @var $_user \apps\www\models\Member
 * @var $memberHouse  \common\models\MemberHouse
 * @var $flowStyleID
 * @var integer $project
 * @var int $site
 */
use common\models\Repair;
use components\za\Html;

?>
    <div class="panel" id="repair-form">
<?php \components\za\ActiveForm::begin(); ?>
    <div class="pure-form">

    <?php if($flowStyleID == 'w'){?>

        <label>您需要</label>
        <div class="identity-choice inline">
            <div <?php if($site == Repair::SITE_TYPE_2) echo 'class="hover"';?> data-d="2" <?php if($site != Repair::SITE_TYPE_2) echo "data-go='/new-repair?flowStyleID=w&site=". Repair::SITE_TYPE_2 ."'"?>>
                <div>个人报修</div>
                <i></i>
            </div>
            <div <?php if($site == Repair::SITE_TYPE_1) echo 'class="hover"';?> data-d="1" <?php if($site != Repair::SITE_TYPE_1) echo "data-go='/new-repair?flowStyleID=w&site=". Repair::SITE_TYPE_1 ."'"?>>
                <div>公共维修</div>
                <i></i>
            </div>
        </div>

    <?php }?>

    <?= Html::hiddenInput($repair->formName() . '[site]', $site, ['placeholder' => '请选择报修类型', 'data-required' => true, 'id' => 'site']) ?>

    <?= Html::hiddenInput($repair->formName() . '[flow_style_id]', $flowStyleID, ['data-required' => true]) ?>

    <?php /**
    <label>请选择您的报修类型</label>
    <div class="form-group">
        <?php
        $map['']= '请选择类型';
        foreach(Repair::TypeMap() as $key=>$v)
            $map[$key]=$v;
        echo Html::dropDownList(
            $repair->formName() . '[type]',
            $repair->type,
            $map,
            ['data-required' => true, 'data-label' => '报修类型']
        );
        ?>
        <label>报修类型</label>
    </div>
 */ ?>

    <label>输入您个人联系信息</label>
    <div class="form-group">
        <?= Html::textInput($repair->formName() . '[name]', $_user->name, ['placeholder' => '请输入真实姓名', 'data-required' => true]) ?>
        <label>您的姓名</label>
    </div>
    <div class="form-group">
        <?= Html::textInput($repair->formName() . '[tel]', empty($_user->phone)?'':$_user->phone, ["readonly" => true, 'data-required' => true]) ?>
        <label>手机号码</label>
    </div>

    <?php echo Html::hiddenInput($repair->formName() . '[house_id]', $memberHouse?$memberHouse->house_id:null, ['id' => 'house_id','data-required' => true]); ?>
    <div id="projectHouse">
        <div class="form-group">
            <?php
            echo Html::dropDownList("", null,
                \yii\helpers\ArrayHelper::map(
                    \common\models\Project::findAll(['status'=>1, 'house_id' => $project]),
                    'house_id',
                    'house_name'
                )
                ,['id' => 'project']);
            ?>
            <label>小区</label>
        </div>
    </div>

    <div id="houseChoose">
        <div class="form-group">

        </div>
    </div>

    <?php if($memberHouse && $flowStyleID == Repair::FLOW_STYLE_TYPE_W) { ?>
        <div <?php if($site == Repair::SITE_TYPE_2 ) echo 'id="houseShow"'; ?> data-showHouse="1">
            <label>地址</label>
            <div class="form-group">
                <?= Html::textarea($repair->formName() . '[address]', $memberHouse->house->ancestor_name, ['placeholder' => '','data-required' => true,]) ?>
            </div>
        </div>
    <?php } ?>

    <label>输入您的<?= $flowStyleID == Repair::FLOW_STYLE_TYPE_W ? '维修' : '投诉' ?>内容</label>
    <div class="form-group">
        <?php
        echo Html::textarea($repair->formName() . '[content]', $repair->content, ['placeholder' => '详细描述', 'data-required' => true])
        ?>
        <div>
            <label>上传相关图片</label>
            <?= \components\inTemplate\widgets\Html::hiddenInput($repair->formName() . '[pics]', $repair->pics, ['id' => 'pics']) ?>
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
                var siteData = <?= $site ?>;

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