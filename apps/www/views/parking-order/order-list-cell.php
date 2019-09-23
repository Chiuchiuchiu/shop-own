<?php
/**
 * @var $model \common\models\ParkingOrder
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="pm-order-cell">
        <h4><span>付款金额</span> ¥<?=number_format($model->amount,2)?></h4>
        <ul>
            <li style="text-align: left;"><span>类型：</span><?= $model->typeText ?></li>

            <?php if($model->type == \common\models\ParkingOrder::TYPE_M){?>
                <li><span>数量：</span><?= $model->quantity ?></li>
                <li><span>缴费开始日期</span><?= date('Y-m-d', $model->effect_date) ?></li>
                <li><span>缴费结束日期</span><?= date('Y-m-d', $model->expire_date) ?></li>
            <?php } else {?>
                <li><span>入场时间</span><?= $model->effectDate ?></li>
            <?php }?>

            <li><span>交易时间</span><?=date('Y-m-d H:i:s',$model->payed_at)?></li>
            <li><span>状<em></em><em></em>态</span><?= $model->statusText ?></li>
            <li><span>订单编号</span><?=$model->number?></li>
        </ul>
        <div class="flex a1">
            <div>
                <a href="/parking-order/order-view?id=<?=$model->id?>" class="detail">详情</a>
            </div>
        </div>

    </div>
<?php
}
