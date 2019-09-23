<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;
use \components\inTemplate\widgets\IBox;


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array*/
/* @var $projects array*/
/* @var $adlist array*/


IBox::begin();
$form = ActiveForm::begin();

echo $form->field($model, 'title')->textInput(['maxlength' => true]);
?>
<style>

    .choose-goods-main {
        width: 440px;
        height: 403px;
        border: 1px solid #e5e5e5;
        border-radius: 2px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        -ms-border-radius: 2px;
        -o-border-radius: 2px;
        display: inline-block;
        float: left;
    }
    .choose-goods-title {
        line-height: 32px;
        padding: 0 20px;
        border-bottom: 1px solid #e5e5e5;
        position: relative;
    }
    .choose-goods-int {
        margin: 5px 10px;
        /*border: 1px solid #e5e5e5;*/
        border-radius: 2px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        -ms-border-radius: 2px;
        -o-border-radius: 2px;
        line-height: 28px;
        /*height: 28px;*/
        background-color: #fff;
        position: relative;
    }
    .choose-goods-list {
        height: 324px;
        overflow: auto;
        padding: 0;
    }
    ul, ol {
        list-style: none;
    }
    .choose-goods-list-inp {
        position: relative;
        width: 40px;
        height: 45px;
        float: left;
        line-height: 45px;
        text-align: center;
    }
    .choose-goods-list-inp > input[type='checkbox'] {
        position: relative;
        top: 15px;
    }
    input[type=checkbox], input[type=radio] {
        margin: 4px 0 0;
        margin-top: 1px\9;
        line-height: normal;
    }
    input[type=checkbox], input[type=radio] {
        box-sizing: border-box;
        padding: 0;
    }

    .checkclass {
        z-index: 10;
        width: 20px;
        height: 20px;
        position: relative;
        top: 35px;
        opacity: 0;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
        filter: alpha(opacity=0);
        cursor: pointer;
    }
    .allchecked {
        position: absolute;
        top: -23px;
        left: 10px;
        z-index: 2;
        cursor: pointer;
        width: 20px;
        height: 20px;
        float: left;
        cursor: pointer;
        margin-left: 10px;
        text-align: center;
        background-image: url(/static/images/checkbox_01.gif);
        background-repeat: no-repeat;
        background-position: 0 0;
        margin-top: 30px;
        cursor: pointer;
    }
    .check {
        background-position: 0 -21px!important;
    }.choose-goods-main
    .choose-goods-title > input[type='checkbox'] {
        margin-right: 11px;
        position: relative;
        top: 5px;
    }
    label {
        font-weight: 400;
    }
    .clearfix:after, .clearfix:before {
        content: " ";
        display: table;
    }
/*---------------------*/
    .choose-goods-list-inp > input[type='checkbox'] {
        position: relative;
        top: 15px;
    }
    input[type=checkbox], input[type=radio] {
        margin: 4px 0 0;
        margin-top: 1px\9;
        line-height: normal;
    }
    input[type=checkbox], input[type=radio] {
        box-sizing: border-box;
        padding: 0;
    }
    .piaochecked {
        position: absolute;
        top: -15px;
        left: 0px;
        z-index: 2;
        cursor: pointer;
        width: 20px;
        height: 20px;
        float: left;
        cursor: pointer;
        margin-left: 10px;
        text-align: center;
        background-image: url(/static/images/checkbox_01.gif);
        background-repeat: no-repeat;
        background-position: 0 0;
        margin-top: 30px;
        cursor: pointer;
    }

    label {
        font-weight: 400;
    }
    /*--------项目---------*/
    .checker{
        display: inline-block;
        margin-right: 10px;
    }
    #seachWh{
        width: 100%;
    }
    #seachBtn img{
        position: absolute;
        top: 8px;
        right: 14px;
    }
    .two-line{
        margin-top: 15px;
    }
    .col-sm-6{
        width: 60%;
        padding-left: 0;
    }
    #li_1{
        display: inline-block;
        height:30px;
        width: 100%;
    }
    .my_pocite .col-sm-3{
        margin-top: 10px;
    }

    .btn-preview {
        background-color: #99d1f5;
        border-color: #99d1f5;
        color: #FFF;
    }
