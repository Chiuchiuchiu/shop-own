<?php
 
use yii\helpers\Html;

$project_name = isset($projectName) ? " | " .$projectName:'';
$this->title = $cdj_header_tip .$project_name;
?>
<body class="bg-f1">
        <link rel="stylesheet" href="/static/css/swiper.min.css">

<!-- <div id="focus" class="focus">
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
</script> -->
<!-- ----------轮播图-------------- -->
<style>
    .swiper-container {
        width: 100%;
        height: 157px;
        }
.swiper-button-next, .swiper-button-prev{
        height:20px;
        width:28px;
        }
</style>
<div class="swiper-container">
    <div class="swiper-wrapper">
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
                <div class="swiper-slide">
                   <a class="<?= $class; ?>" data-pic="<?= Yii::getAlias($value->pic) ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_BANNER ?>" href="<?=$url;?>"><img src="<?=Yii::getAlias($value->pic);?>" alt="<?=$value->title;?>" style="width:100%;height:157px;" /></a>
                </div>
            <?php }?>
    </div>
<!-- 如果需要分页器 -->
    <div class="swiper-pagination"></div>
</div>
<script src="/static/js/swiper.min.js"></script>
<script>
    var mySwiper = new Swiper('.swiper-container', {
            direction: 'horizontal', // 垂直切换选项
            loop: true, // 循环模式选项
            autoplayDisableOnInteraction : false,
            speed:300,
            autoplay : {
            delay:2500,
            disableOnInteraction:false
            },
            // 如果需要分页器
            pagination: {
            el: '.swiper-pagination',
            },
            // 如果需要前进后退按钮
            navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
            },

            // 如果需要滚动条
            // scrollbar: {
            // el: '.swiper-scrollbar',
            // },
        });


</script>
<!-- ----------轮播图end-------------- -->


<!--焦点图结束-->
<section class="notice">
<!--    <div class="notice_title clearfix">最新消息，新认证房产有红包，最高享200元。活动时间：2019.01~2019.12</div>-->
    <div class="notice_title clearfix">最新消息：由于系统升级，暂停缴费、报事报修等功能！</div>
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
                    <a href="#" class="close" data-title="物业缴费"><i class="icon " style="background-color:#43ADE7;">
                        <img src="/static/images/ico/jiaofei.png"  width="28"  height="28"></i>物业缴费
                    </a>
                </li>
            <?php } else {?>
                <li>
                    <a href="/house/choose-bill"><i class="icon " style="background-color:#43ADE7;">
<!--                    <a href="#" class="close" data-title="物业缴费"><i class="icon " style="background-color:#43ADE7;">-->
                        <img src="/static/images/ico/jiaofei.png"  width="28"  height="28"></i>物业缴费
                    </a>
                </li>

            <?php }?>
            <li>
                <a href="/new-repair/list?status=0"><i class="icon " style="background-color:#FD781D;">
<!--                <a href="#" class="close" data-title="公共维修"><i class="icon " style="background-color:#FD781D;">-->
                    <img src="/static/images/ico/gongg.png"  width="28"  height="28"></i>公共维修
                </a>
            </li>
            <?php if(\common\models\SysSwitch::inVal('testMember', \Yii::$app->user->id)){ ?>
                <li>
                    <a href="/repair/personal-repair"><i class="icon " style="background-color:#FFBD26;">
<!--                    <a href="#" class="close" data-title="上门维修"><i class="icon " style="background-color:#FFBD26;">-->
                        <img src="/static/images/ico/sahngmen.png"  width="28"  height="28"></i>上门维修
                    </a>
                </li>
            <?php }?>
            <li>
                <a href="/new-repair/list?status=0&flowStyleID=8"><i class="icon "  style="background-color:#43ADE7;">
<!--                <a href="#" class="close" data-title="投诉/建议"><i class="icon "  style="background-color:#43ADE7;">-->
                    <img src="/static/images/ico/tousu.png"  width="28"  height="28"></i>投诉/建议
                </a>
            </li>
        <?php endif ?>
        <!--<li>
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
        </li>-->

        <li>
            <a onclick="javascript:window.location.href='https://shop.51homemoney.com/shop/join'"><i class="icon " style="background-color:#FFBD26;"><img src="/static/images/ico/jiaru.png"  width="28"  height="28"></i>商务合作</a>
        </li>
