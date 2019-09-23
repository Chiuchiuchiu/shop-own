<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/4
 * Time: 15:17
 *
 * @var int $identity
 * @var integer $houseId
 * @var \common\models\House $model
 */
$this->title = '无法自动认证，需要提交审核';

?>

<div id="auth-house-review" class="">
    <div class="a-house-r-box panel">
        <div class="cdj-tips">
            <lable>注：</lable>请填写真实姓名，以便管家核实信息!
        </div>

        <?php \components\za\ActiveForm::begin(['method' => 'POST']); ?>
        <div class="pure-form">
            <label>真实姓名：</label>
            <div class="">
                <input class="input-lg" type="text" name="customer-name" id="customerName" data-label="真实姓名" data-required="1" required maxlength="20">
                <input type="hidden" name="identity" value="<?= $identity ?>">
                <input type="hidden" name="houseId" value="<?= $houseId ?>">
            </div>
            <label>房产信息：</label>
            <div class="text-indent">
                <p>
                    <?= $model->showName ?>
                </p>
                <p>
                    <small style="font-size: 0.7em; color: #888888;">(<?= $model->ancestor_name ?>)</small>
                </p>
            </div>
            <button type="submit" class="btn btn-block btn-bottom-all btn-primary">提交审核</button>
        </div>
        <?php \components\za\ActiveForm::end(); ?>
    </div>

</div>


<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    var trim = function(str){
        var v = str.replace(/\s+/g, '');
        return v;
    };

    /*$('#customerName').on('keyup', function (){
        var v = $(this).val();
        v = v.replace(/\s+/g, '');
        $(this).val(v);
    });*/

    $('form').on('submit',function(){
        var customerName = $('#customerName').val();
        customerName = trim(customerName);
        if(customerName.length < 2){
            app.tips().error('请填写姓名！');
            return false;
        }
        $('#customerName').val(customerName);

        var data = $(this).serialize();
        if (app.formValidate(this)) {
            $.ajax({
                type: "POST",
                url: "/auth/house-review-save",
                data: data,
                beforeSend: function (){
                    app.showLoading();
                },
                success: function (res){
                    app.hideLoading();
                    if(res.data.goUrl){
                        location.href = res.data.goUrl;
                        return;
                    }else{
                        app.tips().error(res.message);
                    }
                },
                error: function (){
                    app.hideLoading();
                    app.tips().error('服务繁忙！');
                },
                dataType: 'json'
            });
        }
        return false;
    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
