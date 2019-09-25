<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 17:20
 */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \common\models\ButlerElectionActivity $model */
/* @var int $group */
/* @var int $projectId */
/* @var string $authUrl */
/* @var integer|null $voteId */
$dataProvider->getModels();

$groupName = $group == 1 ? '管家' : '保安';

$this->title = "中奥物业{$groupName}人员大评选活动！";

?>

<div id="vote-lists" class="panel">
    <div class="zz">
    </div>
    <div class="zz-div">
        <span class="close"><img src="../static/images/vote/close@2x.png"></span>
        <h1>管家详情</h1>

        <div class="zz-div-img">
            <img class="img butler_head_img" src="">
        </div>


        <p class="zz-div-title"></p>
        <p class="zz-div-tag">
        </p>
        <p class="zz-div-p">个人介绍</p>
        <p class="zz-div-detail"></p>
        <button class="vote-submit">立即投票</button>
    </div>

    <div class="zz-show-tips">
        <img class="zz-show-tips-img" src="">
    </div>

    <div class="renzheng">
		<span class="close">
			<img src="../static/images/vote/close@2x.png">
        </span>
        <img class="img" src="../static/images/vote/1756@2x.png">
        <p>您需要认证才可以进行投票</p>
        <button data-go="http://<?= $authUrl ?>/auth?group=1&project_id=<?= $projectId ?>">去认证</button>
    </div>

    <figure>

        <?php if($group == 1){ ?>
            <img src="../static/images/vote/banner@2x.png" width="100%" height="100%">
        <?php } else { ?>
            <img src="../static/images/vote/weishi.png" width="100%" height="100%" alt="">
        <?php } ?>

    </figure>
    <section >
        <?php
        if ($dataProvider->count == 0) {
            echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
        } else { ?>

            <ul id="vote-lists-li" data-max-page="<?=$dataProvider->pagination->pageCount?>">
                <?php echo $this->render('vote-lists-cell', ['dataProvider' => $dataProvider, 'group' => $group, 'voteId' => $voteId]); ?>
            </ul>

        <?php } ?>

    </section>

    <?php \components\za\ActiveForm::begin(['id' => 'put-in']) ?>
        <input type="hidden" id="put-in-data" name="data[id]" value="">
        <input type="hidden" name="data[group]" value="<?= $group ?>">
        <input type="hidden" name="data[projectId]" value="<?= $projectId ?>">
    <?php \components\za\ActiveForm::end(); ?>

</div>

<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript"></script>
<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $(function () {
        var html = document.documentElement;
        var hWidth = html.getBoundingClientRect().width;
        html.style.fontSize = hWidth / 15 + "px";

        /*为两个字和四个字自动匹配相应的padding,P自动匹配相应的padding*/
        var a = $("span").eq(1).text();

        /*点击每个li弹出每个管家详情页*/
        $(document).on('click', 'ul li', function () {
            var _self = $(this);
            var _click_class = $(this).attr('class');
            var _data_id = $(this).attr('data-id');

            $('.butler_head_img').attr('src', '').attr('src', $('.' + _click_class + '_img').attr('src'));
            $('.zz-div-title').html('').html($('.' + _click_class + '_title').html());
            $('.zz-div-tag').html('').html($('.' + _click_class + '_span').html());
            $('.zz-div-detail').html('').html($('.' + _click_class + '_introduce').html());
            $('#put-in-data').val(_data_id);

            $("body,html").css({"overflow": "hidden"});
            $(".zz-div").css("left", ($(window).width() - $(".zz-div").outerWidth()) / 2);
            $(".zz-div").css("top", 20);
            $(".zz").css("display", "block");
            $(".zz-div").css("display", "block");
            $(".zz").css("top", $(window).scrollTop());

            $(this).children('.toupiao-toupiao').children('a').removeClass('toupiao-voted').addClass('toupiao-voted');

        });

        $('.zz').click(function () {
            $("body,html").css({"overflow": "auto"});
            $(".zz").css("display", "none");
            $(".zz-div, .zz-show-tips, .renzheng").css("display", "none");
            $('.zz-show-tips').removeClass('faile success');
        });

        $('.close').click(function () {
            $("body,html").css({"overflow": "auto"});
            $(".zz").css("display", "none");
            $(".zz-div, .zz-show-tips, .renzheng").css("display", "none");
            $('.zz-show-tips').removeClass('faile success');
        });

        $('.vote-submit').on('click', function (){
            var _v = $('#put-in').serialize();
            $.ajax({
                type:"POST",
                url:'vote',
                data:_v,
                beforeSend: function (){

                },
                success: function (res){
                    if(res.code == 0){
                        $('.zz-div').hide();
                        $('.zz-show-tips').addClass(res.data.class);
                        $('.zz-show-tips-img').attr('src', res.data.img);
                        $(".zz-show-tips").css("left", ($(window).width() - $(".zz-show-tips").outerWidth()) / 2);
                        $(".zz-show-tips").css("top", $(window).scrollTop() + ($(window).height() - $(".zz-show-tips").height()) / 2);
                        $('.zz-show-tips').show();
                        $('.' + res.data.nClass).html(res.data.number);
                    } else if(res.code == 2){
                        $('.zz-div').hide();
                        $('.zz-show-tips').addClass(res.data.class);
                        $('.zz-show-tips-img').attr('src', res.data.img);
                        $(".zz-show-tips").css("left", ($(window).width() - $(".zz-show-tips").outerWidth()) / 2);
                        $(".zz-show-tips").css("top", $(window).scrollTop() + ($(window).height() - $(".zz-show-tips").height()) / 2);
                        $('.zz-show-tips').show();
                    } else if(res.code == -2){
                        $('.zz-div').hide();
                        $(".renzheng").css("left", ($(window).width() - $(".renzheng").outerWidth()) / 2);
                        $(".renzheng").css("top", $(window).scrollTop() + ($(window).height() - $(".renzheng").height()) / 2);
                        $(".renzheng").css("display", "block");
                        $('.renzheng').show();
                    } else {
                        alert(res.message);
                    }
                },
                dataType:'JSON'
            });
        });

        $('#vote-lists').on('loaded',function(){
            window.app.scrollLoad($('#vote-lists-li'),'/activities/vote-lists?group=<?= $group ?>');
        });

        wx.ready(function () {
            var _title = '中奥物业<?= $groupName ?>人员大评选.';
            var _desc = '为评选出最佳的<?= $groupName ?>人员，拉近<?= $groupName ?>人员与业主之间的的关系，故举办本次活动'; // 分享描述
            var _url = 'https://<?= $authUrl ?>/activities/vote-lists?projectId=<?= $projectId ?>';// 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
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
