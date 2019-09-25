<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = '调查问卷';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/question')]); ?>
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
<?= Html::a('新增问卷', ['create'], ['class' => 'btn btn-success pull-right']) ?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',
        'created_at:datetime',
        'site',
        'typeText',
        'statusText',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {delete}',
        ]
    ],
])
]); ?>
