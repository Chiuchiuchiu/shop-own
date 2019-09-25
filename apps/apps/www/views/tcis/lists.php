<?php
/**
 * @var $id
 * @var \common\models\ArticleCategory $category
 * @var array $categoryList
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 */
$dataProvider->getModels();
?>
<div class="panel" id="order-fpzz-list" data-max-page="<?=$dataProvider->pagination->pageCount?>">
    <?php
    if ($dataProvider->count == 0)
        echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
    else
        echo $this->render('fpzz-list-cell', ['dataProvider' => $dataProvider]);
    ?>
</div>

<?php

\common\widgets\JavascriptBlock::begin(); ?>
<script>
    $('#order-fpzz-list').on('loaded',function(){
        window.app.scrollLoad($('#order-fpzz-list'),'');
    });
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>

