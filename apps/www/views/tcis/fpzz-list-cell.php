<?php
/**
 * Created by
 * Author: HQM
 * Description:
 * @var $model \common\models\PmOrderFpzz
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="order-fpzz-cell" data-go="/tcis/detail?id=<?= $model->id ?>">
        <div>
            <p class="fpzz-top">
                <i class="time"></i>
                <span class="fpzz-time">
                    <?=date('Y-m-d H:i', $model->created_at)?>
                </span>
                <span class="<?= $model->getStatusStyle() ?>"><?= $model->getStatusListsText() ?> <i></i></span>
            </p>
        </div>

        <div>
            <p class="fpzz-bottom">
                <span class="fpzz-cash">发票金额</span>
                <span class="fpzz-total"><?= isset($model->total_amount) ? $model->total_amount : '0.00' ?></span>元
                &nbsp;&nbsp;|&nbsp;&nbsp;<span class="fpzz-pm"><?= $model->typeText ?></span>
            </p>
        </div>

    </div>
    <?php
}