/*-------------*/
    .col-sm-3, .col-sm-4, .col-sm-6, .col-sm-9, .col-sm-12{
        padding-left: 0px!important;
    }
    /*.col-sm-3{*/
        /*padding-top: 12px!important;*/
    /*}*/
    .col-sm-4{
        line-height: 0px!important;
    }
</style>


<?php
    if($model['type'] == 2){
        $url_show = "none";
    }else{
        $url_show = "block";
    }

?>

<div class="form-group field-banner-url required" id="url_div" style="display: <?= $url_show?>;">
    <label class="control-label col-sm-3" for="banner-url">自定义URL</label>
    <div class="col-sm-6">
        <input type="text" id="banner-url" class="form-control" name="Ad[url]" value="<?= $model->url; ?>">
        <div class="help-block help-block-error "></div>
    </div>
    <span style="color: red">提示：手动输入完整地址</span>
<!--    <div class="col-sm-3">
        <?/*= Html::button('生成地址', ['class' => 'btn btn-success urlRule']) */?>
    </div>-->
</div>

<div class="form-group field-banner-url required">
    <label class="control-label col-sm-3" for="banner-url">广告类型</label>
    <div class="col-sm-9" style="padding-left: 0px;">
        <div class="col-sm-12" style="width: 300px;">
            <select class="form-control type-select"  name="Ad[type]" id="ad_type">
                <option value="">-选择广告位-</option>
                <option value="1"    <?php if($model->type==1) echo 'selected'; ?>  >【欢迎页面】广告位</option>
                <option value="2"    <?php if($model->type==2) echo 'selected'; ?>  >【业主中心】广告位</option>
                <option value="3"    <?php if($model->type==3) echo 'selected'; ?>  >【商城聚焦】广告位</option>
                <option value="4"    <?php if($model->type==4) echo 'selected'; ?>  >【物业缴费】广告位</option>
                <option value="5"    <?php if($model->type==5) echo 'selected'; ?>  >【报事报修】广告位</option>
            </select>
        </div>
    </div>
</div>

<div class="form-group field-banner-url required" id="adTemplate">
    <label class="control-label col-sm-3" for="banner-url">广告模块</label>
    <input type="hidden" value="<?= $model['diy_json'] ?>" name="Ad[diy_json]" />
    <div class="col-sm-9" style="padding-left: 0px;">
        <div class="col-sm-12" style="width: 300px;">
            <select class="form-control type-select" id="templateId" name="Ad[template_id]">
                <option value="">-选择模块-</option>
                <?php foreach($adlist as $k => $v): ?>
                    <option value="<?= $v['id'] ?>" <?php if($model->template_id==$v['id']) echo 'selected'; ?> ><?= $v['title'] ?></option>
                <?php endforeach; ?>

            </select>

        </div> <button type="submit" class="btn btn-preview" id="btn-preview">预览</button>
    </div>
</div>

<div class="form-group field-banner-url required">
    <label class="control-label col-sm-3" for="banner-url" style="padding-top: 12px;">广告时间</label>
    <div class="col-sm-9" style="padding-left: 0px;">

        <div class="col-sm-4">
            <label for="">&nbsp;</label>
            <?= \kartik\date\DatePicker::widget([
                'model' => $dateTime,
                'attribute' => 'startDate',
                'attribute2' => 'endDate',
                'options' => ['placeholder' => '开始时间'],
                'options2' => ['placeholder' => '结束时间'],
                'type' => \kartik\date\DatePicker::TYPE_RANGE,
                'language' => 'zh-CN',
                'separator' => "至",
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-m-d'
                ]
            ]) ?>
        </div>
    </div>
</div>

