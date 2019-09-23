<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/11/22
 * Time: 09:29
 */
/* @var $this \yii\web\View */
/* @var $tips string */
/* @var $style string */

$this->title = '公众号受权-';
?>

<?php $this->beginBlock('cssFile')?>
<style type="text/css">
    .mini-p{
        margin: 0 auto;
        padding: 0;
    }
    button{
        margin: 0 auto;
        width: 18em;
        height: 4em;
        border-radius: 2em;
        background-color: #f7b500;
        border: 0;
        color: #fff;
        font-size: 13px;
        display: block;
    }
    .tips-icon{
        height: 13em;
    }
    .success{
        background-image: url('http://cdn.51homemoney.com/icon/success.png');
        background-repeat: no-repeat;
        background-size: 33%;
        background-position: center;
    }
    .fail{
        background-image: url('http://cdn.51homemoney.com/icon/error.png');
        background-repeat: no-repeat;
        background-size: 33%;
        background-position: center;
    }
</style>
<?php $this->endBlock('cssFile')?>

<div class="mini-p">
    <div class="<?= $style ?> tips-icon">

    </div>
    <div style="text-align: center;"><?= $tips ?></div>
    <button ontouchstart="reLaunch()">返回小程序</button>
</div>

<?php $this->beginBlock('jsFile')?>
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script type="text/javascript">
    wx.miniProgram.getEnv(function (res){
        console.log(res.miniprogram)
    })
    function reLaunch(){
        wx.miniProgram.reLaunch({
            url: '/pages/index/main'
        })
    }
</script>
<?php $this->endBlock('jsFile')?>
