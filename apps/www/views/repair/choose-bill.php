<?php
/**
 * @var $this \yii\web\View
 * @var $houseRs
 * @var $_user
 * @var $_project \common\models\Project
 * @var $house \common\models\MemberHouse
 */

use components\za\ActiveForm;

$this->title = '物业缴费';

?>
<div class="panel" id="choose-bill-type">
    <?php
        ActiveForm::begin(['id' => 'auth-1']);
    ?>
    <div class="pure-form">
        <label>请选择房屋信息</label>

        <?php if (sizeof($houseRs) > 0) { ?>
            <select name="id" id="choose-select-l" required>
                <option value="">请选择</option>

                <?php foreach ($houseRs as $house) : ?>
                    <option data-group="<?= $house->group ?>" data-pid="<?= $house->house->project_house_id ?>" value="<?= $house->house_id ?>">
                        <?= $house->house->ancestor_name ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <p class="c-show-house-name"></p>

            <button class="btn btn-block btn-bottom-all btn-primary">查询</button>

        <?php } else { ?>
            <p>
                <div class="empty-status"><i></i>暂无认证房产</div>
                <div class="tac" style="padding-bottom:20px">
                    <a class="btn btn-primary" href="/auth/?group=1">认证房产</a>
                </div>
            </p>
        <?php } ?>

    </div>
    <?php
        ActiveForm::end();
    ?>
</div>
<?php
\common\widgets\JavascriptBlock::begin();
?>
<script>
    $('#choose-select-l').change(function (){
        $('.c-show-house-name').html($(this).children('option:selected').html());
    });

    $('#auth-1').on('submit', function (){
        if(app.formValidate($(this))){

            var val = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '/repair/choose-bill',
                data: val,
                success: function (res){
                    if(res.code == 0){
                        window.location.href = res.data.goUrl;
                    }
                },
                error: function(){
                    app.tips().error('服务异常');
                },
                dataType: 'json'
            });
        }

        return false;
    });

</script>
<?php
\common\widgets\JavascriptBlock::end();
?>
