<?php

/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;

$asset = \apps\www\assets\AppAsset::register($this);
$this->registerJs("requirejs.config({baseUrl:'" . $asset->baseUrl . "/js'});require(['app']);");
$title = $this->title;
if(empty($title) && $this->params['_project'] instanceof \common\models\Project){
    $title = '到家科技 | ' . $this->params['_project']->house_name;
}
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?=empty($title)?'到家科技': Html::encode($title) ?></title>
    <?= Html::csrfMetaTags() ?>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="full-screen" content="true">
    <meta name="x5-fullscreen" content="true">
    <meta name="360-fullscreen" content="true">
    <meta content="telephone=no" name="format-detection" />
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
    <meta name="keywords" content="财到家">
    <meta name="Description" content="粽情粽意—端午节中奥温情回馈业主，10000份粽子免费送！">
    <?php $this->head() ?>
</head>
<body class="">
<?php $this->beginBody() ?>

<?php /**
 * 节日banner
if (!Yii::$app->request->cookies->get('holidayPoster')) {
    $res = Yii::$app->response->cookies->add(
        new \yii\web\Cookie([
            'name' => 'holidayPoster',
            'value' => 1,
        ])
    );
    ?>
    <div id="holiday-poster" style="position: fixed;z-index: 9999;
left:0;
right: 0;top:0;bottom: 0;
background:url(http://cdn.51homemoney.com/dz.jpg) no-repeat center #fff;
background-size:contain;">
    </div>
    <script>
        function hideHoliday() {
            setTimeout(function(){
                document.getElementById('holiday-poster').style.display='none';
            },3500)
        }
    </script>
    <img src="http://cdn.51homemoney.com/dz.jpg" onload="hideHoliday();" style="display: none"
<?php } **/?>

<div id="tips-box"></div>
<main>
    <div id="project-window">
        <div id="project-main">
            <div id="project-body"><?= $content ?></div>
        </div>
        <div id="project-out-view"></div>
    </div>
    <div id="project-cutscenes"></div>
</main>
<div id="ui-loading">
    <div class="timer"></div>
    <p>玩命加载中</p>
</div>
<?php

if (isset($this->params['_wechatJs'])) { ?>
    <script src="//res.wx.qq.com/open/js/jweixin-1.1.0.js" type="text/javascript"></script>
    <script>
        wx.config({
            debug: <?=isset($this->params['_wechat_is_debug'])?$this->params['_wechat_is_debug']:'false'?>,
            appId: '<?=$this->params['_wechatJs']['appId']?>',
            timestamp: '<?=$this->params['_wechatJs']['timestamp']?>',
            nonceStr: '<?=$this->params['_wechatJs']['nonceStr']?>',
            signature: '<?=$this->params['_wechatJs']['signature']?>',
            jsApiList: ['chooseWXPay','startSearchBeacons','stopSearchBeacons','onSearchBeacons', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'hideAllNonBaseMenuItem', 'hideMenuItems']
        });
    </script>
<?php } ?>
<?php $this->endBody() ?>
<?php if(YII_ENV == YII_ENV_PROD){ ?>
<div style="display:none">
    <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1261302000'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1261302000' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
<?php } ?>
</body>
</html>
<?php $this->endPage() ?>
