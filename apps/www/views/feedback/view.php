<?php
/**
 * @var $model \common\models\Feedback
 */
?>
<div class="panel" id="feedback-view">
    <div class="status <?= $model->status == 2 ? "complete" : "" ?>"><?= $model->statusText ?></div>
    <div class="list-cell">
        <div class="info">
            <h4><?= $model->name ?> 的 <?= $model->typeText ?>
            </h4>
            <p><?= $model->content ?></p>
        </div>
        <?php
        $pics = explode(',', $model->pics);
        ?>
        <ul class="pic pure-g tac">
            <?php
            foreach ($pics as $pic):
                if ($pic):
                    ?>
                    <li class="pure-u-1-1"><img src="<?= Yii::getAlias($pic) ?>" alt=""></li>
                    <?php
                endif;
            endforeach;
            ?>
        </ul>
        <div class="more">
            <span class="time"><?= date('Y-m-d', $model->created_at) ?></span>
        </div>
    </div>
    <label class="hr" style="display: none">留言板</label>
    <div class="message-list" style="display: none">
        <p class="empty">暂无留言</p>
    </div>
    <?php if($model->status!=$model::STATUS_COMPLETE) : ?>

    <div class="bottom-message-form pure-form" style="display: none">
        <?php
        \components\za\ActiveForm::begin();
        ?>
        <div class="form-group">
            <div class="">
                <?= \components\za\Html::textarea("content", null, ['placeholder' => '留言内容', 'data-required' => true]) ?>
            </div>
            <div class="">
                <button type="button" class="btn btn-green">发送</button>
            </div>
        </div>
        <?php
        \components\za\ActiveForm::end();
        ?>
    </div>

    <?php endif; ?>
</div>