<input type="hidden" id="type" value="<?= $model->type ?>" />
<?= $form->field($model, 'sort')->textInput(['placeholder' => "排序越小，排名越前"]) ?>

    <?= $form->ajaxUpload($model, 'file', 'pic', 'pic', '图片'); ?>

<!----------------项目-------------------->


<div class="form-group field-banner-url required my_pocite">
    <label class="control-label col-sm-3" for="banner-url" style="margin-top: 0;">所属项目</label>
    <div class="col-sm-9" style="padding-left: 0px;">
        <div class="goods-body-left choose-goods-main">
            <div class="choose-goods-title">
                <label class="allchecked"></label>
                <input type="checkbox" name="leftAll" data-type="left" id="chkall" class="allcheckclass"/>
                全选（<i style="color:red" id="left_count">0</i> 项）
            </div>
            <div class="choose-goods-int">
                <input type="text" placeholder="请输入项目名称" id="seachWh">
                <span id="seachBtn"><img src="/static/images/search-icon.png"></span>
            </div>
            <ul class="choose-goods-list">

                <?= $form->field($model, 'projects')->checkboxList(\yii\helpers\ArrayHelper::getColumn($projects,'house_name'), [
                    'item' => function($index, $label, $name, $checked, $value) use ($model) {
                        $id = $value;

                        $checkStr = in_array($value, explode(',', trim($model->projects, ','))) ? "checked" : "";
                        $checkStr2 = in_array($value, explode(',', trim($model->projects, ','))) ? "check" : "";
                        $res = '<li class="clearfix" id="li_'.$id .'" data-name="'.$label.'">'
                            .'<span class="choose-goods-list-inp"><label class="piaochecked '.$checkStr2.'"></label>'
                            .'<input type="checkbox" name="Ad[projects][]" value="'.$value.'" '.$checkStr.' class="checkclass"></span>'
                            .'<ul class="choose-goods-list-ul">'
                            .'<li class="two-line">'.$label.'</li></ul></li>';
                        return $res;
                    }
                ]); ?>
            </ul>
        </div>
    </div>
</div>

<!-----------------END------------------------>

<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>
<?php
ActiveForm::end();
IBox::end();
?>
<div class="rule-modal" style="display: none;">
    <div class="row">
        <div class="ibox-content">
            <div class="form-group" style="margin-bottom: 80px;">
                <label class="col-sm-3">选择规则</label>
                <div class="col-sm-9">
                    <div class="col-sm-12">
                        <?php
                        $model = new \common\models\Ad();
                        echo Html::dropDownList('', null,
                            [''=>"全部", \common\models\Ad::TYPE_1 => "商城首页", \common\models\Ad::TYPE_2 => "商城详情页"],
                            ['class' => 'form-control type-select'])
                        ?>
                        <?php foreach(\common\models\Ad::typeMap() as $k => $v): ?>
                            <div class="help-block help-block-error url-map type<?= $k ?>" style="display: none">
                                <?= $v['url'] ?>
                                <?php if($k == 1): ?>
                                    <br><span class="url-map type<?= $k ?>">参数描述：sid=商铺id；</span>
                                <?php elseif($k == 2): ?>
                                    <br><span class="url-map type<?= $k ?>">参数描述：sid=商铺id；id=商品id；</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 160px;">
                <label class="col-sm-3">参数</label>
                <div class="col-sm-9">
                    <input class="form-control params" placeholder="每个%s表示一个参数，多个参数用竖线“|”分隔" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12" style="text-align: center;">
                    <button class="btn btn-success create-url">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php \common\widgets\JavascriptBlock::begin(); ?>
