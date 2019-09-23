<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/24 18:12
 * Description:
 * @var $model \common\models\Repair
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div data-go="/feedback/view?id=<?= $model->id ?>" class="list-cell">
        <div class="info">
            <h4><?= $model->name ?> 的 <?=$model->typeText?>
                <span><?=$model->statusText?></span>
            </h4>
            <p><?= $model->content ?></p>
        </div>
        <?php
            $pics = explode(',',$model->pics);
        ?>
        <ul class="pic pure-g">
            <?php
            foreach($pics as $pic):
                if($pic):
            ?>
                <li class="pure-u-7-24"><img src="<?= Yii::getAlias($pic) ?>" alt=""></li>
            <?php
                endif;
                endforeach;
            ?>
        </ul>
        <div class="more">
            <span class="time"><?= date('Y-m-d', $model->created_at) ?></span>
            <span>查看详情</span>
        </div>
    </div>
    <?php
}
?>