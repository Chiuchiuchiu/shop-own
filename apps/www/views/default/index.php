<?php

/* @var $this yii\web\View */
/* @var $newDataProvider \yii\data\ActiveDataProvider */
/* @var $memberAuthRedPack \common\models\AuthHouseNotificationMember */
/* @var $siteTitle string */
/* @var $projectUrlKey string|null */
/* @var $propertyAnnouncement array */
/* @var $projectId integer */
/* @var $aUrl array */

?>
<div class="panel" id="index">

    <div class="wb">
        <div class="banner">

                <div class="flicking_con">
                    <a href="#"></a>
                    <a href="#"></a>
                    <a href="#"></a>
                </div>

                <div class="flex banner-roll">
                    <ul>

                        <li>
                            <a>
                                <img src="../static/images/banner/2018-5-13.jpg?2" alt="">
                            </a>
                        </li>

                        <li>
                            <a href="/activities/christmas">
                                <img src="../static/images/banner/a-r-p.jpg?2" alt="">
                            </a>
                        </li>

                        <li>
                            <a>
                                <img src="../static/images/banner/2018-happy-new-years.jpg?2" alt="">
                            </a>
                        </li>

                    </ul>
                    <a id="btn_prev"></a>
                    <a id="btn_next"></a>
                </div>

        </div>
        <div class="console">
            <div class="flex">

                <?php if(\common\models\SysSwitch::inVal('pauseWeChatPayment', $projectId)){?>
                    <a onclick="app.tips().error('该项目暂取消“微信缴费”业务！')">
                        <i class="pay-bill"></i>
                        物业缴费
                    </a>
                <?php } else {?>
                    <a href="/house/choose-bill">
                        <i class="pay-bill"></i>
                        物业缴费
                    </a>
                <?php }?>

                <a href="/new-repair/list?status=0">
                    <i class="repair-blue"></i>
                    报事报修
                </a>
                <a href="/new-repair/list?status=0&flowStyleID=8">
                    <i class="icon-repair-type2"></i>
                    投诉
                </a>
                <a data-origin="1" href="/article/list/"">
                    <i class="even"></i>
                    社区动态
                </a>
            </div>
            <div class="flex">
                <a href="/member/">
                    <i class="mine"></i>
                    我的账户
                </a>
                <a href="/house/">
                    <i class="owners"></i>
                    业主中心
                </a>
                <a href="/life-service/mobile?">
                    <i class="mobile"></i>
                    话费充值
                </a>

                <?php if(Yii::$app->params['temp_add_href']['status']){?>

                    <a onclick="javascript:window.location.href='<?= Yii::$app->params['temp_add_href']['href']?>'">
                        <i class="about"></i>
                        加入我们
                    </a>

                <?php } else {?>

                    <a href="/page/about/">
                        <i class="about"></i>
                        关于我们
                    </a>

                <?php } ?>

            </div>

            <div class="flex">
                <?php foreach($aUrl as $key => $urlV){?>
                    <?= $urlV ?>
                <?php }?>
            </div>

        </div>

    </div>


    <?php if(sizeof($propertyAnnouncement)){?>
        <div class="property-ann">
            <div class="line-scroll">
                <span class="s-id-height">公告：</span>
                <ul>

                    <?php foreach($propertyAnnouncement as $key => $rows){?>

                    <li>
                        <a href="/property-announcement/detail?id=<?= $rows['id'] ?>"><?= $rows['title'] ?></a>
                    </li>

                    <?php }?>

                </ul>
                <span class="s-id-height">
                    <a href="/property-announcement/list">更多</a>
                </span>
            </div>
        </div>
    <?php }?>

    <div class="owners-exclusive">
        <b>业主专享</b>
    </div>

    <div>
        <?php foreach ($newDataProvider->getModels() as $model) { ?>
        <div data-go="/article?id=<?= $model->id ?>" class="article-cell type-<?= $model->show_type ?>">
            <div class="info">
                <h4><?= $model->title ?></h4>
                <p><?= $model->summary ?></p>
            </div>
            <?php if ($model->show_type !== 3): ?>
                <div class="pic">
                    <img src="<?= Yii::getAlias($model->pic) ?>?x-oss-process=image/resize,w_<?=$model->show_type==1?'120':'640'?>" alt="<?= $model->title ?>">
                </div>
            <?php endif; ?>
            <?php /*<div class="more">
                <span class="time"><?= date('Y-m-d', $model->post_at) ?></span>
                <span>查看详情</span>
            </div>
            */?>
        </div>
        <?php
        }
        ?>
    </div>

</div>

<?php if($memberAuthRedPack){?>
    <div id="cdj-read-packet-index" data-go="/activities/red-pack?houseId=<?= $memberAuthRedPack->house_id ?>">
        <img src="../static/images/activity/RedPacketIcon.png" alt="">
    </div>
<?php }?>



<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

        $(".banner-roll").touchSlider({
            flexible: true,
            speed: 500,
            mouse: true,
            btn_prev: $("#btn_prev"),
            btn_next: $("#btn_next"),
            paging: $(".flicking_con a"),
            counter: function(e) {
                $(".flicking_con a").removeClass("on").eq(e.current - 1).addClass("on");
            }
        });

        var timer = setInterval(function() {
            $("#btn_next").click();
        }, 4000);

        $(".main_visual").hover(function() {
            clearInterval(timer);
        }, function() {
            timer = setInterval(function() {
                $("#btn_next").click();
            }, 4000);
        });

        $('.location-lxj').click(function (){
            var h = $(this).attr('data-h');
            $.get('/lxj/log', function (res){
                if(res.code == -1){
                    location.href = res.data.url;
                    return;
                }

                window.location.href = h;
            }, 'json');
        });

        $('.parking-url').on('click', function () {
            $.getJSON('/parking/get-url', function(res){
                if(res.data.goUrl){
                    location.href = res.data.goUrl;
                    return;
                }
            })
        });

        function autoScroll(obj, timeOut){
            var l = $(obj).find('li').length;
            if(l > 1){
                setInterval(function (){
                    $(obj).animate({
                        marginTop : "-39px"
                    },500,function(){
                        $(this).css({marginTop : "0px"}).find("li:first").appendTo(this);
                    })
                }, timeOut);
            }
        }

        autoScroll('.line-scroll ul', 3500);


</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
