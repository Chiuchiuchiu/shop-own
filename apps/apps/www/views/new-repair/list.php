<?php
/**
 * @var $id
 * @var \common\models\ArticleCategory $category
 * @var array $categoryList
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 * @var $status
 * @var bool|integer $hasHouse
 * @var string $projectName
 * @var string $flowStyleID
 */
$dataProvider->getModels();
?>

<div class="panel" id="new-repair-list" data-max-page="<?= $dataProvider->pagination->pageCount ?>">
    <div class="topPath">

        <?php if($flowStyleID == 'w'){?>
            <a href="<?= $hasHouse ? '/new-repair?flowStyleID=w&site=' . \common\models\Repair::SITE_TYPE_2 : 'javascript:void(0)'; ?>"
               class="addNew maintain <?= $hasHouse ? '' : 'alterWin' ?>">报修</a>
        <?php } else {?>
            <a href="<?= $hasHouse ? '/new-repair?flowStyleID=8&site=' . \common\models\Repair::SITE_TYPE_2 : 'javascript:void(0)'; ?>"
               class="addNew complain <?= $hasHouse ? '' : 'alterWin' ?>">投诉</a>
        <?php }?>

    </div>

    <div class="ui-row-flex tab-list">
        <div>
            <a href="/new-repair/list?status=0&flowStyleID=<?= $flowStyleID ?>" <?= $status == '0' ? 'class="hover"' : '' ?>>待处理</a>
        </div>
        <div>
            <a href="/new-repair/list?status=1,2,5,6,8,9&flowStyleID=<?= $flowStyleID ?>" <?= $status == '1,2,5,6,8,9' ? 'class="hover"' : '' ?>>处理中</a>
        </div>
        <div>
            <a href="/new-repair/list?status=3,7&flowStyleID=<?= $flowStyleID ?>" <?= $status == '3,7' ? 'class="hover"' : '' ?>>待评价</a>
        </div>
        <div>
            <a href="/new-repair/list?status=3000&flowStyleID=<?= $flowStyleID ?>" <?= $status == '3000' ? 'class="hover"' : '' ?>>已完成</a>
        </div>
        <div>
            <a href="/new-repair/list?status=4,1000&flowStyleID=<?= $flowStyleID ?>" <?= $status == '4,1000' ? 'class="hover"' : '' ?>>已取消</a>
        </div>
    </div>
    <?php
    if ($dataProvider->count == 0)
        echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
    else
        echo $this->render('list-cell', ['dataProvider' => $dataProvider]);
    ?>

    <div id="typeSelect">
        <div class="mask"></div>
        <div class="c-panel">
            <div class="t">
                <p>该功能仅对 <b><?= $projectName ?></b> 认证业主用户开放</p>
            </div>
            <div class="button-line">
                <button href="" class="btn-off btn-block btn-hidden">取消</button>
                <button class="placeholder"></button>
                <button data-go="/auth" class="btn-active btn-block">立即认证</button>
            </div>
        </div>
    </div>

    <div style="height: 6em;line-height: 6em;">

    </div>

</div>

<div id="bottom-nav">
    <a href="/">
        <i class="home"></i>
        首页
    </a>
    <a href="/article/list/">
        <i class="area-even"></i>
        社区动态
    </a>
    <a href="/new-repair/list?status=0">
        <i class="repair-blue"></i>
        报事报修
    </a>
    <a href="/house/">
        <i class="owners"></i>
        我的
    </a>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $('#new-repair-list').on('loaded', function () {
        app.scrollLoad($('#new-repair-list'), '/new-repair/list?status=<?= $status ?>&flowStyleID=<?= $flowStyleID ?>');
        $('.alterWin').bind('click', function () {
            $('#typeSelect').show();
        });

        $('.btn-hidden').bind('click', function () {
            $('#typeSelect').hide();
        });
    });
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>

