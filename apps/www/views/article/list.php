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
<div class="panel" id="article-list" data-max-page="<?=$dataProvider->pagination->pageCount?>">
    <div class="ui-row-flex category-list">
        <?php
        foreach ($categoryList as $value):
            ?>
            <div>
                <a href="/article/list?id=<?= $value->id ?>"
                   class="<?= $value->id == $id ? 'hover' : '' ?>"><?= $value->name ?></a>
            </div>
            <?php
        endforeach;
        ?>
    </div>
    <?php
    if($dataProvider->count==0)
        echo "<div class=\"empty-status\"><i></i>暂无资讯</div>";
    else
        echo $this->render('list-cell', ['dataProvider' => $dataProvider]);
    ?>
</div>

<?php

\common\widgets\JavascriptBlock::begin(); ?>
<script>
    $('#article-list').on('loadedPage', function () {
        var now = [];
        $('.date-hr', this).each(function (i, e) {
            if (!now[$(this).text()]) {
                now[$(this).text()] = 1;
                $(this).show();
            }
        })
    }).trigger('loadedPage');
    $('#article-list').on('loaded',function(){
        window.app.scrollLoad($('#article-list'),'/article/list?id=<?=$category->id?>');
    });
</script>
<?php \common\widgets\JavascriptBlock::end(); ?>

