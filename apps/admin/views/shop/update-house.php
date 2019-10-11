<?php
use components\inTemplate\widgets\ActiveForm;
use common\models\House;
use components\inTemplate\widgets\Html;

/**
 * @var $this yii\web\View
 * @var $model House
 * @var $houseRelevanceModel \common\models\HouseRelevance
 */
$this->title = '编辑 ' . $model->ancestor_name;
$this->params['breadcrumbs'][] = ['label' => '项目', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget([
    'name' => '返回楼盘列表',
    'option'=>[
        'class'=>'btn btn-w-m btn-white pull-left'
    ]
]);
?>



<?php \components\inTemplate\widgets\IBox::begin();

$form = ActiveForm::begin(['layout' => 'horizontal']);
echo $form->field($model, 'house_name')->textInput(['readonly' => true]);
echo $form->field($model, 'ancestor_name')->textInput(['readonly' => true]);
echo $form->field($model, 'house_alias_name')->textInput(['maxlength' => true]);
echo $form->field($model, 'ordering')->textInput();
echo $form->field($model, 'show_status')->radioList([
        '0' => '是',
        '1' => '否',
]);

/*echo $form->field($houseRelevanceModel, 'with_house_id')->hiddenInput(['id' => 'relevance'])->label(false);
echo $form->field($houseRelevanceModel, 'house_id')->hiddenInput(['value' => $model->house_id])->label(false);
echo $form->field($houseRelevanceModel, 'id')->hiddenInput()->label(false);*/

echo Html::activeHiddenInput($houseRelevanceModel, 'with_house_id', ['id' => 'relevance']);
echo Html::activeHiddenInput($houseRelevanceModel, 'house_id', ['value' => $model->house_id]);
echo Html::activeHiddenInput($houseRelevanceModel,'id');

?>

<input type="hidden" id="" name="origin[HouseId]" value="<?= $model->house_id ?>" />
<input type="hidden" id="" name="origin[WithHouseId]" value="<?= $houseRelevanceModel->with_house_id ?>" />

<?php
    if($model->level > 3) {
        ?>

        <div class="form-group">
            <label class="control-label col-sm-3">需要关联的数据</label>
            <div class="col-sm-7">
                <div id="region_ctr" class="row">
                    <div class="col-sm-4 sl ctr-template">
                        <?php
                        echo \components\inTemplate\widgets\Html::dropDownList('', null, [
                            '加载中...'
                        ], ['readonly' => true])
                        ?>
                    </div>
                </div>
                <ul class="unstyled" style="padding-left: 0" id="relevance_show">
                    <?php if ($houseRelevanceModel->houseName) {
                        ?>
                        <li class="clear"
                            data-id="<?= $houseRelevanceModel->houseName->house_id ?>"><?= $houseRelevanceModel->houseName->ancestor_name ?>
                            <a
                                    class="pull-right btn remove text-danger btn-xs">删除</a></li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col-sm-2">
                <button type="button" id="add_region" class="btn btn-success">添加</button>
            </div>
        </div>
        <?php
    }
?>
    <div class="row">
        <div class="form-group">
            <div class="text-center">
                <?= \yii\bootstrap\Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
                <?= \yii\bootstrap\Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
            </div>
        </div>
    </div>

<?php

    ActiveForm::end();
    \components\inTemplate\widgets\IBox::end();
?>

<?php
$this->registerCss(<<<CSS
.ctr-template{
display:none;
}
CSS
);
?>

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">
    $(function(){
        var initProjectId = <?= $model->project_house_id;?>;
        function createDropDownList(parent_id) {
            var o = $('.ctr-template').clone();
            o.removeClass('ctr-template');
            $('#region_ctr').append(o);
            var s = $('select',o);
            s.bind('change',function(){
                //remove right
                o.siblings('.sl').slice(o.index()).remove();
                if(s.val()!='')
                    createDropDownList(s.val())
            });
            $.getJSON('query-house-child',{id:parent_id},function(res) {
                if(res.code==0){
                    s.html('');
                    if(res.data.list.length>0){
                        $('<option value="">请选择</option>').appendTo(s)
                        $.each(res.data.list,function(i,e){
                            $('<option value="'+e.house_id+'">'+e.house_name+'</option>').appendTo(s)
                        })
                        s.attr('readonly',false)
                    }else{
                        s.remove();
                    }
                }else{
                    toastr.error(res.message?res.message:res)
                }
            })
        }
        createDropDownList(initProjectId);
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
            });
            var rm = $('<a class="pull-right btn remove text-danger btn-xs">删除</a>');
            rm.bind('click',function(){
                $(this).parent().remove();
                $('#add_region').show();
                updateRegionData();
            });
            cleanRelevanceData();

            $('<li/>').html(s).attr('data-id',id)
                .append(rm)
                .appendTo($('#relevance_show'));

            updateRegionData();
            $('#region_ctr>div:not(.ctr-template)').remove();
            createDropDownList(initProjectId);
//            $(this).hide();
        });
        $('#relevance_show li a').bind('click',function(){
            $(this).parent().remove();
            updateRegionData();
        });

        function updateRegionData(){
            var d = [];
            $('#relevance_show li').each(function(i,e){
                d.push($(e).attr('data-id'))
            });
            $('#relevance').val(d.join(','));
        }

        function cleanRelevanceData()
        {
            $('#relevance_show').html('');
            $('#relevance').val('');
        }
    });
</script>

<?php \common\widgets\JavascriptBlock::end();?>

