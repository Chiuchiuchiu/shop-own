<?php


/* @var $this yii\web\View */
/* @var $publicUseWechatsMp WechatsConfigPublic */
/* @var $projectModel yii\web\View */
/* @var $sign $sign */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */
/* @var $projectAmount int */
/* @var $wechatsPublicAmount int */

use apps\pm\models\WechatsConfigPublic;
use \components\inTemplate\widgets\Html;

$this->title = '订阅号列表';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php if(!empty($publicUseWechatsMp)){?>

<?php \components\inTemplate\widgets\IBox::begin(['title' => '默认公众号（用来配置公用内容，此号不可用于生产）']) ?>

    <div class="ibox float-e-margins " style="">
        <div class="ibox-content">
            <div class="grid-view">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>公众号名称</th>
                        <th>AppID(应用ID)</th>
                        <th>公众号原始ID</th>
                        <th class="action-column">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr data-key="<?= $publicUseWechatsMp->id?>">
                        <td><?= $publicUseWechatsMp->id?></td>
                        <td><?= $publicUseWechatsMp->public_name?></td>
                        <td><?= $publicUseWechatsMp->app_id?></td>
                        <td><?= $publicUseWechatsMp->token?></td>
                        <td><a class="btn btn-xs btn-info" href="/wechats-mp-manage/open-wmp?id=0" 0="0"
                               color="info">管理微信公众号</a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php \components\inTemplate\widgets\IBox::end() ?>

<?php }?>

<?php
    \components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
    \components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/wechats-mp-manage')]);
?>
    <div class="form-group">
        <div class="col-sm-3">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="项目名" value="<?= $search ?>" class="form-control">
                <span class="input-group-btn">
                <button type="submit" class="btn btn-primary">查找</button>
            </span>
            </div>
        </div>
    </div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
<p>
<?php

echo Html::button('楼盘数（' . $projectAmount . '）', ['class' => 'btn btn-success pull-left']);
echo Html::button('微信订阅号已受权数（' . $dataProvider->totalCount . '）', ['class' => 'btn btn-info pull-left']);
echo Html::buttonA('同步微信公众号用户数据', ['sync-wechats-users'], ['class' => 'btn btn-w-m btn-success pull-right']);
echo Html::buttonA('查看数据', ['show-wechats-data'], ['class' => 'btn btn-w-m btn-warning pull-right']);
echo Html::buttonA('查看周报表：认证/缴费数', ['week-data'], ['class' => 'btn btn-w-m btn-primary pull-right']);
echo Html::buttonA('各项目管家分属区域数据', ['/butler-export'], ['class' => 'btn btn-w-m btn-success pull-right']);

?>
</p>

<?= \components\inTemplate\widgets\IBox::widget([
    'content' => \components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'ProjectName',
            'public_name',
            'all_users_total',
            'app_id',
            'public_id',
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template' => '{open}',
                'buttons' => [
                    'open' => function ($key, $model, $url) {
                        return Html::buttonA('管理微信公众号', ['open-wmp', 'id' => $model->project_id], [$model->project_id]);
                    },
                ]
            ],
        ],
    ])
]) ?>