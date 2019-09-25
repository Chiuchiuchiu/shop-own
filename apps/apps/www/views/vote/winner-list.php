<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/12
 * Time: 16:31
 */
/* @var $model \common\models\ButlerElectionActivity */

$this->title = '品牌管家/最美卫士荣誉榜';

?>

<div id="winner-vote-box">
    <ul>

        <?php foreach ($model as $key => $row){?>
            <?php /* @var $row \common\models\ButlerElectionActivity */?>

            <li>
                <div class="li-div-outline">
                    <div class="li-image">
                        <img src="<?= Yii::getAlias($row->head_img)?>">
                    </div>

                    <p><?= $row->name ?></p>
                    <p><?= $row->group == 1 ? '管家' : '保安' ?></p>
                    <p><?= $row->project->house_name ?></p>
                </div>
            </li>

        <?php } ?>

    </ul>
</div>

<div style="height: 6em;line-height: 6em;">

</div>


<?php \common\widgets\JavascriptBlock::begin(); ?>
<script type="text/javascript">

    function route() {
        for (var i = 0; i < $('#winner-vote-box ul li').length; i++) {
            var random = 10 - parseInt(Math.random() * 20);
            $("li").eq(i).css("transform", "rotate(" + random + "deg)");
        }
    }
    route();

</script>
<?php \common\widgets\JavascriptBlock::end(); ?>
