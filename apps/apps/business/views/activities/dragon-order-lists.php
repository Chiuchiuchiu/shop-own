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
/* @var int|string $status */

$this->title = '端午节活动领取人员列表';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['activities/dragon-order-lists'])])

?>

    <div class="form-group">

        <div class="col-sm-7">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-4 sl ctr-template">

                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projectsArray,
                    ])?>

                </div>

            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-1 control-label">状态：</label>
        <div class="col-sm-3">
            <label class="checkbox-inline">
                <input name="status" <?php if($status == 0) echo 'checked'; ?> value="0" id="inlineCheckbox1" type="radio">
                认证
            </label>
            <label class="checkbox-inline">
                <input name="status" <?php if($status == 1) echo 'checked'; ?> value="1" id="inlineCheckbox2" type="radio">
                缴费
            </label>
            <label class="checkbox-inline">
                <input name="status" <?php if($status == 2) echo 'checked'; ?> value="2" id="inlineCheckbox2" type="radio">
                全部
            </label>
        </div>
        <div class="col-sm-2">
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

    <div class="col-lg-12">
        <?php
        echo \yii\helpers\Html::a(
            '导出当前数据报表',
            ['dragon-export', 'house_id' => $house_id, 'status' => $status],
            ['class'=>'btn btn-info pull-right']
        );
        ?>
    </div>

<?= \components\inTemplate\widgets\IBox::widget(['content' => \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'user_name',
        [
            'label'=>'头像',
            'format'=>'raw',
            'value'=>function(\common\models\ActivitiesCollectOrder $model){
                return \components\inTemplate\widgets\Html::img($model->member->headimg);
            }
        ],
        [
            'label' => '手机号',
            'format' => 'raw',
            'value' => function (\common\models\ActivitiesCollectOrder $model){
                return \yii\helpers\Html::label($model->tel, '', ['class' => 'text-info']);
            }
        ],
        [
            'label'=>'认证/缴费',
            'format' => 'raw',
            'value'=>function(\common\models\ActivitiesCollectOrder $model){
                return $model->house->ancestor_name;
            }
        ],
        [
            'label' => '领取状态',
            'format' => 'raw',
            'value' => function (\common\models\ActivitiesCollectOrder $model){
                return \yii\helpers\Html::label($model->statusText, '', ['class' => 'text-success']);
            }
        ],
        [
            'label'=>'用户收货地址',
            'format' => 'raw',
            'value'=>function(\common\models\ActivitiesCollectOrder $model){
                return $model->userHouse->ancestor_name;
            }
        ],
        'comment',
        'created_at:datetime',
    ],
])
]); ?>