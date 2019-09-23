<?php
/**
 * @var $memberHouse array
 * @var $model \common\models\MemberHouse
 * @var $_user \apps\www\models\Member
 */
?>
<div class="panel" id="house-index">
    <div class="house-info">
        <img src="<?= Yii::getAlias($_user->headimg) ?>">
        <h4><?php echo $_user->name ? $_user->name : $_user->nickname ?></h4>
        <p>您名下拥有<?= count($memberHouse) ?>套房产</p>
    </div>
    <?php if (sizeof($memberHouse) == 0) { ?>
        <div class="empty-status"><i></i>请添加您的房产信息</div>
    <?php } else { ?>
        <label class="hr">请选择您想查询房屋的信息</label>
        <ul>
            <?php foreach ($memberHouse as $model): ?>
                <li>
                    <?php if ($model->status == \common\models\MemberHouse::STATUS_WAIT_REVIEW) { ?>
                        <a href="">
                            <?= $model->house->showName ?>
                            <small>(审核中)</small>
                        </a>
                    <?php } else { ?>
                        <a data-origin="1" href="/house/bill?id=<?= $model->house_id ?>">
                            <?= $model->house->showName ?>
                        </a>
                    <?php } ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php } //endif sizeof($memberHouse)==0?>
</div>
