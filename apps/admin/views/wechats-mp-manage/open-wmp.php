<?php
/** @var $sign string */
/** @var $projectName string */

$this->title = $projectName . ' > ' . '微信公众号管理';
$this->params['breadcrumbs'][] = ['label' => '公众号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget();
?>
<?php \components\inTemplate\widgets\IBox::begin()?>


    <iframe src="http://<?= Yii::$app->params['domain.wmp']?>?sign=<?= $sign?>" width="100%" height="900px"></iframe>

<?php \components\inTemplate\widgets\IBox::end()?>