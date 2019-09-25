<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;
use common\models\QuestionUserChose;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = '调研住户信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
$from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['/question/user-list'])])
?>
<div class="form-group">

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <select class="input-group form-control" name="status" id="status">
            <option value=""   <?php if($status==''){ echo ' selected';}?> >不筛选</option>
            <option value="1"  <?php if($status==1){ echo ' selected';}?>>未调研</option>
            <option value="2"  <?php if($status==2){ echo ' selected';}?>>已调研</option>
        </select>
    </div>
    <div class="col-sm-2">
        <label>&nbsp;</label>
        <input type="text" name="keywords" placeholder="请输入关键字姓名|手机|物业单位" value="<?=$keywords;?>" class="form-control">
    </div>

    <div class="col-sm-2">
        <label>&nbsp;</label>
        <input type="hidden" name="project_id" value="<?=$project_id;?>">
        <?php echo \yii\bootstrap\Html::submitButton('查询', ['class' => 'btn btn-info btn-block']) ?>
    </div>
</div>
<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
当前共<?=$dataProvider->totalCount?>位住户,已经完成调研的<?=$QuestionCount2;?>人,尚未完成调研的<?=$QuestionCount1;?>人
<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'house_name',
        'username',
        'telephone',
        'house_status',
        [
            'label'=>'状态',
            'format' => 'raw',
            'value' => function(QuestionUserChose $model){
                if($model->status==1){
                    return '<font color="red">待调研</font>';
                }else{
                    return Html::a('已调研', '/question/user-update?id=' . $model->id.'&project_id='.$model->project_id, ['target' => '_top','class' => 'btn btn-xs btn-success']);

                }
            }
        ]
    ],
])
]); ?>
