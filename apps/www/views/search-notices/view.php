<?php
/**
 * @var $model \common\models\SearchNotices
 */
/* @var $res array|string */

?>
<div class="panel" id="new-repair-view">
    <div class="status <?= $model->status == 1 ? "complete" : "" ?>" style="height: 40px;">
     <p  id="title-back" style="width: 5%;float: left; " >
        <img src="/static/images/leftback-icon.png">
     </p>
       <p style="width: 100%;text-align:center;font-size: 16px; ">  <span  style="">寻物启事</span></p>

    </div>

    <div class="list-cell">

        <div class="new-info">
            <img style="width:50px;float: left; " src="<?= $model->member_headimg ?>">
            <b class=" " style="margin-left: 20px;">
               <span><?= $model->title ?></span>
            </b>
            <?php if($isFav == 1) { ?>
                <img id="unCollectionBtn" data-id="<?= $model->id ?>" style="float: right;display: block;" src="/static/images/sc-icon.png">
                <img id="CollectionBtn"  data-id="<?= $model->id ?>" style="float: right; display: none;" src="/static/images/unsc-icon.png">
            <?php }else{ ?>
                <img id="unCollectionBtn" data-id="<?= $model->id ?>" style="float: right;display: none;" src="/static/images/sc-icon.png">
                <img id="CollectionBtn"  data-id="<?= $model->id ?>" style="float: right; display: block;" src="/static/images/unsc-icon.png">
            <?php } ?>
            <p><span style="margin-left: 20px;margin-top: 13px;">发起人：</span><?= $model->tel ?></p>
        </div>


        <div class="more new-more">
            <ul>
                <li>
                    <span style="color: #e68e0b;font-size: 16px;">联系电话:</span>
                    <span style="color: #000000;"><?= $model->tel ?></span>
                </li>
                <li>
                    <span style="color: #e68e0b;font-size: 16px;">联系人:</span>
                    <span style="color: #000000;"><?= $model->linkman ?></span>
                </li>

                <?php if($model->status == \common\models\SearchNotices::STATUS_RECEIVE){ ?>
                    <li>
                        <span style="color: #e68e0b;font-size: 16px;"">领取地点:</span>
                        <span style="color: #e68e0b;font-size: 16px;""><?= $model->receive_address ?></span>
                    </li>
                    <li>
                        <span style="color: #e68e0b;font-size: 16px;"">领取时间:</span>
                        <span style="color: #999999;font-size: 16px;""><?= date('Y-m-d H:i:s',$model->updated_at)?></span>
                    </li>
                <?php } ?>
                <li>
                    <span style="color: #e68e0b;font-size: 16px;"">丢失地点:</span>
                    <span style="color: #999999;font-size: 16px;""><?= $model->lose_address ?></span>
                </li>
                <li>
                    <span style="color: #e68e0b;font-size: 16px;"">丢失时间:</span>
                    <span style="color: #999999;font-size: 16px;""><?= $model->lose_time ?></span>
                </li>

                <li>
                    <span style="color: #e68e0b;font-size: 16px;"">状态:</span>
                    <span style="color: #999999;font-size: 16px;""><?php if ($model->status == \common\models\SearchNotices::STATUS_WAIT) {
                        echo '待领取';
                    } else {
                        echo '已领';
                    } ?></span>
                </li>
                <?php  if($model->status == \common\models\SearchNotices::STATUS_RECEIVE) { ?>
                    <li>
                        <span style="color: #e68e0b;font-size: 16px;"">备注:</span>
                        <span style="color: #999999;font-size: 16px;""><?= $model->receive_remark ?></span>
                    </li>
                <?php  } ?>

                <?php if($model->status == \common\models\SearchNotices::STATUS_WAIT){ ?>
                <li>
                    <span style="color: #ff2222;font-size: 16px;"">注:请带上有效的身份证明</span>
                </li>
                <?php } ?>
            </ul>
        </div>

    </div>


    <ul class="list-cell">
        <span style="color: #6f6f6f;font-size: 16px;"">&nbsp;&nbsp;&nbsp;&nbsp;<?= $model->describtions ?></span>
    </ul>
    <?php if($model->pics != null) { ?>
        <div class="list-cell">
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
        </div>
    <?php } ?>

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
    <a href="/search-notices/list">

        <i  > <img style="margin-top: 7px;" src="/static/images/notices-icon.png"></i>
        寻物启事
    </a>
    <a href="/house/">
        <i class="owners"></i>
        我的
    </a>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $('#title-back').on('click', function () {
        history.go(-1);
    });

    $('#unCollectionBtn').on('click',function(){
        var _id = $(this).attr("data-id");
        favFun(_id,0);
    });
    $('#CollectionBtn').on('click',function(){
        var _id = $(this).attr("data-id");
        favFun(_id,1);
    });

    function favFun(id,status){
        $.ajax({
            type: 'GET',
            dataType: "json",
            url: '/search-notices/add-favorites',
            data: "id=" + id +"&status=" + status,
            timeout: 3000, //超时时间：30秒
            success: function (res) {
                var _res = res.data;
                 if(_res.code == 1){
                     if(status== 1){
                         $('#unCollectionBtn').show();
                         $('#CollectionBtn').hide();
                     }else{
                         $('#CollectionBtn').show();
                         $('#unCollectionBtn').hide();
                     }
                 }else{
                     if(status == 1){
                         alert("收藏失败");
                     }else{
                         alert("取消失败");
                     }
                 }
            }
        });
    }

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
