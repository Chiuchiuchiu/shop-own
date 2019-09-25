<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/24 18:12
 * Description:
 * @var $model \common\models\Article
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="date-hr"><?= date('Y年m月', $model->post_at) ?></div>
    <div data-go="/article?id=<?= $model->id ?>&title=<?= $model->title ?>" class="article-cell type-<?= $model->show_type ?>">
        <div class="info">
            <h4><?= $model->title ?></h4>
            <p><?= $model->summary ?></p>
        </div>
        <?php if ($model->show_type !== 3): ?>
            <div class="pic">
                <img src="<?= Yii::getAlias($model->pic) ?>?x-oss-process=image/resize,w_<?=$model->show_type==1?'120':'640'?>" alt="<?= $model->title ?>">
            </div>
        <?php endif; ?>
        <div class="more">
            <span class="time"><?= date('Y-m-d', $model->post_at) ?></span>
            <span>查看详情</span>
        </div>
    </div>
    <?php
}
?>