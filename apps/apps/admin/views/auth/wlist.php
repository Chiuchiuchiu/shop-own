<?php
use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $projectHouseAmount int */
/* @var $verifiedHouseAmount int */
/* @var $parkingSpacesAmount int */
/* @var $verifiedParkingSpacesAmount int */
/* @var $dateTime \common\valueObject\RangDateTime */
/* @var $status int */
/* @var $group int */
/* @var $projectId int */

$this->title = '业主白名单列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    \components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
    $from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['/auth/white-list'])])
?>

    <div class="form-group">
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
            <input type="text" name="kw" placeholder="微信昵称" value="<?= $kw ?>" class="form-control">
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
    <a class="btn btn-success" href="/auth/create?_referrer=%2Fauth/white-list">新增白名单</a>
    <div>总条数：<?= $dataProvider->totalCount ?></div>
</div>

<?php

\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label'=>'ID',
            'value'=>function(\common\models\MemberHouseWList $model){
                return $model->id;
            }
        ],
        [
            'label'=>'会员ID',
            'value'=>function(\common\models\MemberHouseWList $model){
                if($model->member_id){
                    return $model->member_id;
                }else{
                    return '--';
                }
            }
        ],
        [
            'label'=>'微信昵称',
            'value'=>function(\common\models\MemberHouseWList $model){
                if($model->member){
                    return $model->member->nickname;
                }else{
                    return '--';
                }
            }
        ],
        [
            'label'=>'头像',
            'format'=>'raw',
            'value'=>
                function(\common\models\MemberHouseWList $model){
                if($model->member){
                    return  \components\inTemplate\widgets\Html::img($model->member->headimg);
                }else{
                    return '--';
                }

            }
        ],
        [
            'label'=>'房产授权数',
            'value'=>function(\common\models\MemberHouseWList $model){
                return $model->auth_count;
            }
        ],
        [
            'label'=>'添加时间',
            'value'=>function(\common\models\MemberHouseWList $model){
                return date('Y-m-d H:i:s',$model->created_at);
            }
        ],
        [
            'label'=>'更新时间',
            'value'=>function(\common\models\MemberHouseWList $model){
                return date('Y-m-d H:i:s',$model->updated_at);
            }
        ],
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => ' {edit-wlist}' ,
            'buttons' => [
                'edit-wlist' => function ($key,\common\models\MemberHouseWList $model, $url) {
                    return  Html::button('编辑', [
                        'target' => '_blank',
                        'class' => 'btn btn-xs btn-primary approval-auth',
                        'id' => 'approval-auth',
                        'data-id' => $model->id,
                        'data-mem' => $model->member_id
                    ]);
                },
            ]
        ]
    ],
]);
\components\inTemplate\widgets\IBox::end();
?>

<?php \components\inTemplate\widgets\ActiveForm::begin(['id' => 'form']); ?>

    <input type="hidden" name="member_id" value="" id="member_id">

<?php \components\inTemplate\widgets\ActiveForm::end(); ?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $('.approval-auth').on('click', function (){
        var _id = $(this).attr("data-id");
        location.href = '/auth/create?id=' + _id;

    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
