<?php

use yii\helpers\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var string $phone */
/* @var integer $house_id */

$this->title = '管家授权号';
$this->params['breadcrumbs'][] = $this->title;

\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['butler-auth/index'])])
?>

    <div class="form-group">

        <div class="col-sm-7">
            <label>项目</label>
            <div id="region_ctr" class="row">
                <div class="col-sm-4 sl ctr-template">

                    <?php echo \components\inTemplate\widgets\Chosen::widget([
                        'name' => 'house_id',
                        'value' => $house_id,
                        'items' => $projects,
                    ])?>

                </div>

            </div>
        </div>
    </div>

    <div class="form-group">

        <div class="col-sm-2">
            <label>&nbsp;</label>
            <?php echo \yii\bootstrap\Html::input('input', 'phone', $phone, ['class' => 'krajee-datepicker form-control', 'placeholder' => '手机号']) ?>
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
    <?= Html::a('新增', ['create', '_referrer' => Yii::$app->request->url], ['class' => 'btn btn-success']);?>
</div>

<div class="col-lg-12">
    <div>总条数：<?= $dataProvider->totalCount ?></div>
</div>

<?php

\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'projectName',
        'account',
        'password',
        'groupText',
        [
            'label' => '关联管家/保安',
            'format' => 'raw',
            'value' => function(\common\models\ButlerAuth $model){
                $butlerName =  isset($model->butler) ? $model->butler->nickname : '未设置';
                return Html::label($butlerName, '', ['class' => 'text-warning']);
            }
        ],
        [
            'label' => '管理区域',
            'format' => 'raw',
            'value' => function(\common\models\ButlerAuth $model){
                return $model->region;
            }
        ],
        'statusText',
        'created_at:datetime',
        'used_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {delete}'
        ],
    ],
]);
\components\inTemplate\widgets\IBox::end();