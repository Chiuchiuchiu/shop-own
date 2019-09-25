<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\PropertyAnnouncement
 */
?>
<div class="panel" id="article">
    <h1><?= $model->title ?></h1>
    <div class="info">
        <span><?=date('Y-m-d',$model->created_at)?></span>
        <span><?=$model->author?></span>
        <span style="color:#576B95"><?= $model->projectName ?></span>
    </div>
    <article>
        <?php
        $content = $model->content;
        $content = preg_replace_callback('/height="[0-9]+"/i',function(){
            return '';
        },$content);
        echo $content;
        ?>
    </article>

    <?php \common\widgets\JavascriptBlock::begin()?>
    <script>
        $('#article').bind('loaded',function(){
            _czc.push(["_trackEvent",'文章阅读','<?=$_project?$_project->house_name:'unknown'?>','通知公告','<?=$model->title?>']);
        });
    </script>
    <?php \common\widgets\JavascriptBlock::end();?>
</div>
