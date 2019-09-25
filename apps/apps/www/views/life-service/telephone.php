<?php
/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 * @var integer $selectedCategory
 */
$dataProvider->getModels();
?>

<div class="panel" id="new-repair-list" data-max-page="<?= $dataProvider->pagination->pageCount ?>">

    <div class="ui-row-flex tab-list">

        <i class="icon left"><img src="/static/images/icon/left-arrow.png" width="30" height="30" style="margin: 0 0.4em"></i>

            <input type="hidden" value="0" class="num"/>
            <input type="hidden" value="<?= count($category) ?>" class="categoryCount"/>

            <?php foreach ($category as $k => $v){ ?>
                <div class="categoryList<?= $k ?>" style="<?= ($k < 3 ? '' : 'display: none') ?>">
                    <a href="telephone?selectedCategory=<?= $v['id'] ?>" <?= $v['id'] == $selectedCategory ? 'class="hover"' : '' ?>>
                        <?= $v['name'] ?>
                    </a>
                </div>
            <?php } ?>

        <i class="icon right"><img src="/static/images/icon/right-arrow.png" width="30" height="30" style="margin: 0 0.4em"></i>
    </div>
    <?php
    if ($dataProvider->count == 0)
        echo "<div class=\"empty-status\"><i></i>暂无相关内容</div>";
    else
        echo $this->render('telephone-cell', ['dataProvider' => $dataProvider]);
    ?>

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
    <a href="/house/choose-bill">
        <i class="repair-blue"></i>
        便民电话
    </a>
    <a href="/house/">
        <i class="owners"></i>
        我的
    </a>
</div>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">
    $(function(){

        var count = $('.categoryCount').val();
        var num = $('.num').val();  //页数
        var leftObj = $('.left');
        var rightObj = $('.right');

        if(count > 3){
            leftObj.click(function(){
                num --;

                if (num < 0) {
                    num = 0;
                    return;
                }else{
                    $('.num').val(num);
                }

                $('.tab-list > div').hide();

                var prev1 = num * 3 + 1*1;
                var prev2 = num * 3 + 2*1;

                $('.categoryList' + num * 3).show();
                $('.categoryList' + prev1).show();
                $('.categoryList' + prev2).show();
            });

            rightObj.on('click', function(){
                num ++;
                if(num >= Math.ceil(count / 3)){
                    num = Math.ceil(count / 3) - 1;
                    return;
                }else{
                    $('.num').val(num);
                }

                $('.tab-list > div').hide();

                var next1 = num * 3 + 1*1;
                var next2 = num * 3 + 2*1;

                $('.categoryList' + num * 3).show();
                $('.categoryList' + next1).show();
                $('.categoryList' + next2).show();
            });
        }else{
            leftObj.hide();
            rightObj.hide();
        }
    })
</script>

<?php \common\widgets\JavascriptBlock::end(); ?>

