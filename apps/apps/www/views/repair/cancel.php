<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/10
 * Time: 11:11
 */
/* @var $model \common\models\Repair*/
?>

<div class="panel" id="new-repair-view">

    <div class="list-cell repair-cancel">
        <div class="info">
            <h4>
                取消报修单
            </h4>
        </div>
        <?php \components\za\ActiveForm::begin(['id' => 'cancel-form']); ?>
            <div class="more repair-cancel-mo">
                <p>取消原因：</p>
                <ul id="comment-list">
                    <li class="li_0">
                        <span class="checked"></span>
                        <span>速度太慢，不想等了</span>
                    </li>
                    <li class="li_1">
                        <span class="icon"></span>
                        <span>我自己修好了</span>
                    </li>
                    <li class="li_2">
                        <span class="icon"></span>
                        <span>我临时有事，改天再说</span>
                    </li>
                    <li class="li_3">
                        <span class="icon"></span>
                        <span>其他</span>
                    </li>
                </ul>
                <div class="text-area">
                    <textarea placeholder="请输入内容" name="RepairCancel[content]" id="" cols="30" rows="10" disabled></textarea>
                </div>
            </div>
            <input name="RepairCancel[id]" type="hidden" value="<?= $model->id ?>">
            <input name="RepairCancel[type]" id="RepairCancel-type" type="hidden" value="0">
        <?php \components\za\ActiveForm::end(); ?>
    </div>

    <div class="repair-cancel-footer">
        <button id="back" class="btn-block">返回</button>
        <button id="step" class="btn-block">确定</button>
    </div>

</div>

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">

    $(function (){
        $('#comment-list').on('click', 'li', function () {
            var cla = $(this).attr('class'), liCla='';
            if($(this).children('.icon').hasClass('icon')){
                $('#comment-list li').each(function (e){
                    liCla = $(this).attr('class');
                    if(cla == liCla){
                        $(this).children('.icon').removeClass('icon').addClass('checked');
                        $("#RepairCancel-type").val(e);
                        if(e == '3' && $('textarea').attr('disabled')){
                             $('textarea').attr({ disabled: false, required: true });
                        } else {
                            $('textarea').attr('disabled', true)
                        }
                    } else {
                        $(this).children('.checked').removeClass('checked').addClass('icon');
                    }
                });
            }
        });

        $('#back').click(function (){
            history.go(-1);
        });

        $('#step').on('click', function (){
            var _this = $('#cancel-form').serialize();
            $.post('/repair/cancel?id=<?= $model->id ?>', _this, function (res) {
                if (res.code === 0) {
                    app.go('/repair/list?status=4,1000');
                } else {
                    app.tips().error(res.message);
                }
            }, 'json')

        })

    });

</script>

<?php \common\widgets\JavascriptBlock::end();?>
