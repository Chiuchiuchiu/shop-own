<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/01/24
 * Time: 11:11
 *
 * @var integer $butlerId
 */
/* @var $model \common\models\MemberHouse */
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

                <div class="pure-form">
                    <label>请选择房屋信息</label>
                    <select style="min-height: 3em;width: 100%" name="id" id="choose-select-l" required="">
                        <option value="">请选择</option>

                        <?php if(!empty($model)){?>

                            <?php foreach($model as $house){?>
                                <option value="<?= $house->house_id ?>">
                                    <?= $house->house->ancestor_name ?>
                                </option>
                            <?php }?>

                        <?php }?>

                    </select>
                    <p style="font-size: 12px;" class="c-show-house-name"></p>
                </div>

                <div style="margin-top: 1em;font-size: 0.7em;color: #F7B500;">
                    <p>打星等级：<br />
                        1星：非常不满意；2星：不满意；3星：一般；4星：满意；5星：非常满意
                    </p>
                </div>

                <div class="repair-evaluation-mo Satisfaction" style="margin-top: 1em;">
                    <ul id="" class="evaluation-box">
                        <li class="li_0" id="">
                            <div class="">公共设施设备维护满意度：<span class="satisfaction-text sopf-sf">满意</span></div>
                            <div class="start-box">
                                <div class="start-clo-2">
                                    <input type="hidden" name="sopfSf" id="sopf-sf" value="4">
                                </div>
                                <div class="start-clo-8 starC" data-cl="sopf-sf">
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon"></a>
                                </div>
                            </div>
                        </li>
                        <li class="li_0" id="">
                            <div class="">报事报修管理满意度：<span class="satisfaction-text repair-sf">满意</span></div>
                            <div class="start-box">
                                <div class="start-clo-2">
                                    <input type="hidden" name="repairSf" id="repair-sf" value="4">
                                </div>
                                <div class="start-clo-8 starC" data-cl="repair-sf">
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon"></a>
                                </div>
                            </div>
                        </li>
                        <li class="li_0" id="">
                            <div class="">安全管理满意度：<span class="satisfaction-text sm-sf">满意</div>
                            <div class="start-box">
                                <div class="start-clo-2">
                                    <input type="hidden" name="smSf" id="sm-sf" value="4">
                                </div>
                                <div class="start-clo-8 starC" data-cl="sm-sf">
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon"></a>
                                </div>
                            </div>

                        </li>
                        <li class="li_0" id="">
                            <div class="">清洁绿化满意度：<span class="satisfaction-text cg-sf">满意</div>
                            <div class="start-box">
                                <div class="start-clo-2">
                                    <input type="hidden" name="cgSf" id="cg-sf" value="4">
                                </div>
                                <div class="start-clo-8 starC" data-cl="cg-sf">
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon"></a>
                                </div>
                            </div>

                        </li>
                        <li class="li_0" id="">
                            <div class="">管家服务满意度：<span class="satisfaction-text bs-sf">满意</div>
                            <div class="start-box">
                                <div class="start-clo-2">
                                    <input type="hidden" name="bsSf" id="bs-sf" value="4">
                                </div>
                                <div class="start-clo-8 starC" data-cl="bs-sf">
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon"></a>
                                </div>
                            </div>

                        </li>
                        <li class="li_0" id="">
                            <div class="">综合服务满意度：<span class="satisfaction-text os-sf">满意</div>
                            <div class="start-box">
                                <div class="start-clo-2">
                                    <input name="osSf" id="os-sf" type="hidden" value="4">
                                </div>
                                <div class="start-clo-8 starC" data-cl="os-sf">
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon-hover"></a>
                                    <a href="javascript:void(1)" class="start-ic star-icon"></a>
                                </div>
                            </div>

                        </li>

                        <li class="li_2">
                            <div class="d-left">客户意见：</div>
                            <div class="d-right">
                                <textarea placeholder="如需要填写意见，不可少于4个字符" name="content" id="content" cols="30" rows="7" maxlength="100" class="text-area"></textarea>
                            </div>
                        </li>
                    </ul>
                </div>

                <input name="butlerId" id="butlerId" type="hidden" value="<?= $butlerId ?>">
            <?php \components\za\ActiveForm::end(); ?>
        </div>

        <div class="repair-cancel-footer">
            <button style="width: 50%;background-color: #f8f8f8;" data-go="/?" class="btn-block">返回首页</button>
            <button style="width: 50%;" id="step" class="btn-block">提交</button>
        </div>

    </div>

    <div id="warning-tips">
        <div class="mask"></div>
        <div class="warning-content-box">
            <div class="warning-content"></div>
            <div class="warning-back-box">
                <a href="javascript:void(0);"></a>
                <a href="javascript:void(0);" class="closeTips">关闭</a>
            </div>
        </div>
    </div>

    <?php \common\widgets\JavascriptBlock::begin();?>

        <script type="text/javascript">
            var startArray = new Array('非常不满意', '不满意', '一般', '满意', '非常满意');

            $('#choose-select-l').change(function (){
                $('.c-show-house-name').html($(this).children('option:selected').html());
            });

            $('.starC').on('click', 'a', function () {
                var current_position = $(this).index();
                var cc = $(this).parent().attr('data-cl');

                $(this).removeClass('star-icon').addClass('star-icon-hover');
                $(this).prevAll().removeClass('star-icon-hover').addClass('star-icon-hover');
                $(this).nextAll().removeClass('star-icon-hover').addClass('star-icon');

                $('.'+cc).html(startArray[current_position]);

                $("#"+cc).val(current_position+1);
            });

            $("#step").on('click', function (){
                var houseId = $('#choose-select-l option:selected').val();
                var satisfaction = parseInt($('#satisfaction').val());
                var content = trim($('#content').val());
                $('#content').val(content);

                if(houseId.length < 1){
                    $('.warning-content').html('请选择房产');
                    $('#warning-tips').show();
                    return false;
                }

                if(satisfaction < 3 && content.length < 4){
                    $('.warning-content').html('由于您选择的满意度低于 3星，麻烦填写意见进行提交，不可少于4个字符，谢谢！');
                    $('#warning-tips').show();
                    return false;
                }

                submitC('#satisfaction-form');

            });

            var submitC = function (formId){
                var val = $(formId).serialize();
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: val,
                    success: function (res){
                        if(res.code === 0){
                            app.go(res.data.goUrl);
                        } else {
                            $('.warning-content').html(res.message);
                            $('#warning-tips').show();
                        }

                    },
                    dataType: 'json'
                });
            };

            $('.closeTips').on('click', function (){
                $('#warning-tips').hide();
            });

            function trim(str){
                return str.replace(/(^\s*)|(\s*$)/g, "");
            }

            /*wx.ready(function (){
                wx.hideAllNonBaseMenuItem();
            });*/

        </script>

    <?php \common\widgets\JavascriptBlock::end();?>

<?php } ?>