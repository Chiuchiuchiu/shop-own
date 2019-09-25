<?php
/**
 * @var $this \yii\web\View
 * @var $group integer
 * @var $_user \apps\www\models\Member
 * @var $_project \common\models\Project
 * @var $projectList array
 */
use components\za\Html;
?>
    <div class="panel" id="auth-step-1">
        <?php
            \components\za\ActiveForm::begin(['id' => 'auth-1',
                'enableAjaxValidation' => true,
                'validationUrl' => \yii\helpers\Url::toRoute(['auth/index'])
            ]);
        ?>
        <div class="pure-form">
            <?= Html::hiddenInput("identity", null, ['data-required' => true, 'data-label' => '身份']) ?>
            <label>我是</label>
            <div class="identity-choice">
                <div data-val="1">
                    <div>业主</div>
                    <i></i>
                </div>
                <div data-val="2">
                    <div>租户</div>
                    <i></i>
                </div>
                <div data-val="3">
                    <div>家庭成员</div>
                    <i></i>
                </div>
            </div>
            <label>请选择房屋信息</label>
            <?php echo Html::hiddenInput('houseId', null, ['id' => 'house_id']); ?>
            <div class="form-group">
                <?php
                echo \components\za\Html::dropDownList("", $_project?$_project->house_id:$project_id,
                    $projectList
                    , ['id' => 'project', 'data-required' => true]);
                ?>
                <label>小区</label>
            </div>
            <div id="houseChoose">

            </div>
            <button class="btn btn-block btn-bottom-all btn-primary">下一步</button>
        </div>
        <input type="hidden" class="fullName" value="<?= $projectList[$projectId]; ?>"/>
        <?php
        \components\za\ActiveForm::end();
        ?>
    </div>
<?php
\common\widgets\JavascriptBlock::begin();
?>
    <script>
        $(function () {
            $('#auth-step-1').on('loaded', function () {
                var that = this;
                $('#project', that).on('change', function () {
                    $('#ui-loading').show();
                    $.getJSON('/house/query', {houseId: $(this).val(),group:'<?=$group?>'}, function (res) {
                        $('#ui-loading').hide();
                        if(res.code==0){
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
                        }
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
                    
                    var iParent = i*1 - 1;
                    var fullName = $('.fullName').val();
                    fullName = fullName + $('#houseChoose select[data-i='+ iParent +'] option:selected').text();
                    $('.fullName').val(fullName);

                    $('#ui-loading').show();
                    var houseId=$(this).val();
                    $.getJSON('/house/query', {houseId: houseId,group:'<?=$group?>'}, function (res) {
                        target.append($('<option/>').attr('value', '').html('请选择'));
                        $.each(res.data.list, function (houseId, row) {
                            target.append($('<option/>').attr('value', row.house_id).html(row.house_name));
                        });
                        if(res.data.list.length==0){
                            $('input[name=houseId]').val(houseId);
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

//                $('#houseChoose',that).on('change','select[data-last=1]', function () {
//                    $('input[name=houseId]').val($(this).val());
//                });

                $('.identity-choice>div').on('tap click', function () {
                    var val = $(this).attr('data-val');
                    $('input[name=identity]').val(val);
                    $(this).siblings().removeClass('hover');
                    $(this).addClass('hover');
                });
                $('.identity-choice>div:eq(0)').click();
                $('form').on('submit', function () {
                    if (app.formValidate(this)) {
                        var r = confirm("你确定认证“" + $('.fullName').val() + "”吗？");
                        if(r == true){
                            var val = $(this).serialize();
                            $.post('', val, function (res) {
                                if (res.code === 0) {
                                    if(res.data.goUrl){
                                        window.location.href = res.data.goUrl;
                                    }else{
                                        app.go('/auth/result?type=success')
                                    }
                                } else {
                                    app.tips().error(res.message);
                                }
                            }, 'json')
                        }


                    }
                    return false;
                })
            })
        });
    </script>
<?php
\common\widgets\JavascriptBlock::end();
?>