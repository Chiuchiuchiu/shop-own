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
<div class="panel" id="feedback-list" data-max-page="<?= $dataProvider->pagination->pageCount ?>">
    <div class="ui-row-flex category-list">
        <div>
            <a href="/new-repair/list?status=0">我的报修</a>
        </div>
        <div>
            <a href="/feedback/list" class="hover">我的投诉</a>
        </div>
    </div>
    <?php
    if ($dataProvider->count == 0)
        echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
    else
        echo $this->render('list-cell', ['dataProvider' => $dataProvider]);
    ?>
</div>

<?php

\common\widgets\JavascriptBlock::begin(); ?>
<script>
    $('#feedback-list').on('loaded', function () {
        app.scrollLoad($('#feedback-list'), '/feedback/list');
    });
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>

