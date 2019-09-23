<?php

/* @var $this yii\web\View */
/* @var $channel */

$this->title = '首页';
?>

<?php \components\inTemplate\widgets\IBox::begin() ?>
    <h2 class="text-center"><span>欢迎来到 管理后台</h2>
<?php \components\inTemplate\widgets\IBox::end() ?>

<?php \components\inTemplate\widgets\IBox::begin(['title' => '清空缓存']); ?>

<div class="form-group">
    <div class="col-sm-2">
        <?= \yii\helpers\Html::a('清空缓存', '#', ['class'=>'btn btn-warning clean']); ?>
    </div>

</div>

<?php \components\inTemplate\widgets\IBox::end(); ?>


<?php \common\widgets\JavascriptBlock::begin(); ?>

<script>

    $('.clean').on('click', function (){
        $.getJSON('/default/clean-cache', function (res){
            if(res.code == 0){
                layer.msg('已清空');
            }
        })
    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
