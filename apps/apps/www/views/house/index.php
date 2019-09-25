<?php
/**
 * @var $_user \apps\www\models\Member
 * @var $house \common\models\MemberHouse
 * @var $parking \common\models\MemberHouse
 * @var $houseRs
 * @var $parkingRs
 * @var \common\models\AuthHouseNotificationMember $memberAuthRedPack
 */
?>

<link rel="stylesheet" href="/static/css/base.css">


<style>
    #house-index .my_index{
        width:100%;
        height:130px;
    }
    #house-index .my_index ul li{
        float:left;
        width:25%;
        padding:0;
        text-align:center;
        margin-top:14px;
    }
    #house-index .my_index ul li i{
        display:block;
        width:32px;
        height:32px;
        background-size:100%;
        margin:auto;
    }
    #house-index .my_index ul li span{
        color:#000;
    }
    #house-index .my_index ul li i.house-o{
        background-image: url('/static/images/icon/house-o.png');
    }   
    #house-index .my_index ul li i.parking{
        background-image: url('/static/images/icon/parking.png');

    }
    #house-index .my_index ul li i.door{
        background-image: url('/static/images/icon/door.png');

    }
    #house-index .my_index ul li i.house-member{
        background-image: url('/static/images/icon/house-member.png');

    }
    #house-index .my_index ul li i.zhanghu{
        background-image: url('/static/images/icon/zhanghu.png');
    }
    #house-index .my_index ul li i.fapiao{
        background-image: url('/static/images/icon/fapiao.png');
    }
    #house-index .my_index ul li i.Coupons{
        background-image: url('/static/images/icon/Coupons.png');
    }
</style>

