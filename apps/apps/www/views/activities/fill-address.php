<?php
/**
 * @var \yii\web\View $this
 * @var \common\models\Member $user
 * @var integer $project
 * @var integer $house_id
 * @var \common\models\ActivitiesCollectOrder $model
 */
use components\za\Html;

?>
    <div class="panel" id="activity-fill-address">

        <div class="address-title">
            领取端午礼品
        </div>

        <p class="ask-ad">您的收货地址</p>

        <?php \components\za\ActiveForm::begin(['action' => '/activities/save-address']); ?>
            <div class="pure-form">

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

                <?= Html::hiddenInput($model->formName() . '[house_id]', $house_id) ?>

                <?= Html::hiddenInput($model->formName() . '[member_house_id]', '', ['placeholder' => '请选择收货地址', 'data-required' => true, 'id' => 'member_house_id'])?>

            </div>

            <p class="ask-ad">您的个人信息</p>
            <div class="form-group user-info">
                <?= Html::textInput($model->formName() . '[user_name]', $user->showName, ['placeholder' => '请输入收货人姓名', 'data-required' => true]) ?>
                <label>收货人</label>
            </div>
            <div class="form-group user-info">
                <?= Html::textInput($model->formName() . '[tel]', $user->phone, ['placeholder' => '请输入联系人电话', 'data-required' => true]) ?>
                <label>联系人电话</label>
            </div>
            <div class="form-group user-info">
                <?= Html::textInput($model->formName() . '[comment]', '', ['placeholder' => '备注']) ?>
                <label>备注</label>
            </div>

        <button type="submit" class="btn btn-block btn-bottom-all btn-primary">提交</button>
        <?php \components\za\ActiveForm::end(); ?>
    </div>

    <div style="height: 6em;line-height: 6em;">

    </div>

    <div id="shade-div">
        <div class="mask"></div>
        <div class="c-panel">

        </div>
    </div>

<?php
\common\widgets\JavascriptBlock::begin();
?>

    <script>
        $(function () {
            $('#activity-fill-address').on('loaded', function () {
                var that = this;

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
                            $('input#member_house_id').val(houseId);
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

                $('form').on('submit', function () {
                    var houseId = $('#member_house_id').val();
                    if(confirm('是否要提交？')){
                        if(houseId.length < 1){
                            app.tips().error('请选择收货地址');
                            return false;
                        }
                        $('#shade-div').show();
                        var _d = $(this).serialize();
                        $.post('/activities/save-address', _d, function (res){
                            $('#shade-div').hide();

                            if(res.code == 0){
                                app.go('/activities/result');
                            }
                            app.tips().error(res.message);
                        }, 'json');
                    }

                    return false;
                })
            })
        });
    </script>

<?php
\common\widgets\JavascriptBlock::end();
?>