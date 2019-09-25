<?php
/** @var $sign string*/
/** @var $token string*/

$this->title = '微信公众号数据查看';
$this->params['breadcrumbs'][] = ['label' => '微信公众号数据查看', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>
<?php \components\inTemplate\widgets\IBox::begin()?>

    <iframe src="http://<?= Yii::$app->params['domain.wmp']?>/index.php?s=/home/user/show_wechats_data&sign=<?= $sign?>&token=<?= $token?>" width="100%" height="900px"></iframe>

<?php \components\inTemplate\widgets\IBox::end()?>