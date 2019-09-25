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
    <div class="repair-lists">
        <div class="list-cell">
            <div class="new-info" data-go="/new-repair/view?id=<?= $model->id ?>">
                <ul>
                    <li>
                        <h4>
                            <label class="flow_style_<?= $model->flow_style_id == 'w' ? $model->flow_style_id . $model->site : $model->flow_style_id ?>">
                                <?= $model->flow_style_id == 'w' ? $model->siteText : $model->flowStyleText ?>
                            </label>
                            <?= mb_substr($model->content, 0, 5) . '...'?>
                            <span id="st-Left"><?= $model->statusText ?></span>
                        </h4>
                    </li>

                    <?php if(in_array($model->status, [2, 7, 3000])){?>
                        <li>
                            <span>事务类型：</span>
                            <?= $model->flowStyleText ?>
                        </li>
                    <?php } ?>

                    <li>
                        <span>事务编号：</span>
                        <?= isset($model->repairResponse->services_id) ? mb_substr($model->repairResponse->services_id, 0, 12) . '...': $model->id ?>
                    </li>
                    <li class="time-bottom">
                        <div class="time-bottom-left">
                            <span>提交时间：</span>
                            <?php $doTime = isset($model->repairResponse->created_at) ? $model->repairResponse->created_at : null ?>
                            <?= \common\valueObject\TimeValue::dateDiff($model->created_at) ?>
                        </div>

                        <div class="time-bottom-right">
                            <?php if(in_array($model->status, [2, 3, 7, 3000]) && isset($model->repairResponse->created_at)){?>
                                <span>受理时间：</span><?= \common\valueObject\TimeValue::dateDiff($model->repairResponse->created_at)?>
                            <?php }?>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <div class="repair-bottom">
            <ul class="bottom-ul">
                <li class="bottom-ul-li">
                    <ul>
                        <?php if($model->status == \common\models\Repair::STATUS_CANCEL){?>
                            <li class="hidden-chart">
                                <label>取消原因：</label>
                                <?= $model->repairCancel->type < 3 ? $model->repairCancel->typeText : $model->repairCancel->content ?>
                            </li>
                        <?php } else {?>
                            <li class="bottom-ul-li0">
                                <?php if($model->status == \common\models\Repair::STATUS_COMPLETE){?>
                                    <label>受理人：</label>
                                    <?= isset($model->reception_user_name) ? $model->reception_user_name : '暂无' ?>
                                <?php } elseif ($model->status == \common\models\Repair::STATUS_EVALUATED) {?>
                                    <label>满意度：</label>
                                    <?= $model->repairCustomerEvaluation->satisfactionText ?>
                                <?php } ?>
                            </li>
                            <li class="bottom-ul-li1">

                                <?php if($model->status == \common\models\Repair::STATUS_COMPLETE){?>
                                    <label>处理人：</label>
                                    <?= isset($model->order_user_name) ? $model->order_user_name : '暂无' ?>
                                <?php } elseif ($model->status == \common\models\Repair::STATUS_EVALUATED){?>
                                    <label>及时性：</label>
                                    <?= isset($model->repairCustomerEvaluation->timelinessText) ? $model->repairCustomerEvaluation->timelinessText : '暂无' ?>
                                <?php }?>

                                <?php if($model->status == \common\models\Repair::STATUS_WAIT){?>
                                <button class="bottom-right" data-go="/new-repair/cancel?id=<?= $model->id ?>">取消</button>
                                <?php } ?>

                                <?php if( in_array($model->status, [\common\models\Repair::STATUS_DONE, 7])){?>
                                    <button class="bottom-right" data-go="/new-repair/customer-evaluation?id=<?= $model->id ?>">评价</button>
                                <?php } ?>

                            </li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>

        </div>

    </div>

    <?php
}
?>