<!--        <?php /*if(Yii::$app->params['temp_add_href']['status']){*/?>
            <li>
                <a onclick="javascript:window.location.href='<?/*= Yii::$app->params['temp_add_href']['href']*/?>'"><i class="icon " style="background-color:#FFBD26;"><img src="/static/images/ico/jiaru.png"  width="28"  height="28"></i>加入我们</a>
            </li>

        <?php /*} else {*/?>
            <li>
                <a href="/page/about/"><i class="icon "><img src="/static/images/ico/ico7.png"  width="40"  height="40"></i>关于我们</a>
            </li>
        --><?php /*} */?>
            <!-- <li>
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
        <li>
            <a href="/activities/vote-lists?projectId=<?= $projectInfo->house_id ?>">
                <i class="icon "><img src="/static/images/ico/toupiao.png" width="40" height="40"></i>最美管家
            </a>
        </li>

        <?php if(in_array($projectInfo->house_id, [128802, 78874, 84244])){ ?>
            <li>
                <a class="shopJump" data-pic="<?= \Yii::$app->params['domain.www']; ?>/static/images/ico/gdf.png" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_ICON ?>" href="javascript:;"><i class="icon "><img src="/static/images/ico/gdf.png"  width="40"  height="40"></i>到家好米</a>
            </li>
        <?php } ?>

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
            <li>
                <a href="https://shop.51homemoney.com/Mobile/Index/index/sid/3/pk/<?= $projectInfo->url_key ?>/pid/<?= $projectInfo->house_id ?>"><i class="icon "><img src="/static/images/ico/gdf.png"  width="40"  height="40"></i>测试-到家好米</a>
            </li>

        <?php } ?> -->

        <?php foreach($aUrl as $key => $urlV){?>
            <?= $urlV ?>
        <?php }?>
        <li>
            <a href="/shopping/"><i class="icon " style="background-color:#E4393C;"><img src="/static/images/ico/shoping.png"  width="28"  height="28"></i>商城</a>
        </li>
        <li>
            <a href="/my-life/"><i class="icon " style="background-color:#C6C7CB;"><img src="/static/images/ico/more.png"  width="28"  height="28"></i>更多...</a>
        </li>
    </ul>
</section>

<!-- --------------------广告位-------------------------------- -->
    <style>
        /* icon图标 */
            .IndexMenu li a .icon{
                border-radius:15px;
            }
            .IndexMenu li a .icon img{
                margin-top:5px;
            }
            .IndexMenu{
                margin-bottom:10px;
            }
            .IndexNews{
                margin-top:0;
            }
            

        /* 广告位 */
        .advertising{
            width:100%;
            height:300px;
            margin-bottom:10px;

        }
        .advertising_left{
            float:left;
            width:50%;
            height:300px;
        }
        .advertising_left img{
            height:100%;
            width:100%;
            border: 0.5px solid #ddd;
        }
        .advertising_right{
            float:right;
            width:50%;
            height:300px;
        }
        .advertising_top{
            height:50%;
        }
        .advertising_top img{
            width:100%;
            height:100%;
            border: 0.5px solid #ddd;
        }
        .advertising_bottom{
            height:50%;
        }
        .advertising_bottom img {
            width:100%;
            height:100%;
            border: 0.5px solid #ddd;

        }
         .advertising_bottom ul{
             height:100%;
         }
        .advertising_bottom ul li {
            float:left;
            width:50%;
            height:100%;
        }
        .advertising_bottom ul li img{
            height:100%;
            border: 0.5px solid #ddd;

        }
        .advertising_three{
            height:100px;
            width:100%;
            margin-bottom:10px;
        }
        .advertising_three img{
            width:100%;
            height:100%;
            border: 0.5px solid #ddd;

        }
        .advertising_four{
            width:100%;
            height:160px;
        }
        .advertising_four ul li {
            float:left;
            width:33.33%;
        }
        .advertising_four ul li img{
            width:100%;
            height:100%;
            border: 0.5px solid #ddd;

        }
        .advertising_five{
            height:300px;
            width:100%;
            margin-bottom:10px;
        }
        .advertising_five .text_left{
            float:left;
            width:50%;
            height:100%;
        }
        .text_left ul,
        .text_right ul{
            height:100%;
            width:100%;
        }
        .text_left ul li,
        .text_right ul li{
            height:50%;
            width:100%;
        }
        .text_left ul li img,
        .text_right ul li img{
            width:100%;
            height:100%;
            border: 0.5px solid #ddd;

        }
         .advertising_five .text_right{
             float:left;
             width:50%;
             height:100%;
         }
         .advertising_six{
             width:100%;
             height:300px;
             margin-bottom:10px;
         }
         .advertising_six ul{
             height:100%;
             width:100%;
         }
         .advertising_six ul li {
             height:50%;
             width:100%;
         }
         .advertising_six ul li img {
             height:100%;
             width:100%;
            border: 0.5px solid #ddd;

         }
         .advertising_seven{
             width:100%;
             height:200px;
             margin-bottom:10px;
         }
         .advertising_seven ul {
             height:100%;
             width:100%;
         }
         .advertising_seven ul li {
             float:left;
             width:50%;
             height:100%;
         }
         .advertising_seven ul li img{
             width:100%;
             height:100%;
            border: 0.5px solid #ddd;

         }
         .advertising_eight{
             width:100%;
             height:250px;
             margin-bottom:10px;
         }
         .eight_lfet{
             float:left;
             height:100%;
             width:35%;
         }
         .eight_lfet img{
             width:100%;
             height:100%;
            border: 0.5px solid #ddd;

         }
         .eight_right{
            float:left;
            width:65%;
            height:100%;
         }
         .eight_top{
            height:50%;
            width:100%;
         }
         .eight_top img{
            height:100%;
            width:100%;
            border: 0.5px solid #ddd;

         }
         .eight_bottom{
             height:50%;
             width:100%;
         }
         .eight_bottom ul{
             height:100%;
             width:100%;
         }
         .eight_bottom ul li {
             float:left;
             width:50%;
             height:100%;
         }
         .eight_bottom ul li img{
             width:100%;
             height:100%;
            border: 0.5px solid #ddd;

         }
         .advertising_ten{
             width:100%;
             height:250px;
             margin-bottom:10px;
         }
    </style>
