<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = $TopTitle;
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('')]); ?>
<div class="form-group">
    <label class="control-label col-sm-3">关键字搜索</label>
    <div class="col-sm-6">
        <div class="input-group m-b">
            <input type="text" name="keywords" placeholder="请输入联系人或联系电话" value="<?= $keywords ?>" class="form-control">
            <input type="hidden" name="activity_id" value="<?=$activity_id;?>">
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
<?= Html::a('导出报名名单', ['sign-up-export?activity_id='.$activity_id.'&keywords='.$keywords], ['class' => 'btn btn-success pull-right']) ?>
<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
            'id',
        'uid',
        'surname',
        'telephone',
        'site',
        'options1',
        'options2',
        'comment',
        'star_number',
        'ancestor_name',
        'created_at:datetime'

    ],
])
]); ?>
