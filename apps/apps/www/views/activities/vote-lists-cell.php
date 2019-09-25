<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/26
 * Time: 9:07
 */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model \common\models\ButlerElectionActivity */
/* @var int $group */
/* @var integer|null $voteId */

?>

    <?php foreach ($dataProvider->getModels() as $model) { ?>
        <?php
        $AuthCodeModel = new \components\cryptographic\AuthCode();
        $authCode = $AuthCodeModel->authCode($model->id, 'ENCODE');
        ?>

        <li class="li_<?= $model->id ?>" data-id="<?= $authCode ?>">
            <div class="<?= $group == 1 ? 'toupiao-touxiang' : 'se-toupiao-touxiang'; ?>">
                <span class="touxiang-hot">
                    <img src="../static/images/vote/hot@2x.png"><br>
                    <i class="li_number_<?= $model->id ?>"><?= $model->number ?></i>
                </span>

                <div class="head-img-div">
                    <img class="li_<?= $model->id ?>_img" src="<?= Yii::getAlias($model->head_img) ?>">
                </div>

                <p class="touxiang-title li_<?= $model->id ?>_title"><?= $model->name ?>
                    (<?= $model->project->house_name ?>)</p>
                <p class="touxiang-ms li_<?= $model->id ?>_span">
                    <?php
                    $labels = [];
                    switch ($group){
                        case 1:
                            $labels = \common\models\ButlerLabels::findAll(['butler_id' => $model->butler_id]);
                            break;
                        default:
                            $labels = \common\models\SecurityPerLabels::findAll(['bea_id' => $model->id]);
                            break;
                    }

                    ?>

                </p>
                <p class="li_<?= $model->id ?>_introduce"
                   style="display: none">
                    <?= $model->introduce ?>
                </p>
            </div>
            <div class="toupiao-toupiao">
                <a href="javascript:" <?= $voteId == $model->id ? 'class="toupiao-voted"' : ''; ?>>
                    <?= $voteId == $model->id ? '已投' : '为ta投票'; ?>
                </a>
            </div>
        </li>

    <?php } ?>

