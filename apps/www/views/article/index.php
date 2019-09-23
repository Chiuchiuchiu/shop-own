<?php
/**
 * @var $this \yii\web\View
 * @var $article \common\models\Article
 */
?>
<div class="panel" id="article">
    <h1><?=$article->title?></h1>
    <div class="info">
        <span><?=date('Y-m-d',$article->post_at)?></span>
        <span><?=$article->author?></span>
        <span style="color:#576B95"><?=$article->projectName?></span>
    </div>
    <article>
        <?php
        $content = $article->content;
        $content = preg_replace_callback('/height="[0-9]+"/i',function(){
            return '';
        },$content);
        echo $content;
        ?>
    </article>

    <?php \common\widgets\JavascriptBlock::begin()?>
    <script>
        $('#article').bind('loaded',function(){
            _czc.push(["_trackEvent",'文章阅读','<?=$_project?$_project->house_name:'unknown'?>','<?=$article->categoryName?>','<?=$article->title?>']);
        });
    </script>
    <?php \common\widgets\JavascriptBlock::end();?>
</div>
