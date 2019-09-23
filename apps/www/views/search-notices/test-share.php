
<style>

</style>

<div class="panel" id="new-repair-list" >
    <sapn><?=  $groupName ?></sapn>
</div>


<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $(function () {
        wx.config({
            debug: false,
            appId: '',
            timestamp: '',
            nonceStr: '',
            signature: '',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中-
                'onMenuShareAppMessage', //分享给朋友
                'onMenuShareTimeline',  //分享到朋友圈
                'onMenuShareQQ',	//分享到qq
                'onMenuShareQZone',	//分享到qq 空间
                'onMenuShareWeibo' //分享到腾讯微博
            ]
        });
        wx.ready(function () {
            var _title = '中奥物业<?= $groupName ?>人员大评选.';
            var _desc = '为评选出最佳的<?= $groupName ?>人员，拉近<?= $groupName ?>人员与业主之间的的关系，故举办本次活动'; // 分享描述
            var _url = '';// 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            var _picUrl = 'http://<?= Yii::$app->params['domain.www'] ?>/static/images/vote/vote-butler-se.png';

            wx.onMenuShareAppMessage({
                title: _title, // 分享标题
                desc: _desc, // 分享描述
                link: _url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: _picUrl, // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });


            wx.onMenuShareTimeline({ //分享到朋友圈
                title: _title, //分享的标题
                link: _url,   //分享的链接
                imgUrl: _picUrl,  //分享的图片
                trigger: function (res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    // alert('用户点击分享到朋友圈');
                },
                success: function (res) {
                    // alert('已分享');
                },
                cancel: function (res) {
                    // alert('已取消');
                },
                fail: function (res) {
                    // alert(JSON.stringify(res));
                }
            });

            wx.onMenuShareQQ({ //分享到QQ
                title: _title, // 分享标题
                link: _url, // 分享链接
                imgUrl: _picUrl, // 分享图标
                desc: _desc,
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareQZone({ //分享到QQ空间
                title: _title, // 分享标题
                link: _url, // 分享链接
                imgUrl: _picUrl, // 分享图标
                desc: _desc,
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

            wx.onMenuShareWeibo({ //分享到腾讯微博
                title: _title, // 分享标题
                link: _url, // 分享链接
                imgUrl: _picUrl, // 分享图标
                desc: _desc,
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

