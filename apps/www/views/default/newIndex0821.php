<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $projectInfo object */
/* @var $message string */
/* @var $exception Exception */
/* @var $memberAuthRedPack \common\models\AuthHouseNotificationMember */

use yii\helpers\Html;
?>
<body class="bg-f1">

<div id="focus" class="focus">
    <div class="hd">
        <ul></ul>
    </div>
    <div class="bd">
        <ul>
            <?php foreach ($BannerList as $value) { ?>
                <?php
                    if(strstr(strtolower($value->url), "shop.51homemoney.com/mobile/index")){
                        $url = "{$value->url}/pid/{$projectInfo->house_id}/pk/{$projectInfo->url_key}";
                        $class = "shopJump";
                    }else{
                        $url = $value->url;
                        $class = "";
                    }
                ?>
                <li><a class="<?= $class; ?>" data-pic="<?= Yii::getAlias($value->pic) ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_BANNER ?>" href="<?=$url;?>"><img src="<?=Yii::getAlias($value->pic);?>" alt="<?=$value->title;?>" /></a></li>
            <?php }?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    TouchSlide({
        slideCell:"#focus",
        titCell:".hd ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell:".bd ul",
        effect:"left",
        autoPlay:true,//自动播放
        autoPage:true, //自动分页
        switchLoad:"_src" //切换加载，真实图片路径为"_src"
    });
</script>


<!--焦点图结束-->
<section class="notice">
    <div class="notice_title clearfix">最新消息，新认证房产有红包，最高享200元。活动时间：2019.01~2019.12</div>
</section>

<!--<div class="property-ann" style="line-height: 3em;margin-top: 0.5em;">-->
<!--    <section class="notice" style="display: flex;">-->
<!--        <label style="font-size: 16px;font-weight: bold;color: red;margin-left: 1em;">公告：</label><div class="clearfix">因服务升级，缴费、房产认证业务暂停使用！</div>-->
<!--    </section>-->
<!--</div>-->

<section class="IndexMenu">
    <ul class="clearfix">

<!--        撤场项目隐藏缴费和报事按钮-->
        <?php if($projectInfo->status == 1): ?>
            <?php if(\common\models\SysSwitch::inVal('pauseWeChatPayment', $projectInfo->house_id)){?>
                <li>
                    <a href="#" onclick="TipsError('该项目暂取消“微信缴费”业务！')"><i class="icon ">
                        <img src="/static/images/ico/ico1.png"  width="40"  height="40"></i>物业缴费
                    </a>
                </li>
            <?php } else {?>
                <li>
                    <a href="/house/choose-bill"><i class="icon ">
                        <img src="/static/images/ico/money.png"  width="40"  height="40"></i>物业缴费
                    </a>
                </li>

            <?php }?>
            <li>
                <a href="/new-repair/list?status=0"><i class="icon ">
                    <img src="/static/images/ico/repair.png"  width="40"  height="40"></i>公共维修
                </a>
            </li>
            <?php if(\common\models\SysSwitch::inVal('testMember', \Yii::$app->user->id)){ ?>
                <li>
                    <a href="/repair/personal-repair"><i class="icon ">
                        <img src="/static/images/ico/brush.png"  width="40"  height="40"></i>上门维修
                    </a>
                </li>
            <?php }?>
            <li>
                <a href="/new-repair/list?status=0&flowStyleID=8"><i class="icon ">
                    <img src="/static/images/ico/ico3.png"  width="40"  height="40"></i>投诉/建议
                </a>
            </li>
        <?php endif ?>
        <li>
            <a href="/article/list/"><i class="icon "><img src="/static/images/ico/megaphone.png"  width="40"  height="40"></i>社区动态</a>
        </li>

        <li>
            <a href="/house/"><i class="icon "><img src="/static/images/ico/ico11.png"  width="40"  height="40"></i>业主中心</a>
        </li>

        <li>
            <a href="/member/"><i class="icon "><img src="/static/images/ico/ico5.png"  width="40"  height="40"></i>我的账户</a>
        </li>

        <li>
            <a href="/life-service/mobile?"><i class="icon "><img src="/static/images/ico/phone.png"  width="40"  height="40"></i>话费充值</a>
        </li>
        <?php if(Yii::$app->params['temp_add_href']['status']){?>
            <li>
                <a onclick="javascript:window.location.href='<?= Yii::$app->params['temp_add_href']['href']?>'"><i class="icon "><img src="/static/images/ico/ico7.png"  width="40"  height="40"></i>加入我们</a>
            </li>

        <?php } else {?>
            <li>
                <a href="/page/about/"><i class="icon "><img src="/static/images/ico/ico7.png"  width="40"  height="40"></i>关于我们</a>
            </li>
        <?php } ?>
            <li>
                <a href="javascript:;" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_ICON ?>" id="jzyJump"><i class="icon "><img src="/static/images/ico/jzy_logo.png"  width="40"  height="40"></i>旅游服务</a>

            </li>
        <?php foreach($aUrl as $key => $urlV){?>
            <?= $urlV ?>
        <?php }?>
        <?php if($MeterCount>0){?>
            <li>
                <a href="/meter/index"><i class="icon "><img src="/static/images/ico/ico15.png"  width="40"  height="40"></i>水电抄表</a>
            </li>
        <?php }?>
        <li>
            <a href="/life-service/telephone"><i class="icon "><img src="/static/images/ico/service-phone.png"  width="40"  height="40"></i>便民电话</a>
        </li>
        <?php if($projectInfo->house_id == 296149): ?>
        <li>
            <a href="/activity?id=85">
                <i class="icon "><img src="/static/images/ico/toupiao.png" width="40" height="40"></i>投票活动
            </a>
        </li>
        <?php endif; ?>