<script>
    $(function(){
        console.log("211212");

        $("#btn-preview").on('click',function () {
            var _tempid = $("#templateId").val();
            if(parseInt(_tempid) >0){
                var _url = "https://shop.51homemoney.com/admin/showTemplate/index/preview/1/pid/"+_tempid+"#&home";
                window.open(_url);
            }else{
                layer.msg("请选择广告模板");
            }
        });
        var _type = $("#type").val();
        initDiv(_type);
        function initDiv(_type){
            if(parseInt(_type) == 2){
                $("#adTemplate").show();
                $("#pic_div").hide();
                $("#url_div").hide();
            }else{
                $("#adTemplate").hide();
                $("#pic_div").show();
                $("#url_div").show();
            }
        }

        $("#ad_type").change(function(){
            var _type=$("#ad_type").val();
            initDiv(_type);
        });


        $('.allchecked').on('click',function(){
            var _this = $(this);

            if((_this).hasClass("check")){
                _this.removeClass('check');
                $("#chkall").prop('checked',false);
                $('input[name="Ad[projects][]"]').prop('checked',false);
                $('input[name="Ad[projects][]"]').siblings('.piaochecked').removeClass('check');
            }else{
                _this.addClass('check');
                $("#chkall").prop('checked',true);
                $('input[name="Ad[projects][]"]').prop('checked',true);
                $('input[name="Ad[projects][]"]').siblings('.piaochecked').addClass('check');
            }
            updateAll();
        });

        $('input[name="Ad[projects][]"]').on('click',function(){
           var _this = $(this);
            var _isCheck = _this.is(':checked');
            if(!_isCheck){
                _this.prop('checked',false);
                _this.siblings('.piaochecked').removeClass('check');
            }else{
                _this.prop('checked',true);
                _this.siblings('.piaochecked').addClass('check');
            }

            updateAll();
        });
        updateAll();
        function updateAll(){
            var _all = 0;
            var _sel = 0;
            $(".clearfix  input[name='Ad[projects][]']").each(function(){
                var _this = $(this);
                var _isCheck = _this.is(':checked');
                if(_isCheck){
                    _sel = parseInt(_sel) + 1;
                }
                _all = parseInt(_all) + 1;
            });
            if(parseInt(_sel) == parseInt(_all)){
                $(".allchecked").removeClass('check').addClass('check');
                $("input[name='leftAll']").prop('checked',true);
            }else{
                $(".allchecked").removeClass('check');
                $("input[name='leftAll']").prop('checked',false);
            }


            $("#left_count").html(_sel.toString() + "/" + _all.toString());
        }

        //--
        $('#seachBtn').on('click',function(){
            seachPara();
        });
        function seachPara(){
            var _kw = $("#seachWh").val();
            if(_kw != "" ){
                $(".clearfix").each(function(){
                    var _this = $(this);

                    var _name = _this.attr("data-name");
                    if(_name.indexOf(_kw)>=0){
                        _this.show();
                    }else{
                        _this.hide();
                    }
                });
            }else{
                $(".clearfix").each(function(){
                    var _this = $(this);
                    _this.show();
                });
            }

        }


        $('.urlRule').click(function () {
            layer.open({
                type: 1,
                skin: 'layui-layer-rim', //加上边框
                area: ['700px', '420px'], //宽高
                content: $('.rule-modal')
            });
        });

        $(".type-select").on('change',function(){

            $('.url-map').hide();

            $(".type" + $(this).val()).show();
        });

        $('.create-url').click(function(){

            var type_num = $(".type-select option:selected").val();
            var url = $(".type" + type_num).text();
            var params = $('.params').val();

            $.ajax({
                type: 'POST',
                dataType: "json",
                url: '/banner/create-url-ajax',
                data: {url:url, params:params, type_num:type_num},
                success:function(res){

                    if(res.code == 0){
                        $("#banner-url").val(res.data.url);

                        $('.params').val('');
                        $('.url-map').hide();
                        $(".type-select").val("");

                        layer.closeAll();
                    }else{
                        layer.msg(res.message);
                    }
                }
            })
        })
    })

    $('#ad-projects > label > input').click(function(){

        var input = $(this);

        if(input.val() == '0'){
            if(input.is(":checked")){
                $("input[name='Ad[projects][]']").prop("checked",true);
            }else{
                $("input[name='Ad[projects][]']").prop("checked",false);
            }
        }else{
            $('#banner-projects > label > input[value=0]').prop("checked",false);
        }
    })
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>
