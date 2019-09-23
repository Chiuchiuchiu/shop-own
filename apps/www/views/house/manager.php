<?php
/**
 * @var $_user \apps\www\models\Member
 * @var $parkingRs
 * @var array $list
 */
?>
<div class="panel" id="house-manager">
    <div class="banner"></div>
    <div class="house-list">
        <ul>
            <?php foreach ($list as $model) { ?>
                <?php /* @var $model \common\models\MemberHouse */ ?>
                <li data-id="<?= $model->house_id ?>">
                    <div>
                        <i style="background-image:url(<?= Yii::getAlias($model->house->project->icon) ?>);background-size:contain"></i>
                        <p><?= $model->house->showName ?></p>
                        <h6 class="newwindow-house-n">(<?= $model->house->ancestor_name ?>)</h6>
                    </div>
                    <div data-id="<?= $model->house_id ?>" class="del">删除</div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <a href="javascript:;" style="margin-top:2em" class="btn btn-block btn-primary new_add_primary">添加新房产</a>
    <a href="/member/search-houses?" style="margin-top:2em" class="btn btn-block btn-primary">批量添加房产</a>
    <div style="height: 6em;line-height: 6em;">

    </div>
</div>

<div id="warning-tips">
    <div class="mask"></div>
    <div class="warning-content-box">
        <div class="warning-content"></div>
        <div class="warning-back-box" style="display: none;">
            <a class="close-hidden">关闭</a>
            <a class="comf-step" data-id="">确定</a>
        </div>
    </div>
</div>

<?php \common\widgets\JavascriptBlock::begin() ?>
    <script>

        $(".new_add_primary").on('click',function () {
            $.ajax({
                type: 'GET',
                dataType: "json",
                url: '/auth/ajax-house-count',
                data: "",
                timeout: 3000, //超时时间：30秒
                success: function (res) {
                    var _res = res.data;
                    if(_res.code == 1){
                        location.href = '/auth/index?group=1';
                    }else{
                        alert(_res.message);
                    }
                }
            });
        });

        $('#house-manager').on('loaded', function () {
            $('.del[data-id]', this).bind('click', function () {
                var id = $(this).attr('data-id');
                /*if (window.confirm("确定要删除该处房子吗？")) {
                    $.getJSON('/house/del-house?id=' + $(this).attr('data-id'), function (res) {
                        if (res) {
                            $('li[data-id=' + id + ']').remove();
                        }
                    });
                }*/

                $('.plaId').val('');
                $('.comf-step').attr('data-id', '');
                $('.warning-content').text('临近活动/活动期间暂停删除房产！');
                $('#warning-tips').show().fadeOut(2000);
            })
        });
    </script>
<?php \common\widgets\JavascriptBlock::end() ?>