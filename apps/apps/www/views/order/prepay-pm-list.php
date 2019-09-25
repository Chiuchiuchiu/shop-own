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
<div class="panel" id="order-prepay-pm-list" data-max-page="<?=$dataProvider->pagination->pageCount?>">
    <div class="ui-row-flex category-list"><div>
        <a href="/order/pm-list" class="">物业缴费</a>
    </div>
    <div>
        <a href="javascript:void(0);" class="hover">物管费预缴</a>
    </div></div>
    <?php
    if ($dataProvider->count == 0)
        echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
    else
        echo $this->render('prepay-pm-list-cell', ['dataProvider' => $dataProvider]);
    ?>
</div>

<?php

\common\widgets\JavascriptBlock::begin(); ?>
<script>
    $('#order-prepay-pm-list').on('loaded',function(){
        window.app.scrollLoad($('#order-prepay-pm-list'),'');
    });
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>

