<?php
 
$this->title = '我的生活';
?>

<?php $this->beginBlock('cssFile')?>
<style type="text/css">
   
</style>
<?php $this->endBlock('cssFile')?>

<div class="mini-p">
 <span>hello</span>
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
