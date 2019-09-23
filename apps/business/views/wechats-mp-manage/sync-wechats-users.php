<?php

$this->title = '同步微信公众号用户数据';
$this->params['breadcrumbs'][] = ['label' => '楼盘管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);
?>
<?php \components\inTemplate\widgets\IBox::begin()?>
    <div>
        <iframe src="http://<?= Yii::$app->params['domain.wmp']?>?s=/home/user/sync_all_wechats_users" width="100%" height="500"></iframe>
    </div>
<?php \components\inTemplate\widgets\IBox::end()?>