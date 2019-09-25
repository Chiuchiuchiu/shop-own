<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/24 18:12
 * Description:
 * @var $model \common\models\PrepayPmOrder
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="pm-order-cell">
        <h4><span>付款金额</span> ¥<?=number_format($model->total_amount,2)?></h4>
        <ul>
            <li><span>交易时间</span><?=date('Y-m-d H:i:s',$model->payed_at)?></li>
            <li><span>状<em></em><em></em>态</span>已支付</li>
            <li><span>预付月数</span><?=$model->num?></li>
            <li><span>订单编号</span><?=$model->number?></li>
        </ul>
        <a href="/order/prepay-pm-view?id=<?=$model->id?>" class="detail">详情</a>
    </div>
    <?php
}
