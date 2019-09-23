<?php

use yii\helpers\Html;
use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Butler */
/* @var array $projectsArray */

\components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(); ?>

<?= $form->field($model, 'nickname')->textInput() ?>
<?= $form->field($model, 'status')->dropDownList(\common\models\Butler::statusMap()) ?>
<?= Html::hiddenInput($model->formName() . '[regions]',$model->regions, ['id' => 'regionIds']) ?>

<div class="form-group">
    <label class="control-label col-sm-3">项目</label>
    <div class="col-sm-7">
        <div class="row">
            <div class="col-sm-4">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => $model->formName() . '[project_house_id]',
                    'value' => $model->project_house_id,
                    'items' => $projectsArray,
                    'addClass' => 'c-project'
                ])?>

            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3">管辖区域</label>
    <div class="col-sm-7">
        <div id="region_ctr" class="row">
            <div class="col-sm-4 sl ctr-template">
                <?php
                echo \components\inTemplate\widgets\Html::dropDownList('', null, [
                    '加载中...'
                ], ['readonly' => true, 'class' => 'form-control m-b load-pro'])
                ?>
            </div>
        </div>
        <ul class="unstyled" style="padding-left: 0" id="region_show">
            <?php
            foreach ($model->regionData as $value):?>
                <li class="clear" data-id="<?= $value->house_id ?>"><?= $value->ancestor_name ?><a
                        class="pull-right btn remove text-danger btn-xs">删除</a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-sm-2">
        <button type="button" id="add_region" class="btn btn-success">添加</button>
    </div>
</div>


<div class="form-group text-center">
    <?= Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
$this->registerCss(<<<CSS
.ctr-template{
display:none;
}
CSS
);
?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script>

    function createDropDownList(parent_id) {
        let projV = $('.c-project').val();

        var o = $('.ctr-template').clone();
        o.removeClass('ctr-template');
        $('#region_ctr').append(o);
        let s = $('select',o);
        s.bind('change',function(){
            //remove right
            o.siblings('.sl').slice(o.index()).remove();
            if(s.val()!='')
                createDropDownList(s.val())
        });
        $.getJSON('/house/query-child',{id:parent_id},function(res) {
            if(res.code==0){

                s.html('');
                if(res.data.list.length>0){
                    $('<option value="">请选择</option>').appendTo(s)
                    $.each(res.data.list,function(i,e){
                        $('<option value="'+e.house_id+'">'+e.house_name+'</option>').appendTo(s)
                    });
                    s.attr('readonly',false);

                    if(parent_id < 1){
                        let obj = $('.load-pro option[value="'+ projV +'"]');
                        $(obj).attr('selected', true).trigger('change');
                    }

                }else{
                    s.remove();
                }
            }else{
                toastr.error(res.message?res.message:res)
            }
        });
    }
    createDropDownList(0);
    //添加按钮实现
    $('#add_region').bind('click',function(){
        var id= $('#region_ctr select:last').val();
        if(id<1){
            id = $('#region_ctr select').eq($('#region_ctr select').length-2).val();
        }
        if(id<1){
            toastr.error("请选择区域");
            return ;
        }
        var s = '';
        $('#region_ctr select').not('.ctr-template select').each(function(i,e){
            if(s) s+='->';
            s+=$('option:selected',e).html();
            if($(e).val()==id) return false;
        })
        var rm = $('<a class="pull-right btn remove text-danger btn-xs">删除</a>');
        rm.bind('click',function(){
            $(this).parent().remove();
            updateRegionData();
        })
        $('<li/>').html(s).attr('data-id',id)
            .append(rm)
            .appendTo($('#region_show'))
        updateRegionData();
        /*$('#region_ctr>div:not(.ctr-template)').remove();
        createDropDownList(0);*/
    });
    $('#region_show li a').bind('click',function(){
        $(this).parent().remove();
        updateRegionData();
    })
    function updateRegionData(){
        var d = [];
        $('#region_show li').each(function(i,e){
            d.push($(e).attr('data-id'))
        });
        $('#regionIds').val(d.join(','));
    }

    $('.c-project').on('change', function (){
        let projV = $(this).val();
        let obj = $('.load-pro option[value="'+ projV +'"]');

        $(obj).attr('selected', true).trigger('change');

    }).trigger('change');

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>