<!--        <li>
            <a class="shopJump" data-pic="<?/*= \Yii::$app->params['domain.www']; */?>/static/images/ico/gdf.png" data-from="<?/*= \common\models\ThirdpartyViewHistory::CLICK_ICON */?>" href="javascript:;"><i class="icon "><img src="/static/images/ico/gdf.png"  width="40"  height="40"></i>到家好米</a>
        </li>-->


        <?php foreach($shopList as $key => $urlV){?>
            <?= $urlV ?>
        <?php }?>


        <?php

        if(in_array(\YII::$app->user->id, [117155,188418,191510,193625,121845,202406,206921])){?>

            <?php if($isShowQuestion) { ?>
            <li>
                <a href="/question/preface"><i class="icon "><img src="/static/images/ico/wenjuan.png"  width="40"  height="40"></i>问卷调查</a>
            </li>
            <?php } ?>

            <li>
                <a href="/search-notices/list"><i class="icon "><img src="/static/images/ico/searchIcon.png"  width="40"  height="40"></i>寻物启事</a>
            </li>
<!--            <li>
                <a href="https://shop.51homemoney.com/Mobile/Index/index/sid/3/pk/<?/*= $projectInfo->url_key */?>/pid/<?/*= $projectInfo->house_id */?>"><i class="icon "><img src="/static/images/ico/gdf.png"  width="40"  height="40"></i>测试-到家好米</a>
            </li>-->

        <?php } ?>
    </ul>
</section>

