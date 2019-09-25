<div id="page">
    <div class="title"></div>
    <div class="message-box">
        <div class="message">
            <div id="myGift"></div>
            <span id="show-times"><?= $times ?></span>
            <div id="message-btn"></div>
        </div>
    </div>
    <div id="rotate-box">
        <div class="rotate">
        </div>
        <div id="rotate-btn"></div>
        <div class="arrow"></div>
    </div>
</div>
<div id="mask">
    <div class="mask-btn">确 定</div>
</div>
<div id="rule" class="window">
    <h3>抽奖规则：</h3>
    <p>1.根据不同缴费月份累计，业主可获取对应次数的抽奖机会：</p>
    <p>预缴1个月物业费：1次抽奖机会</p>
    <p>缴纳2个月物业费：2次抽奖机会</p>
    <p>缴纳3个月物业费：3次抽奖机会</p>
    <p>2.除特等奖的领取需登记相关个人信息，交由工作人员审核后通过邮寄形式送到您手中外，其他奖项您可选择管家派送上门或前往物业中心领取。</p>
    <p>3.如有疑问请咨询物业中心服务人员。</p>
    <p>&nbsp;</p>
    <p>注：本活动最终解释权归深圳市到家信息科技有限公司所有</p>
    <h3>活动时间：</h3>
    <p>2017年1月16日至2017年1月23日。<br>注：本活动最终解释权归深圳市到家信息科技有限公司所有；</p>
</div>
<div id="gift" class="window">
    <h2>恭喜您！获得</h2>
    <div id="gift-img">
        <div data-id="0"></div>
        <h5></h5>
    </div>
    <div class="info">
        <div>
            <img id="qrcode" width="130" height="130" src="">
        </div>
        <div>
            <span class="status">可领取</span>
            <p>温馨提示：<br/>
                请联系在场工作人员扫一扫二维码确认领取</p>
        </div>
    </div>
</div>
<div id="gift-list" class="window">
    <ul>

    </ul>
</div>

<div class="msg-box no-gift window">
    <h4>抱歉，啥也没抽到</h4>
    <div></div>
    <p>礼物离您很近了，再接再厉！！！</p>
</div>

<div class="msg-box no-times window">
    <h4>抱歉，您还没有获得抽奖机会</h4>
    <div></div>
    <p>预缴即可获得抽奖机会哦！！！</p>
</div>
<?php \common\widgets\JavascriptBlock::begin() ?>
<script>
    $(function () {
        var times = <?=$times?>;
        var running=false;
        function getmatrix(a, b, c, d, e, f) {
            var aa = Math.round(180 * Math.asin(a) / Math.PI);
            var bb = Math.round(180 * Math.acos(b) / Math.PI);
            var cc = Math.round(180 * Math.asin(c) / Math.PI);
            var dd = Math.round(180 * Math.acos(d) / Math.PI);
            var deg = 0;
            if (aa == bb || -aa == bb) {
                deg = dd;
            } else if (-aa + bb == 180) {
                deg = 180 + cc;
            } else if (aa + bb == 180) {
                deg = 360 - cc || 360 - dd;
            }
            return deg >= 360 ? 0 : deg;
        }
        var receive = function (a,id) {
            var gDeg = 22.5 + a * 45;
            console.log('targer:'+gDeg);
            var i = $('#rotate-box .rotate').css('transform');
            var deg = eval('get' + i);
            $('#rotate-box .rotate').removeClass('running');
            $('#rotate-box .rotate').rotate({
                angle: deg,
                duration: 2000, //旋转时间
                animateTo: gDeg + 1440, //让它根据得出来的结果加上1440度旋转
                callback: function() {
                    running=false;
                    showGift(id);
                }
            });
        };

        $('#rotate-btn').on('click', function () {
            if(running) return false;
            if (times > 0) {
                times--;
                $('#show-times').html(times);
                running=true;
                $('#rotate-box .rotate').removeAttr('style').addClass('running');
                var t = new Date();
                $.getJSON('/prepay-lottery/default/gift?projectId=<?=$projectId?>', function (res) {
                    var t2 = new Date();
                    if (res.code === 0) {
                        setTimeout(function () {
                            receive(res.data.giftId,res.data.id);
                        }, 2000 - t2.getTime() + t.getTime());
                    }else{
                        $('#rotate-box .rotate').removeAttr('style').removeClass('running');
                        alert(res.message);
                    }
                });
            } else {
                $('.no-times,#mask').show();
            }
        });

        $('#mask').on('click', function () {
            $('#mask,.window').hide();
        });
        $('#message-btn').on('click', function () {
            $('#mask,#rule').show();
        });
        var showGift = function(id){
            $.getJSON('/prepay-lottery/default/my-gift',{id:id},function(res){
                if(res.code===0){
                    if(res.data.id==1){
                        $('.msg-box.no-gift,#mask').show();
                        return;
                    }
                    $('#gift-img div').css({backgroundImage:'url('+res.data.bg+')',backgroundSize:"100% 100%"})
//                    $('#gift-img div').removeClass('g2 g3 g4 g5 g6').addClass('g'+res.data.id);
                    $('#gift .info').show();
                    if(res.data.name=='微信红包' || res.data.name=='谢谢参与')
                        $('#gift .info').hide();
                    $('#gift-img h5').html(res.data.name);
                    $('#gift .status').html(res.data.gave_at==0?'可领取':'已领取');
                    if(res.data.gave_at>0)
                        $('#gift .status').addClass('red');
                    else
                        $('#gift .status').removeClass('red');
                    $('#gift #qrcode').attr('src','/prepay-lottery/default/qrcode?id='+res.data.qrcodeId);
                    $('#gift,#mask').show();
                }
            })
        };
        $('#myGift').bind('click',function(){
            $.getJSON('/prepay-lottery/default/my-gift-list',function(res){
                if(res.code===0){
                    $('#gift-list ul').html('');
                    if(res.data.list.length===0){
                        $('.msg-box.no-gift,#mask').show();
                        return ;
                    }
                    $.each(res.data.list,function(i,e){
                        $('#gift-list ul').append(
                            $('<li/>').addClass(e.gave_at>0?'gave':'w').attr('data-id',e.id).html(
                                '<div class="" style="background-image:url('+e.bg+');background-size:100% 100%"></div><h5>'+e.name+'</h5>'
                            )
                        )
                    });
                    $('#gift-list,#mask').show();
                    $('#gift-list li[data-id]').bind('click',function(){
                        $('#gift-list').hide();
                        showGift($(this).attr('data-id'));
                    })
                }
            })
        });

    })
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>