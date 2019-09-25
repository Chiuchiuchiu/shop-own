<?php

/* @var $model common\models\Project */

$this->title = '同步楼盘数据 > ' . $model->house_name;
$this->params['breadcrumbs'][] = ['label' => '楼盘管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>
<?php \components\inTemplate\widgets\IBox::begin()?>
<div>
    <iframe src="sync-houses-iframe?projectId=<?=$model->house_id?>" width="100%" height="300"></iframe>
</div>
<?php \components\inTemplate\widgets\IBox::end()?>

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">
    $(function () {
        document.onkeydown = function()
        {
            var k = event.keyCode;
            if((event.ctrlKey == true && k == 82) || (k == 116) || (event.ctrlKey == true && k == 116))
            {
                event.keyCode = 0;
                event.returnValue = false;
                event.cancelBubble = true;
            }
        }
    })
</script>

<?php \common\widgets\JavascriptBlock::end();?>
