<?php
/**
 * @var $memberHouse array
 * @var $model \common\models\MemberHouse
 * @var $item \common\models\MemberHouse
 * @var $_user \apps\www\models\Member
 * @var $list array
 */
?>
<div class="panel" id="house-member">
    <div class="banner"></div>
    <div class="house-list">
        <ul>
            <?php foreach ($list as $model) { ?>
                <li data-house="<?=$model->house_id?>">
                    <div>
                        <i style="background-image:url(<?= Yii::getAlias($model->house->project->icon) ?>);background-size:contain"></i>
                        <p>
                            <?= $model->house->showName ?>
                        <p class="newwindow-house-n">(<?= $model->house->ancestor_name ?>)</p>
                        </p>
                        <span class="member-manger">成员管理</span>
                    </div>
                    <ul data-id="<?= $model->house_id ?>">
                        <?php foreach ($model->liveAt as $item) { ?>
                            <li data-id="<?= $item->member_id ?>" <?=$model->identity==1?'data-del-house="'.$model->house_id.'"':''?>>
                                <i></i>
                                <p>
                                    <?= $item->member->showName ?><span
                                            class="color<?= $item->identity ?>"><?= $item->identityText ?></span>
                                    <span class="tel"><?= $item->member->phone ?></span>
                                </p>
                            </li>
                        <?php } ?>
                        <li class="flex">
                            <button class="add-member" data-id="<?= $model->house_id ?>">添加成员</button>
                            <button class="del-member" data-id="<?= $model->house_id ?>">删除成员</button>
                        </li>
                    </ul>
                </li>
            <?php } ?>
            <?php if (sizeof($list) == 0) { ?>
                <div class="empty-status"><i></i>暂无资料</div>
            <?php } ?>
        </ul>

        <div style="height: 6em;line-height: 6em;">

        </div>

    </div>
    <div id="qrMask"></div>
    <div id="qrDiv">
        <div class="close">&times;</div>
        <h3>通过二维码添加</h3>
        <div id="qrImg">
            <div>
                <img id="qrcode" src="">
                <p>房产认证二维码</p>
            </div>
            <div class="a2">
                <img src="../static/images/changan.png">
                <p>长按图片发送给朋友</p>
            </div>
        </div>
    </div>
</div>
<?php \common\widgets\JavascriptBlock::begin() ?>
<script>
    $('#house-member').on('loaded', function () {
        $('.house-list .member-manger', this).on('click', function () {
            var o = $(this).parentsUntil('ul');
            o = $(o[o.length - 1]);
            if (o.hasClass('open')) {
                $(this).html('成员管理');
                o.removeClass('open');
                $('ul', o).slideUp();
            } else {
                $(this).html('取消管理');
                o.addClass('open');
                $('ul', o).slideDown();
            }
        });
        $('.house-list li ul li', this).on('click', function () {
            if ($(this).hasClass('checked')) {
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }
        });
        $('.del-member[data-id]').on('click', function () {
            var id = $(this).attr('data-id');
            var memberId = [];
            $('ul[data-id=' + id + '] li.checked').each(function (i, e) {
                if ($(e).attr('data-id')) {
                    memberId.push($(e).attr('data-id'));
                }
            });
            $.post('/house/member-remove',
                {
                    memberIds: memberId.join(','),
                    houseId: id,
                    _csrf: '<?=Yii::$app->request->csrfToken?>'
                },
                function (res) {
                    if (res.code === 0) {
                        $('ul[data-id=' + id + '] li.checked').each(function (i, e) {
                            if ($(e).attr('data-id')) {
                                if($(e).attr('data-del-house')){
                                    $('li[data-house='+$(e).attr('data-del-house')+']').remove()
                                }else{
                                    $(e).remove();
                                }
                            }
                        });
                    } else {
                        app.tips().error(res.message);
                    }
                }, 'json');
        });
        $('.add-member[data-id]').on('click', function () {
            var id = $(this).attr('data-id');
            $('#qrcode').attr('src','/default/qrcode?text=<?=urldecode('http://'.\Yii::$app->params['domain.www'] . '/auth/?group=1&id=')?>'+id)
            $('#qrMask,#qrDiv').show();
            $('#qrMask,#qrDiv .close').on('click',function(){
                $('#qrMask,#qrDiv').hide();
            })
        });
    })
</script>
<?php \common\widgets\JavascriptBlock::end() ?>
