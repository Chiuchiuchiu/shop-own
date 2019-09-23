<?php
/**
 * @var $this \yii\web\View
 * @var $houseRs
 * @var $_user
 * @var $_project \common\models\Project
 * @var $house \common\models\MemberHouse
 */

use components\za\Html;

$this->title = $cdj_header_tip ." | ".'物业缴费';

?>
<div class="panel" id="choose-bill-type">
    <p style="color:#E64340;">
        车位缴费限制说明：<br />
        1、同一周期账单，先缴物业管理费；<br />
        2、车位管理费起始收款时间大于物业管理费起始收款时间，则要求先缴物业管理费；
    </p>
    <?php
    \components\za\ActiveForm::begin(['id' => 'auth-1', 'action' => '/house/show-new-bill', 'method' => 'POST']);
    ?>
    <div class="pure-form">
        <label>请选择房屋信息</label>

        <?php if (sizeof($houseRs) > 0) { ?>
            <select name="id" id="choose-select-l" required>
                <?php foreach ($houseRs as $house) : ?>
                    <option data-group="<?= $house->group ?>" data-pid="<?= $house->house->project_house_id ?>" value="<?= $house->house_id ?>">
                        <?= $house->house->ancestor_name ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <p class="c-show-house-name"></p>

            <label>选择缴费</label>
            <select name="chooseT">
                <option value="bill">物业费（减除滞纳金）</option>
                <option value="all">物业费+滞纳金</option>
            </select>

            <label>缴费类目</label>
            <select name="billCategory" id="billCategory" style="display: none;">
                <option value="h">物业管理服务费</option>
                <option value="notH">公共分摊费用</option>
            </select>

            <select name="Coll" id="Coll" style="display: none;">
                <option value="h">物业管理服务费</option>
                <option value="w">代收水费</option>
            </select>

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
    \components\za\ActiveForm::end();
    ?>
</div>
<?php
\common\widgets\JavascriptBlock::begin();
?>
<script>
    $('#choose-select-l').change(function (){
        $('.c-show-house-name').html($(this).children('option:selected').html());

        var dataPid = $(this).children('option:selected').attr('data-pid');
        var dataGroup = $(this).children('option:selected').attr('data-group');

        if(dataPid === '468497' || dataPid === "117847" || dataPid === '230975'){
            if(dataGroup === '1'){
                $('#billCategory').show();
            }
        } else {
            $('#billCategory').hide();
        }

        if(dataPid === '296152'){
            if(dataGroup === '1'){
                $('#Coll').show();
            }
        } else {
            $('#Coll').hide();
        }

    });

    $('#auth-1').on('submit', function (){
        if(app.formValidate($(this))){
            var houseId = $('#choose-select-l').val();
            var typeN = $('select[name="chooseT"]').val();
            var val = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: '/house/query-bill-type',
                data: val,
                success: function (res){
                    if(res.data.goUrl){
                        location.href = res.data.goUrl + 'id=' + houseId + '&chooseT='+typeN;
                        return;
                    } else {
                        app.tips().error(res.message);
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
