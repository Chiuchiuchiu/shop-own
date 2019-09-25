<?php
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $type int */
/* @var $rs array */
/* @var $result array */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = '周数据报表';
$this->params['breadcrumbs'][] = $this->title;

echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['index']]);

\components\inTemplate\widgets\IBox::begin(['title' => '搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['wechats-mp-manage/week-data'])])
?>

<div class="form-group">
    <div class="col-sm-5">
        <label>时间</label>
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

    <div class="col-sm-3">
        <label>&nbsp;</label>
        <?= \components\inTemplate\widgets\Html::dropDownList('type', $type, ['认证', '缴费'], ['class' => 'input-group form-control']) ?>

    </div>


    <div class="col-sm-2">
        <label>&nbsp;</label>
        <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
    </div>
</div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<div class="col-lg-12">
    <div>【1->小区】【2->组团】【3->大楼】【4->单元】【5->房间】【6->别墅】【7->排屋】【8->储藏室】【10->停车场】【11->车区】【9->车位】【13->自行车位】【14->广告位】【15->卫星收视】
    </div>
</div>

<?php

switch ($type){
    case 0:
        echo \yii\helpers\Html::label('总认证数：' . $result['count']);
        break;
    case 1:
        echo \yii\helpers\Html::label('总缴费户数：' . $dataProvider->totalCount);
        echo '&nbsp;&nbsp;&nbsp;&nbsp;';
        echo \yii\helpers\Html::label('总实际缴费金额：' . $result['amount']);
        break;
}
echo \yii\helpers\Html::a(
    '导出去重数据报表',
    ['export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'type' => $type, 'distinct' => 1],
    ['class'=>'btn btn-primary pull-right']
);
echo \yii\helpers\Html::a(
    '导出不去重数据报表',
    ['export', 'RangDateTime[startDate]'=>$dateTime->getStartDate(), 'RangDateTime[endDate]'=>$dateTime->getEndDate(), 'type' => $type, ],
    ['class'=>'btn btn-success pull-right']
);

?>

<?php
\components\inTemplate\widgets\IBox::begin();
?>

<?php
switch ($type) {
    case 0:
        ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>项目全称</th>
            <th>总数</th>
            <th>类型</th>
            <th>认证数</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach($rs as $rKey => $rVal){?>

            <tr data-key="3">
                <td><?= $rVal['house_name']?></td>
                <td><?php switch($rVal['group']){
                        case 1:
                            echo $parkingSpacesAmount = \common\models\House::find()
                                ->where(['house.project_house_id'=>$rVal['project_house_id'], 'reskind' => 5])
                                ->count();
                            break;
                        case 2:
                            echo $parkingSpacesAmount = \common\models\House::find()
                                ->where(['house.project_house_id'=>$rVal['project_house_id'], 'reskind' => 11])
                                ->orWhere(['house.project_house_id'=>$rVal['project_house_id'], 'reskind' => 9])
                                ->count();
                            break;
                    };?></td>
                <td><?= \common\models\MemberHouse::groupMap()[$rVal['group']]?></td>
                <td><?= $rVal['count']?></td>
            </tr>

        <?php }?>

        </tbody>
    </table>
<?php
        break;
    case 1:
        echo \components\inTemplate\widgets\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => '项目名',
                    'value' => function ($model){
                        return $model->house->project->house_name;
                    }
                ],
                [
                    'label' => '房地全称',
                    'value' => function ($model){
                        return $model->house->ancestor_name;
                    }
                ],
                [
                    'label' => '房产类型',
                    'value' => function ($model){
                        return $model->house->reskind;
                    }
                ],
                [
                    'label' => '缴费笔数',
                    'value' => function ($model){
                        return $model->number;
                    }
                ],
                [
                    'label' => '缴费额',
                    'value' => function ($model){
                        return $model->total_amount;
                    }
                ],
            ],
        ]);
        break;
}
?>

<?php
\components\inTemplate\widgets\IBox::end();


?>
