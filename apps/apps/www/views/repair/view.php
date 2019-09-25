<?php
/**
 * @var $model \common\models\Repair
 */
/* @var $res array|string */

?>
<div class="panel" id="new-repair-view">
    <div id="title-back" class="status <?= $model->status == 2 ? "complete" : "" ?>">
        <&nbsp;<?php echo $model->statusText ?>
    </div>
    <div class="list-cell">

        <div class="new-info">
            <h4>
                <b class="flow_style_<?= $model->flow_style_id == 'w' ? $model->flow_style_id . $model->site : $model->flow_style_id ?>">
                    <?= $model->flow_style_id == 'w' ? $model->siteText : $model->flowStyleText ?>
                </b>

                <?php if($model->status == \common\models\Repair::STATUS_WAIT){?>
                    <button class="re-view-cancel" data-go="/repair/cancel?id=<?= $model->id ?>">取消</button>
                <?php }?>

                <?php if($model->status == \common\models\Repair::STATUS_DONE){?>
                    <button data-go="/repair/customer-evaluation?id=<?= $model->id ?>" class="re-view-done">评价服务</button>
                <?php }?>

            </h4>
            <p><?= $model->content ?></p>
        </div>

        <?php
        $pics = explode(',', $model->pics);
        ?>
        <ul class="pic tac">
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

        <div class="more new-more">
            <ul>
                <li>
                    <span><?= $model->flowStyleText ?>人:</span>
                    <?= $model->name ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= $model->tel ?>
                </li>

                <?php if($model->flow_style_id == \common\models\Repair::FLOW_STYLE_TYPE_W) {?>
                    <li>
                        <span>地址:</span>
                        <?= isset($model->address) ? $model->address : $model->house->ancestor_name ?>
                    </li>
                <?php }?>

                <li>
                    <span>编号:</span>
                    <?= isset($model->repairResponse->services_id) ? $model->repairResponse->services_id : $model->id ?>
                </li>
                <li>
                    <span>时间:</span>
                    <?= date('Y-m-d H:i:s', $model->created_at) ?>
                </li>
            </ul>
        </div>

    </div>

    <?php if(!in_array($model->status, [\common\models\Repair::STATUS_WAIT, \common\models\Repair::STATUS_UNDERWAY, \common\models\Repair::STATUS_CANCEL])){ ?>
        <p class="new-live-line">
            <label for="" class="hr">处理现场</label>
        </p>
        <div class="more new-live">

            <ul class="pic tac">


                <li class="text-pi">现场情况：</li>
                <li class="word-text">
                    <?=
                        isset($res[0]['SpotCircs']) ? $res[0]['SpotCircs'] : '暂无数据';
                    ?>
                </li>
            </ul>

        </div>
    <?php } ?>

    <?php if($model->status == \common\models\Repair::STATUS_EVALUATED){ ?>
        <p class="new-live-line">
            <label for="" class="hr">您的评价：</label>
        </p>
        <div class="evaluation">
            <ul id="new-evaluation">
                <li>
                    <div class="d-left">满意度：</div>
                    <div class="d-right">
                        <ul id="satisfaction-list">
                            <?php if(isset($model->repairCustomerEvaluation->satisfaction)){
                                for ($i=0; $i<$model->repairCustomerEvaluation->satisfaction; $i++):
                                    ?>
                                        <li><span></span></li>
                                        <?php
                                endfor;
                                ?>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="d-left">及时性：</div>
                    <div class="d-right">
                        <ul id="new-timely-list">
                            <li><span class="<?= $model->repairCustomerEvaluation->timeliness == 1 ? 'new-timely-hover' : 'new-timely-icon' ?>"></span><span class="t">及时</span></li>
                            <li><span class="<?= $model->repairCustomerEvaluation->timeliness == 2 ? 'new-timely-hover' : 'new-timely-icon' ?>"></span><span class="t">一般</span></li>
                            <li><span class="<?= $model->repairCustomerEvaluation->timeliness == 3 ? 'new-timely-hover' : 'new-timely-icon' ?>"></span><span class="t">不及时</span></li>
                        </ul>
                    </div>
                </li>
                <li class="new-idea">
                    <div class="d-left">客户意见：</div>
                    <div class="d-right word-text">

                        <?= $model->repairCustomerEvaluation->customer_idea ?>

                    </div>
                </li>
            </ul>
        </div>
    <?php } ?>

    <p class="new-live-line">
        <label class="hr">事务处理流程</label>
    </p>
    <div class="flow">
        <ul>
            <li>
                <h4>您的事务已<?= $model->status == \common\models\Repair::STATUS_CANCEL ? '取消' : '提交' ?>成功</h4>
                <p><?= date('Y-m-d H:i:s', $model->created_at) ?></p>
            </li>

            <?php if(!in_array($model->status, [\common\models\Repair::STATUS_WAIT, \common\models\Repair::STATUS_UNDERWAY, \common\models\Repair::STATUS_CANCEL])){?>
                <li>
                    <h4>管家<span>&nbsp;</span>受理成功</h4>
                    <p><?= date('Y-m-d H:i:s', $model->created_at) ?></p>
                </li>

                <?php if($model->flow_style_id == \common\models\Repair::FLOW_STYLE_TYPE_W){?>
                    <li>
                        <h4>工程部<span>&nbsp;&nbsp;</span>成为处理人</h4>
                        <p><?= date('Y-m-d H:i:s', $model->created_at) ?></p>
                    </li>
                <?php } ?>

                <?php if(in_array($model->status, [3, 3000]) && $model->flow_style_id == \common\models\Repair::FLOW_STYLE_TYPE_W){?>
                    <li>
                        <h4>工程部<span>&nbsp;&nbsp;</span>处理完成</h4>
                        <p><?= date('Y-m-d H:i:s', $model->updated_at) ?></p>
                    </li>
                <?php }?>

            <?php }?>

            <?php if($model->status == \common\models\Repair::STATUS_EVALUATED){?>
                <li>
                    <h4>完成评价本次服务</h4>
                    <p><?= date('Y-m-d H:i:s', $model->updated_at) ?></p>
                </li>
            <?php } ?>

        </ul>
    </div>

    <div style="height: 6em; line-height: 6em">

    </div>

</div>

<div id="bottom-nav">
    <a href="/">
        <i class="home"></i>
        首页
    </a>
    <a href="/article/list/">
        <i class="area-even"></i>
        社区动态
    </a>
    <a href="javascript:void(0)">
        <i class="repair-blue"></i>
        报事报修
    </a>
    <a href="/house/">
        <i class="owners"></i>
        我的
    </a>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $('#title-back').on('click', function (){
        history.go(-1);
    });
</script>

<?php \common\widgets\JavascriptBlock::end();?>