<?php
    foreach ($ad_list as $k=>$vo):
    $lay_json = json_decode($vo['diy_json']);
    foreach ($lay_json as $k1=>$v):
?>

<?php if($v->mark == 'first') { ?>
<!-- ------------模块1-------------- -->
    <div class="advertising_three">
       <a class="jump_shopping" data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link??'' ?>" data-pic="<?= $pic_host.$v->img ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>"> <img src="<?= $pic_host.$v->img ?>" alt=""> </a>
    </div>
<?php }elseif ($v->mark == 'second') { ?>
<!-- ------------模块2-------------- -->
        <div class="advertising">
            <div class="advertising_left">
                <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
            </div>
            <div class="advertising_right">
                <div class="advertising_top">
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >    <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                </div>
                <div class="advertising_bottom">
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >   <img src="<?= $pic_host.$v->img3 ?>" alt=""> </a>
                </div>
            </div>
        </div>
    <?php }elseif ($v->mark == 'third') { ?>
<!-- ------------模块3-------------- -->
        <div class="advertising_four">
            <ul>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" ><img src="<?= $pic_host.$v->img1 ?>"" alt=""> </a>
                </li>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" ><img src="<?= $pic_host.$v->img2 ?>"" alt=""> </a>
                </li>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" ><img src="<?= $pic_host.$v->img3 ?>"" alt=""> </a>
                </li>
            </ul>
        </div>
<?php }elseif ($v->mark == 'fourt') { ?>
<!-- ------------模块4-------------- -->
        <div class="advertising_five">
            <div class="text_left">
                <ul>
                    <li>
                        <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
                    </li>
                    <li>
                        <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img3 ?>" alt=""> </a>
                    </li>
                </ul>
            </div>
            <div class="text_right">
                <ul>
                    <li>
                        <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                    </li>
                    <li>
                        <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link4 ?? '' ?>" data-pic="<?= $pic_host.$v->img4 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img4 ?>" alt=""> </a>
                    </li>
                </ul>
            </div>
        </div>
<?php }elseif ($v->mark == 'fifth') { ?>
<!-- ------------模块5-------------- -->
        <div class="advertising_six">
            <ul>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
                </li>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                </li>
            </ul>
        </div>
<?php }elseif ($v->mark == 'sixth') { ?>
<!-- ------------模块6-------------- -->
        <div class="advertising_seven">
            <ul>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
                </li>
                <li>
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                </li>
            </ul>
        </div>
<?php }elseif ($v->mark == 'seventh') { ?>
<!-- ------------模块7-------------- -->
        <div class="advertising_eight">
            <div class="eight_lfet">
                <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
            </div>
            <div class="eight_right">
                <div class="eight_top">
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                </div>
                <div class="eight_bottom">
                    <ul>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img3 ?>" alt=""> </a>
                        </li>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link4 ?? '' ?>" data-pic="<?= $pic_host.$v->img4 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img4 ?>" alt=""> </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
<?php }elseif ($v->mark == 'eighth') { ?>
<!-- ------------模块8-------------- -->
        <div class="advertising">
            <div class="advertising_top">
                <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
            </div>
            <div class="advertising_bottom">
                <ul>
                    <li>
                        <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                    <li>
                        <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img3 ?>" alt=""> </a>
                    </li>
                </ul>
            </div>
        </div>
    <?php }elseif ($v->mark == 'ninth') { ?>
<!-- ------------模块9-------------- -->
        <div class="advertising_eight">
            <div class="eight_lfet">
                <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
            </div>
            <div class="eight_right">
                <div class="eight_bottom">
                    <ul>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                        </li>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img3 ?>" alt=""> </a>
                        </li>
                    </ul>
                </div>
                <div class="eight_top">
                    <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link4 ?? '' ?>" data-pic="<?= $pic_host.$v->img4 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img4 ?>" alt=""> </a>
                </div>
            </div>
        </div>
<?php }elseif ($v->mark == 'tenth') { ?>
<!-- ------------模块10-------------- -->
        <div class="advertising_ten">
            <div class="eight_lfet">
                <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link1 ?? '' ?>" data-pic="<?= $pic_host.$v->img1 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img1 ?>" alt=""> </a>
            </div>
            <div class="eight_right">
                <div class="eight_bottom">
                    <ul>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link2 ?? '' ?>" data-pic="<?= $pic_host.$v->img2 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img2 ?>" alt=""> </a>
                        </li>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link3 ?? '' ?>" data-pic="<?= $pic_host.$v->img3 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img3 ?>" alt=""> </a>
                        </li>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link4 ?? '' ?>" data-pic="<?= $pic_host.$v->img4 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img4 ?>" alt=""> </a>
                        </li>
                        <li>
                            <a data-pk="<?= $pk?>" data-pid="<?= $pid?>"  data-url="<?= $v->link5 ?? '' ?>" data-pic="<?= $pic_host.$v->img5 ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_DIY ?>" class="jump_shopping" >  <img src="<?= $pic_host.$v->img5 ?>" alt=""> </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
<?php } ?>
<?php endforeach;  endforeach;  ?>
<!-- -------------------广告位end-------------------------------->
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

