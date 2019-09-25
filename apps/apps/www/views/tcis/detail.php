<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/3/24
 * Time: 10:54
 */
/* @var $model \common\models\PmOrderFpzz */
/* @var $pmOrderFpzzItem \common\models\PmOrderFpzzItem */
?>

<div id="fpzz-detail-view">

    <?php foreach($model->pmOrderNewwindowPdf as $key => $row):?>
        <?php /* @var $row \common\models\PmOrderNewwindowPdf */?>

        <div class="fpzz-detail-status">
            <div class="fpzz-commit-time">
                <ul>
                    <li>
                        <span class="fpzz-status-active fpzz-status-font">已开具</span>

                        <label data-id="<?= $row->id ?>" class="fpzz-show-page pdf-jpg">查看</label>

                    </li>

                    <li>
                        <span class="fpzz-time-text">提交时间:</span>
                        <label class="fpzz-show-time"><?= date('Y-m-d', $model->created_at)?></label>
                    </li>
                    <li>
                        <span data-go="/tcis/resend-email?id=<?= $row->id ?>" class="resend flex1">重新发送</span>
                        <span class="empty"></span>
                    </li>

                </ul>
            </div>

        </div>

    <?php endforeach; ?>

    <p>收件信息</p>
    <div class="fpzz-detail-email">
        <ul>
            <li>
                <span class="fpzz-email-left">电子邮箱:</span> <?= $model->email ?>
            </li>
        </ul>
    </div>
    <p>发票信息</p>
    <div class="fpzz-detail-content" id="recipt-mid">
        <table class="table">
            <tbody>
            <tr>
                <td class="recipt-td">发票抬头</td>
                <td colspan="2">
                    <input type="text" value="<?= $model->user_name ?>" readonly disabled>
                </td>
            </tr>

            <tr>
                <td class="" width="100%" colspan="2">发票内容：</td>
            </tr>

            <?php foreach($pmOrderFpzzItem as $row):?>
                <?php /* @var $row \common\models\PmOrderFpzzItem */?>
                <tr class="fpzz-charg-item">
                    <td class="fpzz-title"><?= $row->spmc ?></td>
                    <td class="fpzz-bill-date"><?= $row->ggxh ?></td>
                    <td class="order-item-bill"><?= $row->origin_amount ?></td>
                </tr>
            <?php endforeach; ?>

            <tr id="fpzz-amount">
                <td class="recipt-td">发票金额</td>
                <td colspan="2">
                    <span id="recipt-total-amount"><?= $model->total_amount ?>元</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <p class="fpzz-pm-detail" data-go="/order/pm-view?id=<?= $model->pm_order_id ?>">
        <a>对应详细物业账单</a>
        <i></i>
    </p>

    <?php \components\za\ActiveForm::begin(['id' => 'feedback-form']); ?>
        <input type="hidden" name="id" id="fpzz-id" value="">
    <?php \components\za\ActiveForm::end(); ?>

</div>

<div id="warning-tips">
    <div class="mask"></div>
    <div class="warning-content-box">
        <div class="warning-content-tips"></div>
    </div>
    <div class="show-pimage" style="position: fixed;left: 2%;right: 2%;top: 30%;">
        <img class="show-pdf-jpg" style="width: 100%;height: 100%;" src="" alt="">
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $('.fpzz-feedback').click(function (){
        var id = $(this).attr('data-id');
        $('#fpzz-id').val(id);

        var data = $('#feedback-form').serialize();

        $.ajax({
            type: "POST",
            url: '/tcis/feedback',
            data: data,
            beforeSend: function (){
                $('.warning-content-tips').html('正在提交反馈信息，请稍后……');
                warningFunc();
            },
            success: function (res){
                if(res.code == 0){
                    $('.warning-content-tips').html('已接收到您的开票反馈信息，次日帮您处理！');
                    $('#warning-tips').show();
                } else {
                    $('.warning-content-tips').html(res.message);
                    $('#warning-tips').show();
                }
            },
            error: function (){
                $('.warning-content-tips').html('服务繁忙！');
            },
            dataType: 'json'
        });



    });

    $('.pdf-jpg').on('click', function (){
        var v = $(this).attr('data-id');
        if(v > 0){
            $.ajax({
                type: "GET",
                url: '/tcis/show-pdf',
                data: {id:v},
                beforeSend: function (){
                    $('.warning-content-tips').html('请稍后……');
                    warningFunc();
                },
                success: function (res){
                    if(res.data.pdfJpg){
                        $('.warning-content-tips').html('').hide();
                        $('.show-pdf-jpg').attr("src", res.data.pdfJpg);
                        $('.show-pdf-jpg').show();
                    } else {
                        $('.warning-content-tips').html(res.message);
                    }
                },
                error: function (){
                    $('.warning-content-tips').html('服务繁忙！');
                    warningFunc();
                },
                dataType: 'json'
            })
        }
    });

    $('.wxcard').on('click', function(){
        var fid = $(this).attr('data-fid');
        $.ajax({
            type: "GET",
            url: '/tcis/get-authurl',
            data: {fid:fid},
            beforeSend: function (){
                $('.warning-content-tips').html('正在拉起微信授权，请稍后……');
                warningFunc();
            },
            success: function (res){
                if(res.data.auth_url){
                    window.location.href = res.data.auth_url;
                } else {
                    $('.warning-content-tips').html(res.message);
                }
            },
            error: function (){
                $('.warning-content-tips').html('服务繁忙！');
                warningFunc();
            },
            dataType: 'json'
        })
    });

    var warningFunc = function(){
        $('.show-pdf-jpg').hide();
        $('#warning-tips').show();
    };

    $('.mask').click(function (){
        $('#warning-tips').hide();
    });
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
