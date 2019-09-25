<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/24 18:12
 * Description:
 * @var $model \common\models\PmOrder
 * @var $item \common\models\PmOrderItem
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="pm-order-cell">
        <h4><span>付款金额</span> ¥<?=number_format($model->total_amount,2)?></h4>
        <ul>
            <li style="text-decoration: line-through">
                <?php if(isset($model->pmOrderDiscounts)){?>
                    <span>金额：</span>￥<?= bcadd($model->total_amount, $model->pmOrderDiscounts->discounts_amount, 2); ?>
                <?php } else {?>
                    <span>金额：</span>￥<?=number_format($model->total_amount,2)?>
                <?php }?>

            </li>

            <?php if(isset($model->pmOrderDiscounts)){?>

                <li>
                    <span>优惠：</span>
                    <?= number_format($model->pmOrderDiscounts->discounts_amount, 2) ?>
                </li>

            <?php } ?>

            <li style="text-align: left;"><span>房产：</span><?= $model->house->showName ?></li>
            <li style="text-align: left;"><span>房产全称：</span><?= $model->house->ancestor_name ?></li>
            <li><span>交易时间</span><?=date('Y-m-d H:i:s',$model->payed_at)?></li>
            <li><span>状<em></em><em></em>态</span><?= $model->statusText ?></li>
            <li><span>订单编号</span><?=$model->number?></li>
        </ul>
        <h5>缴费项目</h5>
        <ul>
            <?php foreach ($model->items as $item){ ?>
                <li class="item"><span><?=$item->bill_date?> <?=$item->charge_item_name?></span>¥<?=number_format($item->amount,2)?></li>
            <?php }?>
        </ul>

        <div class="flex a1">
            <div>
                <a href="/order/pm-view?id=<?=$model->id?>" class="detail">详情</a>
            </div>

            <?php if(in_array($model->project_house_id, Yii::$app->params['allow_project_fpzz']['project_house_id']) && !(\common\models\SysSwitch::getValue('Tcis')) && Yii::$app->params['allow_project_fpzz']['allow_use']){?>

                <div>
                    <?php if(isset($model->pmOrderFpzz->status)){?>
                        <?php
                            echo '<a href="/tcis/detail?id='. $model->pmOrderFpzz->id .'" class="opened">'. $model->pmOrderFpzz->statusText .'</a>';
                        ?>

                    <?php } elseif ($model->discount_status < 1){?>
                    <a href="/tcis/recipt?id=<?=$model->id?>" class="receiptd">开发票</a>
                    <?php }?>

                </div>
            <?php } else if(in_array($model->project_house_id, Yii::$app->params['allow_project_fpzz']['project_house_id']) && \common\models\SysSwitch::getValue('Tcis') && $model->total_amount >= Yii::$app->params['allow_fp_amount']){?>

                <div>
                    <?php if(isset($model->pmOrderFpzz->status)){?>
                        <?php
                        echo '<a href="/tcis/detail?id='. $model->pmOrderFpzz->id .'" class="opened">'. $model->pmOrderFpzz->statusText .'</a>';
                        ?>

                    <?php } elseif ($model->discount_status < 1){?>
                        <a href="/tcis/recipt?id=<?=$model->id?>" class="receiptd">开发票</a>
                    <?php }?>
                </div>

            <?php }?>

        </div>

    </div>
    <?php
}
