<?php
use \components\inTemplate\widgets\ActiveForm;
use \common\models\House;
/* @var $projectId int*/


$this->title = '同步数据';

$ids = array_keys($ids);
if( !empty($ids) ){
    \Yii::$app->cache->set('houseIDs_' . $projectId, implode(',', $ids));

    echo '请不要关闭该窗口，正在同步数据…………<br />';
    echo '剩余【'.count($ids).'】个数据未同步 <br />';

    $javascript = '<script type="text/javascript">';
    $javascript .= 'window.location.href="/project/sync-houses-iframe?projectId=' . $projectId . '&ids=implode"';
    $javascript .= '</script>';

    echo $javascript;die;
}

\Yii::$app->cache->delete('houseIDs_' . $projectId);
$ids = null;

$syncCount = House::find()->where(['project_house_id' => $projectId])->count();
$project = \common\models\Project::find()->where(['house_id' => $projectId])->one();
$project->sync_count = $syncCount;
$project->save();

echo '数据同步已完成，已同步数据【'.$syncCount.'】条<br />';
echo '<script type="text/javascript">window.top.alert(\'数据同步已完成\');</script>';