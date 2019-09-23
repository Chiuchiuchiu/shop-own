<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/22
 * Time: 14:05
 */
/* @var integer $projectHouseId */
/* @var $this \yii\web\View */
/* @var string $projectUrlKey */


$this->title = '粽情粽意—端午节中奥温情回馈业主，10000份粽子免费送！';
?>

<div id="dragon-view">
    <div class="logo identical">
        <img src="../static/images/activity/dragon/logo.png" alt="">
    </div>
    <div class="dragon-title identical">
        <img src="../static/images/activity/dragon/title.png" alt="">
    </div>
    <div class="dragon-content identical">
        <div align="center">
            <h2 style="align-content:center">重要提示</h2>
        </div>
        <p>1.首次成功认证业主身份，可领取粽子一份。</p>
        <p>2.已认证业主，在线缴纳1个月及以上物管费，可领取粽子一份。</p>
        <p>3.每套房产仅可领取一次。</p>
        <p>4､先到先得，送完为止，请业主及早参与，礼品发放可咨询物管处。</p>
        <p>∗活动最终解释权归活动方所有</p>
    </div>
    <div class="button identical">

        <?php if (!in_array($projectHouseId, Yii::$app->params['dragon_boat_activities']['project_lists'])) { ?>
            <img src="../static/images/activity/dragon/button.png" onclick="app.tips().warning('该项目不在活动范围内');" alt="">
        <?php } else {?>
            <img src="../static/images/activity/dragon/button.png" data-go="/activities/check-receive" alt="">
        <?php } ?>

    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $(function (){

        wx.ready(function (){
            wx.onMenuShareTimeline({
                title: '粽情粽意—端午节中奥温情回馈业主，10000份粽子免费送！', // 分享标题
                link: 'http://<?= $projectUrlKey ?>.<?= Yii::$app->params['domain.p'] ?>/activities/dragon' , // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: 'http://<?= Yii::$app->params['domain.www'] ?>/static/images/activity/dragon/title.png', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareAppMessage({
                title: '粽情粽意—端午节中奥温情回馈业主，10000份粽子免费送！', // 分享标题
                desc: '粽情粽意—端午节中奥温情回馈业主', // 分享描述
                link: 'http://<?= $projectUrlKey ?>.<?= Yii::$app->params['domain.p'] ?>/activities/dragon', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: 'http://<?= Yii::$app->params['domain.www'] ?>/static/images/activity/dragon/title.png', // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });

    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
