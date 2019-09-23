<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $userName string */
/* @var $phone string */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;

?>


<?php
\components\inTemplate\widgets\IBox::begin();
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['member/index'])]);
?>

    <div class="form-group">

        <div class="col-sm-2">
            <label class="control-label">用户名</label>
            <input type="text" class="form-control" placeholder="用户名" name="name" value="<?= $userName?>">
        </div>

        <div class="col-sm-2">
            <label class="control-label">手机号</label>
            <input type="text" class="form-control" placeholder="手机号" name="phone" value="<?= $phone?>">
        </div>

        <div class="col-sm-4">
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

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?php echo Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
        </div>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

    <div class="col-lg-12">
        <div>总条数：<?= $dataProvider->totalCount ?></div>
    </div>

<?php
\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'nickname',
        [
            'label'=>'头像',
            'format'=>'raw',
            'value'=>function(\apps\admin\models\Member $model){
                return \components\inTemplate\widgets\Html::img($model->headimg);
            }
        ],
        'name',
        'phone',
        'created_at:datetime',

    ],
]);
\components\inTemplate\widgets\IBox::end();