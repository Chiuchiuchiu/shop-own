<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/10
 * Time: 11:11
 */
/* @var $model \common\models\Repair*/
?>

<?php if(empty($model)) {?>
    <div class="empty-status"><i></i>暂无相关内容</div>
<?php } else { ?>

    <div class="panel" id="new-repair-view">

        <div class="list-cell repair-cancel">
            <div class="info">
                <h4>
                    评价服务
                </h4>
            </div>
            <?php \components\za\ActiveForm::begin(['id' => 'satisfaction-form']); ?>
                <div class="more repair-evaluation-mo">
                    <ul id="evaluation-list">
                        <li class="li_0" id="Satisfaction">
                            <div class="d-left">满意度：</div>
                            <div class="d-right">
                                <ul id="sati-list">
                                    <li class="li_0"><span class="star-icon-hover"></span></li>
                                    <li class="li_1"><span class="star-icon-hover"></span></li>
                                    <li class="li_2"><span class="star-icon-hover"></span></li>
                                    <li class="li_3"><span class="star-icon"></span></li>
                                    <li class="li_4"><span class="star-icon"></span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="li_1">
                            <div class="d-left">及时性：</div>
                            <div class="d-right timely">
                                <ul id="timely-li">
                                    <li class="li_0"><span class="radio-hover" for=""></span><span>及时</span></li>
                                    <li class="li_1"><span class="icon" for=""></span><span>一般</span></li>
                                    <li class="li_2"><span class="icon" for=""></span><span>不及时</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="li_2">
                            <div class="d-left">客户意见：</div>
                            <div class="d-right">
                                <textarea name="RepairCustomerEvaluation[customer_idea]" id="" cols="30" rows="3"></textarea>
                            </div>
                        </li>
                    </ul>

                </div>
                <input name="RepairCustomerEvaluation[id]" type="hidden" value="<?= $model->id ?>">
                <input name="RepairCustomerEvaluation[satisfaction]" id="s-type" type="hidden" value="3">
                <input name="RepairCustomerEvaluation[timeliness]" id="timeliness" type="hidden" value="1">
            <?php \components\za\ActiveForm::end(); ?>
        </div>

        <div class="repair-cancel-footer">
            <button id="back" class="btn-block">返回</button>
            <button id="step" class="btn-block">确定</button>
        </div>

    </div>
    <script src="/static/js/layer_mobile/layer.js"></script>

    <?php \common\widgets\JavascriptBlock::begin();?>

        <script type="text/javascript">

            $(function (){

                $('#sati-list').on('click', 'li', function () {
                    var current_position = $(this).index();
                    $('#sati-list li').each(function (e){
                        if(e <= current_position){
                            $(this).children('.star-icon').removeClass('star-icon').addClass('star-icon-hover');
                        } else {
                            $(this).children('.star-icon-hover').removeClass('star-icon-hover').addClass('star-icon');
                        }
                    });
                    $("#s-type").val(current_position+1);
                });

                $('#timely-li').on('click', 'li', function () {
                    var cla = $(this).attr('class'), current_position = $(this).index();
                    var liCla = 'li_' + $(this).index();
                    if(liCla == cla){
                        $(this).children('.icon').removeClass('icon').addClass('radio-hover');
                    }
                    $(this).siblings().children('.radio-hover').removeClass('radio-hover').addClass('icon');
                    $("#timeliness").val(current_position+1);
                });

                $('#back').click(function (){
                    history.go(-1);
                });

                $('#step').on('click', function (){

                    var ii = layer.open({
                        type: 2,
                        content: '提交中...',
                        shadeClose: false,
                        time:30
                    });

                    var _this = $('#satisfaction-form').serialize();
                    $.post('/repair/customer-evaluation?id=<?= $model->id ?>', _this, function (res) {
                        if (res.code === 0) {
                            app.go('/repair/list?status=3000');
                        } else {
                            layer.close(ii);
                            app.tips().error(res.message);
                        }
                    }, 'json')
                    .error(function(){
                        layer.close(ii);
                        layer.open({
                            content: '服务器异常，请通知管理员',
                            skin: 'msg',
                            time: 2 //2秒后自动关闭
                        })
                    })
                })

            });

        </script>

    <?php \common\widgets\JavascriptBlock::end();?>

<?php } ?>