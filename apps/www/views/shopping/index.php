 <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <title><?= $cdj_header_tip ?> | 商城</title>
        <link rel="stylesheet" href="/static/css/base.css" />
        <link rel="stylesheet" href="/static/css/style.css" />
        <link rel="stylesheet" href="/static/css/swiper.min.css">
        <link rel="stylesheet" href="/static/css/mui.min.css">
        <script src="/static/js/jquery-2.1.1.min.js"></script>
        <script src="/static/js/layer_mobile/layer.js"></script>
        <style type="text/css">
             .swiper-container {
            width: 100%;
            height: 150px;
            }
            .swiper-button-next, .swiper-button-prev{
                height:20px;
                width:28px;
            }
            .navBar{
                height:55px;
            }
            .navBar>ul{
                margin-top:10px;
            }
            .navBar>ul>li{
                float:left;
                height:55px;
                width:20%;
            }
            .navBar>ul>li>a{
                display:block;
                position:relative;
                text-align:center;
                height:55px;
            }
            .acceptance{
                position: absolute;
                right: 17%;
                top: -7px;
                width:24px;
                height:18px;
                background: -webkit-linear-gradient(top, #E4393C, #ff6666);
                font-size:9px;
                transform: scale(0.8);
                color:#fff;
                border-radius:8px;
                text-align: -webkit-center;
                font-style:normal;
            }
            .navBar>ul>li>a>img{
                display:block;
                height:24px;
                width:24px;
                margin:auto; 
            }
            .navBar>ul>li>a>span{
                color:#000;
                height:20px;
            }

            /* 搜索栏 */
            input[type=search]{
                text-align:left;
                padding-left:20px !important;
            }
            .mui-search{
                position: sticky;
                top: 0;
                z-index: 999999;
            }
            .search_icon{
                position: absolute;
                right: 10px;
                top:6px;
                background-image: url("/static/images/vote/search.png");
                background-repeat: no-repeat; 
                background-position: 0px 0px; 
                background-size:100%;
                width: 20px; 
                height: 20px; 
            }
           
            /* 合作商家 */
            .collaborate{
                height:76px;
                background-color:#fff;
                overflow:hidden;
            }
            .cooperation{
                float:left;
                display:block;
                width:30px;
                font-size:12px;
                line-height:1;
                padding: 12px 0 0 8px;
            }

            .collaborate_box{
                height:86px;
                overflow-y: hidden;
                position: relative;
            }
            .collaborate_nav{
                left: 0px;
                top: 0px;
                position: absolute;
                white-space: nowrap;
                margin-top:10px;
                display: flex;
            }
            .collaborate_nav li{
                float:left;
                margin-left:10%;
                width:50px;
                height:50px;
                text-align:center;
            }
            .collaborate_nav span{
                display:block;
                margin-top:5px;
                font-size:12px;
                color:#000;
                text-align:center;
            }
            /* 推荐商品 */
            .recommend{
                background-color:#fff;
                margin-top:5px;
            }
            .tuijian{
                width:100%;
                height:30px;
                line-height: 30px;
                text-align: center;
                color: #000;
                border-bottom: 1px solid #ddd;
            }
            .tuijian:before{
                content: "";
                width:15px;
                height:2px;
                background:#ddd;
                display:block;
                position:relative;
                top:16px;
                left:38%;
            }
            .tuijian:after{
                content:"";
                width:15px;
                height:2px;
                background:#ddd;
                display:block;
                position:relative ;
                top:-16px;
                left: 58%;
            }
            .recommend_shop{
                width:100%;
                height:auto;
                display:inline-block;
            }
            .recommend_navbar{
                width:100%;
                display:inline-block;
                padding:0 5px;
            }
            .recommend_navbar > li {
                float:left;
                width:49.3%;
                margin: 0 0.7% 0.7% 0;
                margin-bottom:2px;
            }
            .items-pic{
                height:175px;
            }
            .items-pic img{
                width:100%;
                height:100%;
            }
            .goods-list > li:nth-of-type(1) {
                font-size: 14px;
                color: #333333;
                text-overflow: ellipsis;
                overflow: hidden;
                white-space: nowrap;
                margin-bottom: 5px;
            }
            .goods-list > li > a {
                display: block;
                color: #333;
                text-overflow：ellipsis;
                overflow：hidden;
                white-space：nowrap;
            }
            .goods-list-price {
                overflow: hidden;
                display: inline-block;
            }
            .goods-list-price i{
                font-size:18px;
            }
            .goods-list-price > span {
                color: #E4393C;
                font-size:14px;
            }
            .module-item-rush-buy {
                float:right;
                width: 48px;
                background-color: #E4393C;
                border-radius: 4px;
                text-align: center;
                font-size: 12px;
                line-height: 18px;
                margin-right:5px;
            }
        </style>
    </head>
<body class="bg-f1">
    <div class="contented"  style="margin-bottom: 55px;">
        <!-- ----------搜索框-------------- -->
            <div class="mui-input-row mui-search ">
                    <i class="search_icon"></i>
					<input type="search" class="mui-input-clear" id="kw" value="<?= $kw ?>" placeholder="搜索商品" style="margin-bottom:5px;">
			</div>
            
        <!-- ----------搜索框end-------------- -->

        <!-- ----------轮播图-------------- -->
        <?php if(!empty($ad_list)) { ?>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php foreach ($ad_list as $k=>$vo): ?>
                        <div class="swiper-slide">
                           <a href="<?= $vo['url'] ?? 'javascript:;' ?>"> <img src="<?=  $vo->pic ?>" alt="" style="width:100%;height:150px;"> </a>
                        </div>
                    <?php endforeach; ?>

                </div>
                <!-- 如果需要分页器 -->
                <div class="swiper-pagination"></div>

                <!-- 如果需要导航按钮 -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>

                <!-- 如果需要滚动条 -->
                <!-- <div class="swiper-scrollbar"></div> -->
            </div>
        <?php } ?>
        <!-- ----------轮播图end-------------- -->
        
        <!-- ------合作商家----------- -->
            <div class="collaborate clearfix">
                <span class="cooperation">合作商家</span>
                <div class="collaborate_box">
                    <ul class="collaborate_nav">
                        <?php foreach ($shopList as $k=>$vo):   ?>
                        <li class="icon_click" data-pic="<?= $vo['logo'] ?>" data-url="<?= $shopping_host ."/Mobile/Index/index/sid/" .$vo['id'] ."/pid/" .$pid ."/pk/" .$pk ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_ICON ?>">
                            <i class="icon">
                            <img src="<?= $vo['logo'] ?>"  width="32"  height="32">
                            </i>
                            <span><?= $vo['icon_name'] ?></span>
                        </li>
                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>
        <!-- ------合作商家end----------- -->

        <!-- ------推荐商品-------------- -->
            <div class="recommend">
                <input type="hidden" id="detailUrl" value="<?= $shopping_host ."/Mobile/Shop/detail/pk/" .$pk ."/pid/" .$pid ?>" />
                <p class="tuijian">推荐</p>
                <div class="recommend_shop">
                    <ul class="recommend_navbar">
                        <?php foreach ($reGoods as $k=>$vo):
                            $imgs = $vo['imgs'];
                            $price = $vo['price'];
                            $name = $vo['name'];
                            if(!empty($vo['sku_price'])){
                                $price = $vo['sku_price'];
                            }
                            $parr = explode(".",$price);
                            $price_int = $parr[0];
                            $price_float = $parr[1];
                      //  var_dump($imgs);die;
                            $arr = explode(",",$imgs);
                            $img = '/Public/Uploads/'. $arr[0];
                            ?>
                        <li class="godetail" data-pic="<?= $shopping_host .$img ?>" data-from="<?= \common\models\ThirdpartyViewHistory::CLICK_Goods ?>" data-id="<?= $vo['goods_id']  ?>"  data-sid="<?= $vo['shop_id'] ?>" data-url= "<?= $shopping_host ."/Mobile/Shop/detail/pk/" .$pk ."/pid/" .$pid ?>">
                            <div class="items-pic">
                                <img src="<?= $shopping_host .$img ?>" alt="">
                            </div>
                            <ul class="goods-list">
                                <li><?= $name ?></li>
                                <li>
                                    <div class="goods-list-price">
                                        <span>￥<i><?= $price_int ?></i>.<?= $price_float ?></span>
                                    </div>
                                    <p class="goods-lists-more module-item-rush-buy">
                                        <span style="font-size: 8px; color: #fff; padding:4px 0;">立即购买</span>
                                    </p>
                                </li>
                            </ul>
                        </li>
                        <?php endforeach; ?>

                    </ul>
                </div>
            </div>
        <!-- ------推荐商品end-------------- -->
    </div>

 <!-- footer部分 -->
 <?php 
    include(dirname(dirname(__FILE__)).'/public/foot.php');
?> 
<!-- end -->



<script src="/static/js/swiper.min.js"></script>
<script>
    $(function() {
        $(".search_icon").on("click",function () {
            var kw = $("#kw").val();

            location.href = "?kw=" + kw;
        });

        $(".icon_click").on('click', function () {
            var _this = $(this);

            var url = _this.attr("data-url");
            var clickPlace = $(this).attr('data-from');
            var pic = $(this).attr('data-pic');

            $.ajax({
                type: 'POST',
                url: '/default/ajax-third-parth-view-history',
                data: {houseId:<?= $houseID ?>, type: 1, modelv: 1, clickPlace: clickPlace, pic: pic},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    location.href = url;
                },
                fail: function (res) {
                    console.log(res);
                    location.href = url;
                }
            });
        });

        $(".godetail").on('click', function () {

            var _this = $(this);
            var _id = _this.attr("data-id");
            var _sid = _this.attr("data-sid");
            var _url = _this.attr("data-url");
            var clickPlace = $(this).attr('data-from');
            var pic = $(this).attr('data-pic');

            _url = _url + "/sid/" + _sid + "/id/" + _id;

            $.ajax({
                type: 'POST',
                url: '/default/ajax-third-parth-view-history',
                data: {houseId:<?= $houseID ?>, type: 1, modelv: 1, clickPlace: clickPlace, pic: pic},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    location.href = _url;
                },
                fail: function (res) {
                    console.log(res);
                    location.href = _url;
                }
            });
        });
    });
    //轮播图
     var mySwiper = new Swiper('.swiper-container', {
            direction: 'horizontal', // 垂直切换选项
            loop: true, // 循环模式选项
            autoplayDisableOnInteraction: false,
            speed: 300,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false
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
            //     el: '.swiper-scrollbar',
            // },
        });
</script>
</body>
</html>
