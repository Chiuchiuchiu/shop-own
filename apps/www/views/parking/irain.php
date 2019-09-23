<?php
/**
 * @var $this \yii\web\View
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/9
 * Time: 11:19
 * @var $memberPlate
 * @var $rows \apps\www\models\MemberCar
 */
/* @var $data array */

?>

<div class="" id="cdj-parking">

    <div class="panel parking-view-p">
        <div class="parking-title">
            <div data-go="/parking-order/order-list/" class="park-order">订单</div>
            <div class="park-name" data-pid="<?= $data['parkingId'] ?>"><?= $data['parkingName'] ?></div>
        </div>

        <p style="color: #f00;">注：暂只支持临卡缴费</p>

        <div class="pure-form">
            <div class="parking-change-type">
                <select name="fee-type" id="fee-type">
                    <option value="t">临卡缴费</option>
                </select>
            </div>
            <div class="parking-number">
                <div id="input-pn0" class="input-pro input-width-13 hasPro">粤</div>
                <div id="input-pn1" class="input-pn input-width-13 input-letter"></div>
                <div id="input-pn2" class="input-pn input-width-13"></div>
                <div id="input-pn3" class="input-pn input-width-13"></div>
                <div id="input-pn4" class="input-pn input-width-13"></div>
                <div id="input-pn5" class="input-pn input-width-13"></div>
                <div id="input-pn6" class="input-pn input-width-13"></div>
                <div id="input-pn7" class="input-pn hidden input-width-13"></div>
            </div>
            <div class="new-car-choose">
                <span data-c="" class="new-car-click new-car-choose-d">新能源汽车</span>
            </div>
            <div class="park-btn-s">
                <button class="btn btn-block btn-primary btn-search">查询</button>
            </div>
        </div>

        <div style="height: 3em">

        </div>

        <div class="park-keywords">

        </div>
    </div>

</div>

<div id="member-car-plate" class="panel">
    <ul>

        <?php if(count($memberPlate) > 0){?>

            <?php foreach($memberPlate as $rows){?>
                <li class="member-pl-li-<?= $rows->id ?>">
                    <p>
                        <span><?= $rows->plate_number ?></span>
                        <button data-t="<?= $rows->type ?>" data-pl="<?= $rows->plate_number ?>" class="plate-number-sea">查询</button>
                        <button data-id="<?= $rows->id ?>" data-pl="<?= $rows->plate_number ?>" class="plate-number-del">删除</button>
                    </p>
                </li>
            <?php }?>

        <?php }?>

    </ul>
    <?php \components\za\ActiveForm::begin(['id' => 'del-m-plate']); ?>
        <input type="hidden" class="plaId" name="plaId" value="">
    <?php \components\za\ActiveForm::end(); ?>
</div>

<div style="height: 5em">

</div>

<div id="warning-tips">
    <div class="mask"></div>
    <div class="warning-content-box">
        <div class="warning-content"></div>
        <div class="warning-back-box" style="display: none;">
            <a class="close-hidden">关闭</a>
            <a class="comf-step" data-id="">确定</a>
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $("#input-pn0").click(function(){
        console.log('a');
        $('.park-keywords').show();
        $(this).siblings().removeAttr('style');
        $(this).attr('style', 'border: 1px solid #F8B500');
        showProvince('.park-keywords', '#input-pn0');
    });

    $(".input-pn").click(function(){
        $(this).siblings().removeAttr('style');
        $(this).attr('style', 'border: 1px solid #F8B500');
        parkingNext = $(this).index() - 1;

        if($("#input-pn0").hasClass("hasPro")){
            $('.park-keywords').show();
            showKeybord('.park-keywords');
        }else{
            $("#input-pn0").click();
        }

    });

    $('.new-car-click').click(function (){
        if($(this).hasClass('new-car-choose-c')){
            $(this).removeClass('new-car-choose-c').addClass('new-car-choose-d');
            $(this).removeAttr('data-c');
            $('#input-pn7').addClass('hidden');
            $('.input-pn').removeClass('input-width-11').addClass('input-width-13');
        } else {
            $(this).addClass('new-car-choose-c').removeClass('new-car-choose-d');
            $(this).attr('data-c', 'n');
            $('#input-pn7').removeClass('hidden');
            $('.input-pn').removeClass('input-width-13').addClass('input-width-11');
        }
    });

    $('.btn-search').click(function (){
        var feeT = $('#fee-type').val();
        var chooseC = $('.new-car-click').attr('data-c');
        var plateNo = '';
        var parkingId = $('.park-name').attr('data-pid');
        var locaHref = '<?= $data['tempBillUrl'] ?>';
        var feeType = $('#fee-type').val();
        var p0 = $('#input-pn0').text();
        var p1 = $('#input-pn1').text();
        var p2 = $('#input-pn2').text();
        var p3 = $('#input-pn3').text();
        var p4 = $('#input-pn4').text();
        var p5 = $('#input-pn5').text();
        var p6 = $('#input-pn6').text();
        var p7 = $('#input-pn7').text();
        plateNo = p0+p1+p2+p3+p4+p5+p6;
        if(chooseC === 'n'){
            plateNo = plateNo + p7;
        }
        if(feeType === 'm'){
            locaHref = '<?= $data["monthBillUrl"] ?>';
            }

            if(plateNo.length != 7 && plateNo.length != 8){
                $('.warning-content').text('请填写完整车牌号');
                $('#warning-tips').show().fadeOut(2000);
                return false;
            } else {
                locaHref = locaHref + 'parkingId=' + parkingId + '&plateNo=' + plateNo;

                location.href = locaHref;

                /*$('.warning-content').text('车牌号：' + plateNo);
                $('#warning-tips').show().fadeOut(2000);
                return false;*/
            }

        });

        $('.plate-number-sea').click(function (){
            var plateNo = $(this).attr('data-pl');
            var feeType = $(this).attr('data-t');
            var parkingId = $('.park-name').attr('data-pid');
            var locaHref = '<?= $data["tempBillUrl"] ?>';
            switch(feeType){
                case '2':
                    locaHref = '<?= $data["monthBillUrl"] ?>';
                    break;
            }

            locaHref = locaHref + 'parkingId=' + parkingId + '&plateNo=' + plateNo;
            location.href = locaHref;
            return;
        });

        $('.plate-number-del').click(function (){
            var val = $(this).attr('data-id');
            var plateNu = $(this).attr('data-pl');
            $('.warning-content').text('您确定要删除【'+ plateNu +'】吗？');
            $('#warning-tips, .warning-back-box').show();
            $('.comf-step').attr('data-id', val);
        });

        $('.close-hidden').bind('click', function () {
            $('.comf-step').attr('data-id', '');
            $('#warning-tips').hide();
        });

        $('.comf-step').click(function (){
            var val = $(this).attr('data-id');
            $('.plaId').val(val);
            var data = $('#del-m-plate').serialize();
            $.ajax({
                type: 'POST',
                url: '/parking/del-plate',
                data: data,
                beforeSend: function (res){
                    $('.warning-content').text('正在删除.....');
                    $('.warning-back-box').hide();
                },
                success: function (res){
                    if(res.code === 0){
                        $('.member-pl-li-'+res.data).remove();
                        $('.warning-content').text('已删除！');
                        $('#warning-tips').show().fadeOut(2000);
                    } else {
                        $('.plaId').val('');
                        $('.comf-step').attr('data-id', '');
                        $('.warning-content').text(res.message);
                        $('#warning-tips').show().fadeOut(4000);
                    }
                },
                dataType: 'json'
            });
        });

    </script>

    <?php \common\widgets\JavascriptBlock::end(); ?>
