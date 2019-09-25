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
use components\za\Html;

?>
<style>

    form .pure-form .form-group textarea {
        padding: .5em;
        height: 4em;
        text-align: left;
    }
    /*---------*/
    .horizontal-ul {
        list-style: none;
        margin: 0px;
        padding: 0px;
    }

    .horizontal-ul li {
        float: left;
    }

    .horizontal-ul li span {
        color: #666;
    }

    .horizontal-menu-div {
        width: 100%;
        background-color: yellow;
        height: 47px;
    }

    .img-back {
        margin-left: 10px;
        margin-top: 5px;
    }
    /*---------*/
    .edit-title{
        width: 80%;text-align: center;margin-top: 5px;
    }
    .edit-title span{
        color: #ffffff!important;
        line-height: 33px;
    }

    .edit-form-button{
        width: 90%;
        margin-left: 5%!important;
        margin-bottom: 5px!important;
        border-radius: 5px;
    }

    .shoadowmy {
        width: 100%;
        height: auto;
        position: fixed;
        box-shadow: 0 0 0 100vmax rgba(200, 200, 200, .3);
        z-index: 1000;
    }

    .select-div {
        position: fixed;
        left: 4%;
        bottom: 0px;
        width: 90%;
        height: 400px;
        margin-bottom: 100px;
        background-color: white;
        z-index: 9999;
        color: white;
        font-size: 18px;
        border-radius: 5px;
        text-align: center;
        margin-left: 1%;
    }
    .select-div .sel-li1 {
        clear: both;
        font-size: 18px;
        line-height: 35px;
        color: black;
    }

    .select-div .sel-li {
        clear: both;
        height: 325px;
        font-size: 18px;
        border-top: 1px solid #E5E5E5;
        color: royalblue;
        width: 90%;
        margin-left: 5%;
    }
    .select-div .sel-button {
        clear: both;
        font-size: 18px;
        border-top: 1px solid #E5E5E5;
        line-height: 35px;
        color: royalblue;
    }
</style>


    <div class="topPath horizontal-menu-div" style="background: #F8B708;width: 100%; ">
        <ul class="horizontal-ul">
            <li id="gohomeBtn" style="margin-top: 5px;">
                <img class="img-back" src="/static/images/leftback-icon.png">
            </li>
            <li class="edit-title">
                <span>寻物启事信息发布</span>
            </li>

        </ul>
    </div>

    <div id="shadowDivSel" class="shoadowmy" style="display:none;">
        <div class="select-div">
            <div class="sel-li1"><span>中奥物业社区公约</span></div>
            <div class="sel-li" ><span style="color: #000000e3;font-size: 15px;"><?= $treaty?></span></div>
            <div class="sel-button" id="shadowDivOkBtn" ><span>确定</span></div>
        </div>

    </div>



    <div class="panel" id="notices-form">
<?php \components\za\ActiveForm::begin(); ?>

    <div class="pure-form">
<?= Html::hiddenInput($_model->formName() . '[id]', $_model->id) ?>
    <label> </label>
    <div class="form-group">
        <?= Html::textInput(  $_model->formName() .'[title]', $_model->title, ['placeholder' => '请填写标题', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>标题</label>
    </div>
    <div class="form-group">
        <?= Html::textInput(  $_model->formName() .'[lose_address]', empty($_model->lose_address)?'':$_model->lose_address, ['placeholder' => '请填写丢失地点', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>丢失地点</label>
    </div>

    <label><span style="color: #ff2222">*</span>详细描述</label>
    <div class="form-group" style=" ">
        <?= Html::textarea(  $_model->formName() .'[describtions]', empty($_model->describtions)?'':$_model->describtions, ['placeholder' => '','data-required' => true,]) ?>
    </div>
    <div class="form-group">
        <?= Html::textInput(  $_model->formName() .'[linkman]', empty($_model->linkman)?'':$_model->linkman, ['placeholder' => '请填写联系人', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>联系人</label>
    </div>
    <div class="form-group">
        <?= Html::textInput(  $_model->formName() .'[tel]', empty($_user->phone)?'':$_user->phone, ['placeholder' => '请填写联系电话', 'data-required' => true]) ?>
        <label><span style="color: #ff2222">*</span>联系电话</label>
    </div>

    <div class="form-group">
        <div>
            <label>上传相关图片</label>
            <?= \components\inTemplate\widgets\Html::hiddenInput(  $_model->formName() .'[pics]', $_model->pics, ['id' => 'pics']) ?>
            <input type="file" id="upload" name="uploadfile" value="" style="display:none;"/>


            <ul id="upload-pics" class="pure-g upload-pics">
            <?php
            $pics = explode(',', $_model->pics);
            ?>

                <?php
                foreach ($pics as $pic):
                    if ($pic):
                        ?>

                        <li class="pure-u-1-4 uploaded-img">
                            <i class="mask" style="bottom: 0px; height: 121.94px;"></i>
                            <i class="del-icon" data-save="<?= Yii::getAlias($pic) ?>"></i>
                            <span data-save="<?= Yii::getAlias($pic) ?>" style="background-image:url(<?= Yii::getAlias($pic) ?>)"></span>
                        </li>


                    <?php
                    endif;
                endforeach;
                ?>







                <li class="pure-u-1-4 local-resize-btn"  onclick="upload.click()"><span ></span></li>
            </ul>
        </div>

        <div style="margin-top: 5px;">
            <span>请遵守</span><a id="showTreatyBtn" href="javascript:;" style="color:#082dfd">[中奥物业社区公约]</a><span>规则，不得违反国家法律法规</span>
        </div>
    </div>

    <div style="height: 6em;line-height: 6em;">

    </div>

    <button type="submit" class="btn btn-block btn-bottom-all btn-primary edit-form-button" data-disable="0" id="submitBtn">发布</button>
    <?php \components\za\ActiveForm::end(); ?>


    <?php
    \common\widgets\JavascriptBlock::begin();
    ?>

    <script>
        $(function () {
            $('#shadowDivOkBtn').on('click',function () {
                $('#shadowDivSel').hide();
            });
            $('#showTreatyBtn').on('click',function () {
                $('#shadowDivSel').show();
            });

            $("#gohomeBtn").on("click",function () {
                app.go('/search-notices/list');
            });

            $('.upload-pics').on('click','.del-icon',function(){

                delImageEx(this);
            });

            $('#notices-form').on('loaded', function () {
                var that = this;
                var siteData = 2;

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
                    var _dis = $('#submitBtn').attr("data-disable");
                    if(_dis == 0) {
                        if (confirm('是否要提交？')) {
                            if (app.formValidate(this)) {
                                $('#submitBtn').attr("data-disable",1);
                                var val = $(this).serialize();
                                app.showLoading();
                                $.post('/search-notices', val, function (res) {
                                    app.hideLoading();
                                    $('#submitBtn').attr("data-disable",0);
                                    if (res.code === 0) {
                                        if (res.data.goUrl) {
                                            app.go(res.data.goUrl);
                                            return;
                                        } else {
                                            app.go('/search-notices/list');
                                        }

                                    } else {
                                        app.tips().error(res.message);
                                    }
                                }, 'json');
                            }
                        }
                    }
                    return false;
                })
            });

            function delImage(){
                console.log($(this).attr('data-save'));
                delImageEx(this);
            }
            function delImageEx(_this){
                $(_this).parent().remove();
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