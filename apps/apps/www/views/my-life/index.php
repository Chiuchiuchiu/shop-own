 <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <title><?= $cdj_header_tip ?> | 我的生活</title>
        <link rel="stylesheet" href="/static/css/base.css" />
        <link rel="stylesheet" href="/static/css/style.css" />
        <script src="/static/js/jquery-2.1.1.min.js"></script>
        <script src="/static/js/layer_mobile/layer.js"></script>
        <style type="text/css">
          
        </style>
    </head>
        <style>
            .service p,
            .community p,
            .my_house p{
                font-size:18px;
                background-color:#fff;
                padding-bottom:5px;
                padding:5px 0 0 9px;
                margin-top:5px;
            }
            .sort_list{
                background-color:#fff;
            }
            .sort_list li{
                float:left;
                width:25%;
            }
            .sort_list li a{
                display: block;
                position: relative;
                padding: 10px 0;
                text-align: center;
                color: #666;
            }
             .sort_list li a img {
                margin-top: 7px;
            }
            .sort_list li a .icon{
                width: 40px;
                height: 40px;
                display: block;
                margin: 0 auto 4px;
                background: no-repeat;
                background-size: 100% auto;
                border-radius:15px;
            }
            .community{
                margin-top:10px;
            }

            .header_nav{
                height:40px;
                background-color:#FCC713;
                text-align:center;
                line-height:40px;
                font-size:18px;
                font-weight:500;
                color:#f0f0f0;
            }
        </style>


<body class="bg-f1">
<div class="container">
    <header class="header_nav">
        <p>生活</p>
    </header>
     <div class="service">
             <p>物业缴费</p>
             <ul class="sort_list clearfix">
                 <li>
                     <a href="/house/choose-bill">
<!--                     <a onclick="alert('系统升级，该功能暂时关闭！')">-->
                         <i class="icon " style="background-color:#43ADE7;">
                             <img src="/static/images/ico/jiaofei.png" width="28" height="28">
                         </i>
                         <span>物业缴费</span>
                     </a>
                 </li>
                 <li>
                     <a href="/new-repair/list?status=0">
<!--                     <a onclick="alert('系统升级，该功能暂时关闭！')">-->
                         <i class="icon " style="background-color:#FD781D;">
                             <img src="/static/images/ico/gongg.png" width="28" height="28">
                         </i>
                         <span>公共维修</span>
                     </a>
                 </li>
                 <li>
                     <a href="/repair/personal-repair">
<!--                     <a onclick="alert('系统升级，该功能暂时关闭！')">-->
                         <i class="icon " style="background-color:#FFBD26;">
                             <img src="/static/images/ico/shangmen.png" width="28" height="28">
                         </i>
                         <span>上门维修</span>
                     </a>
                 </li>
                 <li>
                     <a href="/new-repair/list?status=0&flowStyleID=8">
<!--                     <a onclick="alert('系统升级，该功能暂时关闭！')">-->
                         <i class="icon " style="background-color:#43ADE7;">
                             <img src="/static/images/ico/tousu.png" width="28" height="28">
                         </i>
                         <span>投诉/建议</span>
                     </a>
                 </li>
                 <li>
                     <a href="/article/list/">
                         <i class="icon " style="background-color:#FE6868;">
                             <img src="/static/images/ico/shequdongtai.png" width="28" height="28">
                         </i>
                         <span>社区动态</span>
                     </a>
                 </li>
                 <li>
                     <a href="/life-service/mobile?">
                         <i class="icon " style="background-color:#43ADE7;">
                             <img src="/static/images/ico/huafei.png" width="28" height="28">
                         </i>
                         <span>话费充值</span>
                     </a>
                 </li>
                 <?php foreach($aUrl as $key => $urlV){?>
                     <?= $urlV ?>
                 <?php }?>

                 <?php if($MeterCount>0){?>
                     <li>
                         <a href="/meter/index">
                             <i class="icon " style="background-color:#6281FB;">
                                 <img src="/static/images/ico/shuidian.png" width="28" height="28">
                             </i>
                             <span>水电抄表</span>
                         </a>
                     </li>
                 <?php } ?>
                 <li>
                     <a href="/life-service/telephone">
                         <i class="icon " style="background-color:#43ADE7;">
                             <img src="/static/images/ico/bianming.png" width="28" height="28">
                         </i>
                         <span>便民电话</span>
                     </a>
                 </li>
                 <li>
                     <a href="/activities/vote-lists?projectId=<?= $projectInfo->house_id ?>">
                         <i class="icon " style="background-color:#FD781D;">
                             <img src="/static/images/ico/guanjia.png" width="28" height="28">
                         </i>
                         <span>最美管家</span>
                     </a>
                 </li>
                 <li>
                     <a href="/search-notices/list">
                         <i class="icon " style="background-color:#43ADE7;">
                             <img src="/static/images/ico/xunwu.png" width="28" height="28">
                         </i>
                         <span>寻物启事</span>
                     </a>
                 </li>

                 <li>
                     <a onclick="javascript:window.location.href='https://shop.51homemoney.com/shop/join'">
                         <i class="icon " style="background-color:#FFBD26;">
                             <img src="/static/images/ico/jiaru.png" width="28" height="28">
                         </i>
                         <span>商务合作</span>
                     </a>
                 </li>
<!--                 <?php /*if(Yii::$app->params['temp_add_href']['status']){*/?>
                 <li>
                     <a onclick="javascript:window.location.href='<?/*= Yii::$app->params['temp_add_href']['href']*/?>'">
                         <i class="icon " style="background-color:#FFBD26;">
                             <img src="/static/images/ico/jiaru.png" width="28" height="28">
                         </i>
                         <span>加入我们</span>
                     </a>
                 </li>
                 <?php /*} else {*/?>
                 <li>
                     <a href="/page/about/">
                         <i class="icon ">
                             <img src="/static/images/ico/ico7.png" width="28" height="28">
                         </i>
                         <span>关于我们</span>
                     </a>
                 </li>
                 --><?php /*} */?>

             </ul>
         </div>


    <?php foreach ($shopList as $k=>$vo): ?>
        <div class="community">
            <p><?= $vo['cate_name'] ?></p>
            <ul class="sort_list clearfix">
                <?php  foreach ($vo['shop_list'] as $mk => $nv):  ?>
                    <li>
                        <a class="shopJump" data-url="<?= $nv['url'] .'/pk/'.$pk ."/pid/" .$pid ?>" data-pic="<?= $nv['logo'] ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_ICON ?>" >
                            <i class="icon ">
                                <img src="<?= $nv['logo'] ?>"  width="40"  height="40" style="border-radius:15px;margin-top:0px;">
                            </i>
                            <span><?= $nv['icon_name'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach;  ?>


    <div class="community">
        <p>旅游</p>
        <ul class="sort_list clearfix" style="margin-bottom:50px;">
            <li>
                <a href="javascript:;" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_ICON ?>" id="jzyJump">
                    <i class="icon " >
                        <img src="/static/images/ico/jzy_logo.png"  width="32"  height="32">
                    </i>
                    <span>旅游服务</span>
                </a>
            </li>
        </ul>
    </div>

</div>

 <!-- footer部分 -->
 <?php 
    include(dirname(dirname(__FILE__)).'/public/foot.php');
?> 
<!-- end -->
<script>
    $(function(){
        // 联想家
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
            var _this = $(this);

            var url = _this.attr("data-url");
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

    })
</script>
</body>
</html>