<section class="IndexNews">
<div id="leftTabBox" class="tabBox">
    <div class="hd">
        <ul>
            <li><a>最新</a></li>
            <li><a>动态</a></li>
            <li><a>活动</a></li>
        </ul>
    </div>
    <div class="bd NewList">
        <ul  class="clearfix">
            <?php foreach ($newsProvider1 as $model) { ?>
            <a href="/article?id=<?= $model->id ?>&title=<?= $model->title ?>">
                    <li class="clearfix">
                        <div class="detail">
                            <h3><?= $model->title ?></h3>
                            <p><span><?=$model->categoryName;?></span> <?php echo date('Y-m-d',$model->post_at); ?></p>
                        </div>
                        <img src="<?= Yii::getAlias($model->pic) ?>?x-oss-process=image/resize,w_<?=$model->show_type==1?'120':'640'?>" alt="<?= $model->title ?>" class="pic">
                    </li>
            </a>
            <?php }?>
        </ul>
        <ul  class="clearfix">
            <?php foreach ($newsProvider2 as $model) { ?>
            <a href="/article?id=<?= $model->id ?>&title=<?= $model->title ?>">
                <li class="clearfix">
                    <div class="detail">
                        <h3><?= $model->title ?></h3>
                        <p><span><?=$model->categoryName;?></span> <?php echo date('Y-m-d',$model->post_at); ?></p>
                    </div>
                    <img src="<?= Yii::getAlias($model->pic) ?>?x-oss-process=image/resize,w_<?=$model->show_type==1?'120':'640'?>" alt="<?= $model->title ?>" class="pic">
                </li>
            </a>
            <?php }?>
        </ul><ul  class="clearfix">
            <?php foreach ($newsProvider3 as $model) { ?>
            <a href="/article?id=<?= $model->id ?>&title=<?= $model->title ?>">
                <li class="clearfix">
                    <div class="detail">
                        <h3><?= $model->title ?></h3>
                        <p><span><?=$model->categoryName;?></span> <?php echo date('Y-m-d',$model->post_at); ?></p>
                    </div>
                    <img src="<?= Yii::getAlias($model->pic) ?>?x-oss-process=image/resize,w_<?=$model->show_type==1?'120':'640'?>" alt="<?= $model->title ?>" class="pic">
                </li>
            </a>
            <?php }?>
        </ul>
    </div>
</div>
</section>

<div class="property-ann" style="line-height: 2em;margin-top: 0.5em;">
    <section class="notice" style="display: flex;">
        <div class="" style="margin: 0 auto;">
            <a style="color:#FCCA20" href="http://www.beian.miit.gov.cn">深圳市到家信息科技有限公司 粤ICP备16069247号</a>
        </div>
    </section>
</div>


<?php if($memberAuthRedPack){?>
    <div id="cdj-read-packet-index" data-go="/activities/red-pack-activity?houseId=<?= $memberAuthRedPack->house_id ?>">
        <img src="../static/images/activity/RedPacketIcon.png" alt="">
    </div>
<?php }?>

<script type="text/javascript">
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

    TouchSlide({
        slideCell:"#slider",
        titCell:"#pagenavi ul", //开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell:".bd ul",
        effect:"left",
        autoPlay:true,//自动播放
        autoPage:true, //自动分页
        switchLoad:"_src" //切换加载，真实图片路径为"_src"
    });
    TouchSlide({ slideCell:"#leftTabBox" });

    $('.parking-url').on('click', function () {
        $.getJSON('/parking/get-url', function(res){
            if(res.data.goUrl){
                location.href = res.data.goUrl;
                return;
            }
        })
    });
    // 橘子游跳转
    $('#jzyJump').on('click',function () {
        var _url = 'http://www.myorangetravel.com/progress.html';
        var clickPlace = $(this).attr('data-from');
        var pic = $(this).attr('data-pic');

        $.ajax({
            type: 'POST',
            url: '/default/ajax-third-parth-view-history',
            data: {houseId:<?= $houseID ?>,type:2,modelv:1,clickPlace:clickPlace,pic:pic},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                location.href = _url;
            },
           fail:function (res) {
               console.log(res);
                location.href = _url;
           }
        });


    });

    //商城
    $('.shopJump').on('click',function () {

        var url = $(this).attr('href');
        var clickPlace = $(this).attr('data-from');
        var pic = $(this).attr('data-pic');

        $.ajax({
            type: 'POST',
            url: '/default/ajax-third-parth-view-history',
            data: {houseId:<?= $houseID ?>,type:1,modelv:1,clickPlace:clickPlace,pic:pic},
            dataType: 'json',
            success: function (data) {
                console.log(data);
                location.href = url;
            },
            fail:function (res) {
                console.log(res);
                location.href = url;
            }
        });
    });

    $('#cdj-read-packet-index').on('click', function (){
        var url = $(this).attr('data-go');

        location.href = url;
        return;
    });

</script>
