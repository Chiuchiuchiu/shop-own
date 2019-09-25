<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;
use apps\admin\models\QuestionCategory;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $search string */

$this->title = '调查问卷分类';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/question-category')]); ?>





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
<?= Html::a('新增问卷分类', ['create'], ['class' => 'btn btn-success pull-right']) ?>
<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',

        [
            'label'=>'上级分类',
            'format' => 'raw',
            'value' => function(QuestionCategory $model){
                $feeCycleName =  isset($model->category->title) ? $model->category->title : '--';
                return $feeCycleName;
            }
        ],
        [
            'label'=>'题目总数',
            'format' => 'raw',
            'value' => function(QuestionCategory $model){
           $Count = QuestionCategory::getCategorycount($model->id);
                return Html::a($Count.'个', '/question/index?category_id=' . $model->id, ['target' => '_blank','class' => 'btn btn-xs btn-success']);

            }
        ],
        [
            'label'=>'导入题目',
            'format' => 'raw',
            'value' => function(QuestionCategory $model){
                return Html::a('导入题目', 'javascript:void(0);', ['class' => 'btn btn-xs btn-success','onclick'=>'ShowImportExcel(\''.$model->id.'\');']);
             }
        ],
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {delete}',
        ]
    ],
])
]); ?>
