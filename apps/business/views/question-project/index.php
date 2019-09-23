<?php

use \apps\business\models\QuestionProject;
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
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/question-project')]); ?>
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
<?= Html::a('开发商、业委会、居委会 问卷管理', ['develop-question'], ['class' => 'btn btn-primary']) ?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',
        [
            'label'=>'收到的问卷',
            'format' => 'raw',
            'value' => function(QuestionProject $model){
                $Count = QuestionProject::AnswerCount($model->id);
                //href="javascript:void(0);" onclick="js_method()"

                return Html::a($Count.'个', '/question-project/answer-list?id='.$model->id, ['class' => 'btn btn-xs btn-success']);
            }
        ], [
            'label'=>'选择题目',
            'format' => 'raw',
            'value' => function(QuestionProject $model){
                return Html::a('选择题目', 'javascript:void(0);', ['class' => 'btn btn-xs btn-success','onclick'=>'QuestionChoose(\''.$model->id.'\');']);
            }
        ], [
            'label'=>'已选题目',
            'format' => 'raw',
            'value' => function(QuestionProject $model){
            if($model->content==''){
                return 0;
            }else{
                $ArrCount = explode(',',$model->content);
                return count($ArrCount);
            }

            }
        ], [
            'label'=>'活动开始时间',
            'format' => 'raw',
            'value' => function(QuestionProject $model){
                if($model->start_date==null){
                    return '';
                }else{

                    return $model->start_date;
                }

            }
        ], [
            'label'=>'活动结束时间',
            'format' => 'raw',
            'value' => function(QuestionProject $model){
                if($model->end_date==null){
                    return '--';
                }else{

                    return $model->end_date;
                }

            }
        ],
        'created_at',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {delete} {setStatus}',
            'buttons' => [
                    'setStatus' => function ($key,QuestionProject $model, $index){
                        return Html::a(
                                $model->status == 1 ? "<span class='btn btn-xs btn-primary'>进行中</span>" :'<span class="btn btn-xs btn-warning">已关闭</span>',
                                "javascript:;",
                                [
                                    'class' => 'setStatus',
                                    'data-status' => $model->status,
                                    'data-id' => $model->id,
                                ]
                        );
                    }
            ],
        ],

    ],
])
]); ?>
<?php \common\widgets\JavascriptBlock::begin();?>
    <script>
        $('.setStatus').click(function(){

            var status = $(this).data('status');

            var id = $(this).data('id');

            var name = status == 1 ? "关闭" : "开启"

            layer.confirm("您确定"+ name +"该调研计划？", {
                    btn: ['确定', '取消']},
                function(){
                    var ii = layer.load(1, {
                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                    });
                    $.ajax({
                        type: 'get',
                        url: "/question-project/set-status?id=" + id + "&status=" + status,
                        timeout: 3000, //超时时间：30秒
                        dataType:'json',
                        success: function (data) {
                            if(data.code == 0){
                                layer.msg("编辑成功，请稍等...", {
                                    time:500,
                                    end:function(){
                                        location.reload();
                                    }
                                })
                            }else{
                                layer.close(ii);
                                layer.msg(data.message);
                            }
                        }
                    });
                },
                function(){}
            )

        })
    </script>
<?php \common\widgets\JavascriptBlock::end();?>