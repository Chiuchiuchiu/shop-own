<?php
/**
 * Created by
 * Author: feng
 * Time: 2019/8/8 11:12
 * Description:
 * @var $model \common\models\Repair
 * @var $dataProvider \yii\data\ActiveDataProvider
 */
foreach ($dataProvider->getModels() as $model) { ?>
    <div class="repair-lists"  style="margin-top: 2px;margin-bottom: 0px;">
        <div class="list-cell">
            <div class="new-info" data-go="/search-notices/view?id=<?= $model->id ?>">
                <ul>
                    <li>
                        <h4 style="height:1em;color: #bf552e;font-size: 16px;font-weight: normal;">
                            <?php if($model->status == \common\models\SearchNotices::STATUS_RECEIVE) {?>
                                <label class="flow_style_w2" style="color: #ffffff;border: 1px solid #ff0202;background: #ff2222;padding: 0.2em 0.3em;font-size: 12px;">
                                    已领
                                </label>
                            <?php }?>
                            <?php if(strlen($model->title) > 20) { ?>
                                <?= mb_substr($model->title, 0, 10) . '...'?>
                            <?php }else { ?>
                                <?= $model->title?>
                            <?php } ?>
                            <span id="st-Left"><img src="/static/images/right-jt.png"></span>
                        </h4>
                    </li>
 
                    <li style="color: #CCCCCC">
                        <?php $doTime = isset($model->created_at) ? $model->created_at : null ?>
                        <?= \common\valueObject\TimeValue::dateDiff($model->created_at) ?>
                    </li>
                    <li class="time-bottom">
                        <div class="time-bottom-left" style="width:auto">
                            <span style="color: #000000;font-size: 15px;">  <?= $model->describtions ?></span>
                        </div>
                    </li>



                    <?php
                    $pics = explode(',', $model->pics);
                   if(sizeof($pics) == 1){
                        $_w = 60;
                    }else if(sizeof($pics) == 2){
                        $_w = 45;
                    }else{
                        $_w = 30;
                    }
                    ?>
                    <li >
                        <?php
                        foreach ($pics as $pic):
                            if ($pic):
                                ?>
                                <img style="width: <?= $_w?>%;" src="<?= Yii::getAlias($pic) ?>">
                            <?php
                            endif;
                        endforeach;
                        ?>
                    </li>



                </ul>
            </div>
        </div>

        <?php if ($type == "1" && $model->status == \common\models\SearchNotices::STATUS_WAIT) { ?>
            <div class="repair-bottom">
                <ul class="bottom-ul">
                    <li class="bottom-ul-li">
                        <ul>
                            <li class="bottom-ul-li1" style="text-align: center;">
                                <button class="bottom-right" style="padding: 0.3em 1em;border-radius: 5px;"
                                        data-go="/search-notices/index?id=<?= $model->id ?>">编辑
                                </button>
                            </li>
                            <li class="bottom-ul-li1 receiveBtn" style="text-align: center; "   data-id="<?= $model->id ?>">
                                <button class="bottom-right" style="background-color: #2196f3!important;padding: 0.3em 1em; border-radius: 5px;" >领取
                                </button>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php } ?>
    </div>

    <?php
}
?>