<?php


/* @var $this yii\web\View */
/* @var $projectModel yii\web\View */
/* @var $sign $sign */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status int */
/* @var $search string */
/* @var array $projectRegion */
/* @var int $projectRegionId */

use common\models\Project;
use \components\inTemplate\widgets\Html;

$this->title = '楼盘管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/project')]);
?>
    <div class="form-group">
        <div class="col-sm-2">
            <div class="input-group m-b">
                <?= Html::dropDownList('status', $status,array_merge(['' => '全部'], Project::statusMap()) )?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b col-sm-12">

                <?php echo \components\inTemplate\widgets\Chosen::widget([
                    'name' => 'project_region_id',
                    'value' => $projectRegionId,
                    'items' => $projectRegion,
                ])?>

            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="项目名" value="<?= $search ?>" class="form-control">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="input-group m-b">
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

    <div class="col-lg-1">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?php
echo \components\inTemplate\widgets\Html::a('新增', ['create', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-success pull-right']);
?>
<?=\components\inTemplate\widgets\IBox::widget([
    'content'=>\components\inTemplate\widgets\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'house_id',
            'house_name',
            'projectRegionName',
            [
                'label'=>'logo',
                'format'=>'raw',
                'value'=>function(Project $model){
                    return \components\inTemplate\widgets\Html::img($model->logo);
                }
            ],
            'url_key',
            'statusText',
            'sync_count',
            [
                'label' => '缴费周期',
                'format' => 'raw',
                'value' => function(Project $model){
                    $feeCycleName =  isset($model->projectFeeCycle->name) ? $model->projectFeeCycle->name : '未设置';
                    return Html::label($feeCycleName, '', ['class' => 'text-warning']);
                }
            ],
            'payTypeText',
            'mchId',
            [
                'label' => '默认账号/密码',
                'format' => 'raw',
                'value' => function(Project $model){
                    $res = \apps\pm\models\PmManager::find()
                        ->where(['project_house_id' => $model->house_id, 'need_change_pw' => 1])
                        ->andWhere(['name' => 'admin_'.$model->url_key])
                        ->asArray()->one();

                    return $res ? '账号：' . 'admin_'.$model->url_key . ' 密码：' . '123456' : '';
                }
            ],
            [
                'label' => '项目管理后台',
                'format' => 'raw',
                'value' => function(Project $model){
                    return Html::a('进入管理后台', '/project/jump-pm?key=' . $model->url_key, ['target' => '_blank']);
                }
            ],
            [
                'label' => '小程序',
                'format' => 'raw',
                'value' => function(Project $model){
                    return Html::a('下载二维码', '/project/download-qrcode?projectId='.$model->house_id, ['download']);
                }
            ],
            [
                'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
                'template'=>'{update} {edit-structure} {detail?house_id}',
                /*'buttons'=>[
                    'sync-houses'=>function($url, $model, $key){
                        return Html::a('同步楼盘数据', ['sync-houses','projectId'=>$model->house_id], ['class' => 'btn btn-xs btn-success']);
                    }
                ],*/
                'name'=>[
                    'detail'=>'查看明细',
                    'edit-structure'=>'楼盘结构编辑'
                ]
            ],
        ],
    ])
])?>