<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = '活动列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/article')]); ?>
<div class="form-group">
    <label class="control-label col-sm-3">查找</label>
    <div class="col-sm-6">
        <div class="input-group m-b">
            <input type="text" name="search" placeholder="标题" value="<?= $search ?>" class="form-control">
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
<?= Html::a('新建活动', ['create'], ['class' => 'btn btn-success pull-right']) ?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
            'id',
        [
            'label'=>'活动图片',
            'format' => 'raw',
            'value' => function(\common\models\Activity $model){
                return '<img src="'.Yii::getAlias($model->pic).'" width="100">';
            }
        ],
        'title',
        'ProjectName',
        'options1',
        'options2',
        'bg_color',
        [
            'label'=>'报名人数',
            'format' => 'raw',
            'value' => function(\common\models\Activity $model){
                $Count = \common\models\SignUp::count($model->id);
                return Html::a($Count.'个', '/activity/sign-up-list?activity_id=' . $model->id, ['class' => 'btn btn-success']);
            }
        ],
        'click_numbers',
        'auth_numbers',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{open} {update} {delete}',
            'buttons' => [
                'open' => function ($key, $model, $url) {
                    return Html::buttonA('链接', 'http://' . \Yii::$app->params['domain.www'] . '/activity?id=' . $model->id, [
                        'target' => '_blank'
                    ]);
                },
            ]
        ], [
            'label'=>'拷贝活动',
            'format' => 'raw',
            'value' => function(\common\models\Activity $model){
                return Html::a('拷贝活动', 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'ActivityCopy(\''.$model->id.'\');']);
            }
        ], [
            'label'=>'报名二维码',
            'format' => 'raw',
            'value' => function(\common\models\Activity $model){
                return Html::a('点击查看', 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'ActivityQrcode(\''.$model->id.'\');']);
            }
        ],[
            'label'=>'操作',
            'format' => 'raw',
            'value' => function(\common\models\Activity $model){
              if($model->status==0){
                  return Html::a('开启', '/activity/close-type?id=' . $model->id, ['class' => 'btn btn-info']);
              }else{
                  return Html::a('关闭', '/activity/close-type?id=' . $model->id, ['class' => 'btn btn-danger']);
              }

            }
        ],
    ],
])
]); ?>
