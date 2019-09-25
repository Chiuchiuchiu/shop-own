<?php

use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;
use common\models\MinAutumnQuestion;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '题目列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
\components\inTemplate\widgets\IBox::begin(['title' => false]);
\components\inTemplate\widgets\ActiveForm::begin(['layout' => 'horizontal', 'method' => 'get', 'action' => \yii\helpers\Url::toRoute('/min-autumn/answer-list')]); ?>
    <div class="form-group">
        <label class="control-label col-sm-3">查找</label>
        <div class="col-sm-6">
            <div class="input-group m-b">
                <input type="text" name="search" placeholder="题目" value="<?= $search ?>" class="form-control">
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
<?= Html::a('新增题目', ['create'], ['class' => 'btn btn-success pull-right']) ?>

<?= \components\inTemplate\widgets\IBox::widget(['content' => GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'title',
        [
            'label'=>'答案',
            'format' => 'raw',
            'value' => function(MinAutumnQuestion $model){

                $str = '';

                foreach (json_decode($model->answer, true) as &$v){
                    $str .= $v . "<br>";
                }

                return $str;
            }
        ],
        [
            'label'=>'正确答案',
            'format' => 'raw',
            'value' => function(MinAutumnQuestion $model){

                $title = json_decode($model->answer, true);

                return $title[$model->answer_true];
            }
        ],
        'created_at:datetime',
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{update} {setStatus}',
            'buttons' => [
                'setStatus' => function ($key, MinAutumnQuestion $model, $index){
                    return Html::a(
                        "<span class='btn btn-xs " . ($model->status == 1 ? "btn-primary" : "btn-warning") . "'>" . $model::statusType()[$model->status] . "</span>",
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

            var name = status == 1 ? "作废" : "开启"

            layer.confirm("您确定"+ name +"该题目吗？", {
                    btn: ['确定', '取消']},
                function(){
                    var ii = layer.load(1, {
                        shade: [0.1,'#fff'] //0.1透明度的白色背景
                    });
                    $.ajax({
                        type: 'get',
                        url: "/min-autumn/set-status?id=" + id + "&status=" + status,
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