<div class="panel" id="house-index">
    <!-- <div class="banner"></div> -->
    <div class="mid">
        <div class="info">
            <img src="<?= isset($_user->headimg) ? Yii::getAlias($_user->headimg) : '' ?>">
            <h4><?php echo $_user->name ? $_user->name : $_user->nickname ?></h4>
        </div>
        <div class="my_index">
            <ul>
                <li>
                    <a href="/house/manager">
                        <i class="house-o"></i><span>房产管理</span>
                    </a>
                </li>
                <li>
                    <a href="/auth/?group=2">
                        <i class="parking"></i><span>车位认证</span>
                    </a>
                </li>
                <li>
                    <a onclick="app.tips().warning('即将上线，敬请期待')">
                        <i class="door"></i><span>车禁门禁</span>
                    </a>
                </li>
                <li>
                    <a href="/house/member">
                       <i class="house-member"></i><span>房屋成员</span>
                    </a>
                </li>
                <li>
                    <a href="/member">
                       <i class="zhanghu"></i><span>账户管理</span>
                    </a>
                </li>
                <li>
                    <a href="/tcis/lists">
                       <i class="fapiao"></i><span>发票历史</span>
                    </a>
                </li>
                <li>
                    <a href="/coupon/index">
                       <i class="Coupons"></i><span>我的优惠券</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- <div class="flex console">
            <div>
                <a href="/house/manager">
                    <i class="house-o"></i>房产管理
                </a>
            </div>
            <div>
                <a href="/auth/?group=2">
                    <i class="parking"></i>车位认证
                </a>
            </div>
            <div>
                <a onclick="app.tips().warning('即将上线，敬请期待')">
                    <i class="door"></i>车禁门禁
                </a>
            </div>
            <div>
                <a href="/house/member">
                    <i class="house-member"></i>房屋成员
                </a>
            </div>
            
        </div> -->
    </div>
    <div class="flex tab">
        <div class="active" data-target="house-list">
            <i class="house"></i>房产信息
            <div class="line"></div>
        </div>
        <div data-target="parking-list">
            <i class="parking"></i>车位管理
        </div>
    </div>
    <div class="tab-content">
        <div class="house-list">
            <?php if (sizeof($houseRs) > 0) { ?>
                <ul>
                    <?php foreach ($houseRs as $house) : ?>
                        <li>
                            <div class="house-list-n">
                                <i style="background-image:url(<?= Yii::getAlias($house->house->project->icon) ?>);background-size: contain;"></i>
                                <p><?= $house->house->showName ?></p>

                                <em class="color<?= $house->identity ?>"><?= $house->identityText ?></em>
                                <?php if ($house->status == \common\models\MemberHouse::STATUS_ACTIVE) { ?>

                                    <?php if(\common\models\SysSwitch::inVal('pauseWeChatPayment', $house->house->project_house_id)){?>
                                        <a onclick="app.tips().error('该项目暂取消“微信缴费”业务！')">查看物业账单</a>
                                    <?php } else {?>
                                        <a href="/house/choose-bill" data-origin="1">查看物业账单</a>
                                    <?php }?>

                                <?php } elseif ($house->status == \common\models\MemberHouse::STATUS_WAIT_REVIEW) { ?>
                                    <a href="javascript:void(0);" data-origin="1">信息审核中</a>
                                <?php } ?>
                            </div>

                            <div class="newwindow-house">
                                <h6 class="newwindow-house-n">(<?= $house->house->ancestor_name?>)</h6>
                            </div>

                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php } else { ?>
                <p>
                <div class="empty-status"><i></i>暂无认证房产</div>
                <div class="tac" style="padding-bottom:20px">
                   <!-- <a class="btn btn-primary" href="/auth/?group=1">认证房产</a>-->
                    <a class="btn btn-primary" id="goHouseBind" href="javascript:;">认证房产</a>
                </div>
                </p>
            <?php } ?>
        </div>
        <div class="parking-list">
            <?php if (sizeof($parkingRs) > 0) { ?>
                <ul>
                    <?php foreach ($parkingRs as $house) : ?>
                        <li>
                            <div class="house-list-n">
                                <i style="background-image:url(<?= isset($house->house->project->icon) ? Yii::getAlias($house->house->project->icon) : '' ?>);background-size: contain;"></i>
                                <p><?= $house->house->showName ?></p>

                                <em class="color<?= $house->identity ?>"><?= $house->identityText ?></em>
                                <?php if ($house->status == \common\models\MemberHouse::STATUS_ACTIVE) { ?>

                                    <?php if(\common\models\SysSwitch::inVal('pauseWeChatPayment', $house->house->project_house_id)){?>
                                        <a onclick="app.tips().error('该项目暂取消“微信缴费”业务！')">查看物业账单</a>
                                    <?php } else {?>
                                        <a href="/house/choose-bill" data-origin="1">查看物业账单</a>
                                    <?php }?>

                                <?php } elseif ($house->status == \common\models\MemberHouse::STATUS_WAIT_REVIEW) { ?>
                                    <a href="javascript:void(0);" data-origin="1">信息审核中</a>
                                <?php } ?>
                            </div>

                            <div class="newwindow-house">
                                <h6 class="newwindow-house-n">(<?= $house->house->ancestor_name?>)</h6>
                            </div>

                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php } else { ?>
                <p>
                <div class="empty-status"><i></i>暂无认证车位</div>
                <div class="tac" style="padding-bottom:20px">
                    <a class="btn btn-primary" href="/auth/?group=2">认证车位</a>
                </div>
                </p>
            <?php } ?>
        </div>
    </div>

    <div style="height: 6em;line-height: 6em;">

    </div>
</div>


<!-- footer部分 -->
 <?php 
    include(dirname(dirname(__FILE__)).'/public/foot.php');
?> 
<!-- end -->


<?php if($memberAuthRedPack){?>

    <div id="cdj-read-packet-index" data-go="/activities/red-pack?houseId=<?= $memberAuthRedPack->house_id ?>">
        <img src="../static/images/activity/RedPacketIcon.png" alt="">
    </div>

<?php }?>


<?php \common\widgets\JavascriptBlock::begin() ?>
    <script>
        $("#goHouseBind").on('click',function () {
            $.ajax({
                type: 'GET',
                dataType: "json",
                url: '/auth/ajax-house-count',
                data: "",
                timeout: 3000, //超时时间：30秒
                success: function (res) {
                    var _res = res.data;
                    if(_res.code == 1){
                        location.href = '/auth/?group=1';
                    }else{
                        alert(_res.message);
                    }
                }
            });
        });

        $('#house-index').on('loaded', function () {
            $('.tab>div').bind('click', function () {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
                $('.tab-content>div').hide();
                $('.tab-content').find('.' + $(this).attr('data-target')).show();
            })
        });
    </script>
<?php \common\widgets\JavascriptBlock::end(); ?>
