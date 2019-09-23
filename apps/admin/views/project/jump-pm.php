<?php
/** @var $key string */
/** @var $token string */
?>

<?php \common\widgets\JavascriptBlock::begin()?>

<script type="text/javascript">

    $(document).ready(function(){
        var JumpUrl = "http://<?= $key . '.' . Yii::$app->params['domain.pm']?>?token=<?= $token?>";
        window.location.href = JumpUrl;
    });

</script>
<?php \common\widgets\JavascriptBlock::end()?>