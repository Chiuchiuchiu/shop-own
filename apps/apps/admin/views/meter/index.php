<?php

use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = ($type == 1 ? '水' : '电') . '表设备管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/meter/index')]); ?>
    <div class="form-group">

        <div class="col-sm-2">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-12 sl ctr-template">

                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projects,
                    ])?>

                </div>

            </div>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?php echo \yii\bootstrap\Html::input('input', 'search', $search, ['class' => 'krajee-datepicker form-control', 'placeholder' => '房号|设备号']) ?>
        </div>

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>

        <?= \yii\bootstrap\Html::input('hidden', 'type', $type) ?>

    </div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
<?php if($house_id){ ?>

    当前共<?=$dataProvider->totalCount?>条数据
    <?= \components\inTemplate\widgets\IBox::widget([
        'content' => GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'uid',
                'meter_id',
                'ancestor_name',
                'last_meter_data',
                'last_meter_time:datetime',
                'meter_data',
                [
                    'label' => '上期抄表读数',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->meter_time > 0 ? date('Y-m-d H:i:s') : "未抄表";
                    },
                ],
            ],
        ])
    ]); ?>
<?php } ?>