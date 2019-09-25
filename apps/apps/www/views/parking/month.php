<?php

/* @var $this yii\web\View */
/* @var $parkingId string */
/* @var $plateNo string */

?>
<div class="panel" id="parking-month">
    <form>
        <input type="hidden" name="_csrf" value="<?=\Yii::$app->request->csrfToken?>">
        <input type="hidden" name="parkingId" value="<?= $parkingId ?>">
        <div class="plate-numbers">
            <p><?= $plateNo ?></p>
        </div>
        <div class="an change-month">
            <input name="amount" type="hidden" id="amount" value="1">
            <div class="flex">
                <div data-amount="1" class="active">
                    <span>1个月</span>
                </div>
                <div data-amount="2">
                    <span>2个月</span>
                </div>
                <div data-amount="3">
                    <span>3个月</span>
                </div>
            </div>
        </div>
        <div style="margin-top: 4em">
            <button class="btn btn-block btn-danger">查询</button>
        </div>
    </form>
</div>
<?php \common\widgets\JavascriptBlock::begin();?>
<script>
    $('#parking-month').on('loaded',function(){
        $('.an .flex>div',this).bind('click',function(){
            $('.an .flex>div').removeClass('active');
            $(this).addClass('active');
            $('#amount').val($(this).attr('data-amount'));
            console.log($(this).attr('data-amount'));
        });
        $('form',this).bind('submit',function(){
            var mon = $('#amount').val();

            location.href = '/parking/month-bill?parkingId=<?= $parkingId ?>&plateNo=<?= $plateNo?>&month='+mon;
            return false;
        });
    });
    wx.ready(function (){
        wx.hideAllNonBaseMenuItem();
    });
</script>
<?php \common\widgets\JavascriptBlock::end();?>