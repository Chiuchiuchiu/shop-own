<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\ActiveForm;
use \components\inTemplate\widgets\IBox;


/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $categoryMap array*/
/* @var $projects array*/


IBox::begin();
$form = ActiveForm::begin();

echo $form->field($model, 'title')->textInput(['maxlength' => true]);
?>

<div class="form-group field-banner-url required">
    <label class="control-label col-sm-3" for="banner-url">跳转地址</label>
    <div class="col-sm-6">
        <input type="text" id="banner-url" class="form-control" name="Banner[url]" value="<?= $model->url; ?>">
        <div class="help-block help-block-error "></div>
    </div>
    <div class="col-sm-3">
        <?= Html::button('生成地址', ['class' => 'btn btn-success urlRule']) ?>
    </div>
</div>
<?= $form->field($model, 'sort')->textInput(['placeholder' => "排序越大，排名越前"]) ?>
<?= $form->ajaxUpload($model, 'file', 'pic', 'pic', '图片'); ?>


<?= $form->field($model, 'projects')->checkboxList(\yii\helpers\ArrayHelper::getColumn($projects,'house_name'), [
    'item' => function($index, $label, $name, $checked, $value) use ($model){

            $checkStr = in_array($value, explode(',', trim($model->projects, ','))) ? "checked" : "";
            $res = "<label><input type='checkbox' name='Banner[projects][]' value='{$value}' {$checkStr}> {$label}</label>";
            $index % 3 == 2 && $res .= "<br>";

            return $res;
        }
]); ?>
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
                        $model = new \common\models\Banner();
                        echo Html::dropDownList('', null,
                            [''=>"全部", \common\models\Banner::TYPE_1 => "商城首页", \common\models\Banner::TYPE_2 => "商城详情页"],
                            ['class' => 'form-control type-select'])
                        ?>
                        <?php foreach(\common\models\Banner::typeMap() as $k => $v): ?>
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

    $('#banner-projects > label > input').click(function(){
        var input = $(this);
        if(input.val() == '0'){
            if(input.is(":checked")){
                $("input[name='Banner[projects][]']").prop("checked",true);
            }else{
                $("input[name='Banner[projects][]']").prop("checked",false);
            }
        }else{
            $('#banner-projects > label > input[value=0]').prop("checked",false);
        }
    })
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>