<div class="property-ann" style="line-height: 2em;margin-top: 0.5em; padding-bottom:50px;">
    <section class="notice" style="display: flex;">
        <div class="" style="margin: 0 auto;">
            <a style="color:#FCCA20" href="http://www.beian.miit.gov.cn">深圳市到家信息科技有限公司 粤ICP备16069247号</a>
        </div>
    </section>
</div>


<!-- footer部分 -->
 <?php 
    include(dirname(dirname(__FILE__)).'/public/foot.php');
?> 
<!-- end -->



<?php if($memberAuthRedPack){?>
    <div id="cdj-read-packet-index" data-go="/activities/red-pack-activity?houseId=<?= $memberAuthRedPack->house_id ?>">
        <img src="../static/images/activity/RedPacketIcon.png" alt="">
    </div>
<?php }?>

<?php
\common\widgets\JavascriptBlock::begin();
?>
<script type="text/javascript">
    $(".close").click(function(){
        var title = $(this).data('title');
        alert("该项目暂取消“" + title + "”业务！");
        return false;
    });
</script>
<?php
\common\widgets\JavascriptBlock::end();
?>
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


    $(".jump_shopping").on('click',function(){

        var _this = $(this);
        var _pk = _this.attr("data-pk");
        var _pid = _this.attr("data-pid");
        var _url = _this.attr("data-url");
        var clickPlace = $(this).attr('data-from');
        var pic = $(this).attr('data-pic');
        if(_url != ""){
            _url = _url +"/pk/" + _pk +"/pid/" + _pid;

            $.ajax({
                type: 'POST',
                url: '/default/ajax-third-parth-view-history',
                data: {houseId:<?= $houseID ?>,type:1,modelv:1,clickPlace:clickPlace,pic:pic},
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
        }

    });

    $('#cdj-read-packet-index').on('click', function (){
        var url = $(this).attr('data-go');

        location.href = url;
        return;
    });

</script>
