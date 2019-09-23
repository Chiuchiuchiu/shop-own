<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/24 18:12
 * Description:
 * @var $model \common\models\PropertyAnnouncement
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="date-hr"><?= date('Y年m月', $model->created_at) ?></div>
    <div data-go="/property-announcement/detail?id=<?= $model->id ?>" class="article-cell type-1">
        <div class="info">
            <h4><?= $model->title ?></h4>
            <p><?= $model->summary ?></p>
        </div>

            <div class="pic">
                <img src="<?= Yii::getAlias($model->pic) ?>?x-oss-process=image/resize,w_<?= '120' ?>" alt="<?= $model->title ?>">
            </div>

        <div class="more">
            <span class="time"><?= date('Y-m-d', $model->created_at) ?></span>
            <span>查看详情</span>
        </div>
    </div>
    <?php
}
?>