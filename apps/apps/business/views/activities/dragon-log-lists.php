<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/26
 * Time: 10:16
 */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var integer $house_id */
/* @var array $projectsArray */
/* @var array $typeLists */
/* @var array|string $dateTime */
/* @var string $type */
/* @var string $onlyCa */
/* @var string $IdTrue */
/* @var string $payed */
/* @var int $collType */
/* @var string $search */

$this->title = '端午节活动访问日志';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title' => '搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['activities/dragon-log-lists'])])

?>

    <div class="form-group">
        <div class="col-sm-3">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-12 sl ctr-template">
                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projectsArray,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">

        <div class="col-sm-2">
            <label>时间</label>
            <div class="row">
                <div class="col-sm-12 sl ctr-template">
                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'type',
                        'value' => $type,
                        'items' => $typeLists,
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-5">
            <label for=""></label>
            <?= \kartik\date\DatePicker::widget([
                'model' => $dateTime,
                'attribute' => 'startDate',
                'attribute2' => 'endDate',
                'options' => ['placeholder' => '开始时间'],
                'options2' => ['placeholder' => '结束时间'],
                'type' => \kartik\date\DatePicker::TYPE_RANGE,
                'language' => 'zh-CN',
                'separator' => "至",
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-m-d'
                ]
            ]) ?>
        </div>

    </div>

    <div class="form-group">
        <div class="col-sm-2">
            <input name="search" class="form-control" type="text" value="<?= $search ?>" placeholder="用户名/手机/昵称">
        </div>
        <label class="col-sm-1 control-label">状态：</label>
        <div class="col-sm-2">
            <label class="checkbox-inline">
                <input name="onlyCa" value="true" id="inlineCheckbox1" <?php if($onlyCa == 'true') echo 'checked'; ?> type="checkbox">
                仅访问
            </label>
            <label class="checkbox-inline">
                <input name="IdTrue" <?php if($IdTrue == 'true') echo 'checked'; ?> value="true" id="inlineCheckbox2" type="checkbox">
                已认证
            </label>
            <label class="checkbox-inline">
                <input name="payed" <?php if($payed == 'true') echo 'checked'; ?> value="true" id="inlineCheckbox3" type="checkbox">
                已缴费
            </label>
        </div>
        <label class="col-sm-1 control-label">领取资格：</label>
        <div class="col-sm-3">
            <label class="checkbox-inline">
                <input name="collType" <?php if($collType == 0) echo 'checked'; ?> value="0" id="inlineCheckbox1" type="radio">
                未获得
            </label>
            <label class="checkbox-inline">
                <input name="collType" <?php if($collType == 1) echo 'checked'; ?> value="1" id="inlineCheckbox2" type="radio">
                已获得
            </label>
            <label class="checkbox-inline">
                <input name="collType" <?php if($collType == 2) echo 'checked'; ?> value="2" id="inlineCheckbox2" type="radio">
                全部
            </label>
        </div>
        <div class="col-sm-1">
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'label' => '用户昵称',
            'format' => 'raw',
            'value' => function (\common\models\ActivitiesLog $model) {
                switch ($model->collect_status) {
                    case 0:
                        return \components\za\Html::label($model->nick_name);
                        break;
                    default:
                        return \components\za\Html::label($model->nick_name, '', ['class' => 'text-info']);
                        break;
                }
            }
        ],
        [
            'label'=>'头像',
            'format'=>'raw',
            'value'=>function(\common\models\ActivitiesLog $model){
                return \components\inTemplate\widgets\Html::img($model->member->headimg);
            }
        ],
        'name',
        'phone',
        [
            'label' => '项目名称',
            'value' => function (\common\models\ActivitiesLog $model) {
                return $model->project->house_name;
            }
        ],
        [
            'label' => '认证状态',
            'format' => 'raw',
            'value' => function (\common\models\ActivitiesLog $model) {
                if(isset($model->identification_status)){
                    switch ($model->identification_status) {
                        case 0:
                            return \components\za\Html::label($model->identificationStatusText, '', ['class' => 'text-danger']);
                            break;
                        default:
                            return \components\za\Html::label($model->identificationStatusText, '', ['class' => 'text-info']);
                            break;
                    }
                }else{
                    return "未知";
                }
            }
        ],
        'identification_time:datetime',
        [
            'label' => '缴费状态',
            'format' => 'raw',
            'value' => function (\common\models\ActivitiesLog $model) {
                if(isset($model->pay_status)){
                    switch ($model->pay_status) {
                        case 0:
                            return \components\za\Html::label($model->payStatusText, '', ['class' => 'text-danger']);
                            break;
                        default:
                            return \components\za\Html::label($model->payStatusText, '', ['class' => 'text-info']);
                            break;
                    }
                }else{
                    return "未知";
                }

            }
        ],
        'pay_time:datetime',
        [
            'label' => '领取状态',
            'format' => 'raw',
            'value' => function (\common\models\ActivitiesLog $model) {
                switch ($model->collect_status) {
                    case 0:
                        return \components\za\Html::label($model->collectStatusText, '', ['class' => 'text-danger']);
                        break;
                    default:
                        return \components\za\Html::label($model->collectStatusText, '', ['class' => 'text-info']);
                        break;
                }
            }
        ],
        'collect_time:datetime',
        'ac_order_id',
        'created_at:datetime',
    ],
])
]); ?>