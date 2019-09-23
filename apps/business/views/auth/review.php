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

$this->title = '待认证审核列表';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    \components\inTemplate\widgets\IBox::begin(['title'=>'搜索']);
    $from = \components\inTemplate\widgets\ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['/auth/review'])])
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
            <?= Html::dropDownList('group', $group, ['' => '全部', 1 => '房子', 2 => '车位'], ['class' => 'input-group form-control'])?>
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
    <div>总条数：<?= $dataProvider->totalCount ?></div>
</div>

<?php

\components\inTemplate\widgets\IBox::begin();
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label'=>'HouseId',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->house_id;
            }
        ],
        [
            'label'=>'地址',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->house->ancestor_name;
            }
        ],
        [
            'label'=>'微信昵称',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->member->nickname;
            }
        ],
        [
            'label'=>'客户姓名',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->customer_name;
            }
        ],
        [
            'label'=>'联系手机',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->member->phone;
            }
        ],
        [
            'label'=>'身份',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->identityText;
            }
        ],
        [
            'label'=>'状态',
            'value'=>function(\common\models\MemberHouseReview $model){
                return $model->statusText;
            }
        ],
        [
            'label'=>'认证时间',
            'value'=>function(\common\models\MemberHouseReview $model){
                return date('Y-m-d H:i:s',$model->updated_at);
            }
        ],
        [
            'label'=>'提交时间',
            'value'=>function(\common\models\MemberHouseReview $model){
                return date('Y-m-d H:i:s',$model->created_at);
            }
        ],
        [
            'class' => \components\inTemplate\widgets\RBACActionColumn::className(),
            'template' => '{delete} {update-auth}',
            'buttons' => [
                'delete' => function($key, \common\models\MemberHouseReview $model, $url){
                    return Html::button('删除', [
                        'target' => '_blank',
                        'class' => 'btn btn-xs btn-danger delete-auth',
                        'data-hou' => $model->house_id,
                        'data-mem' => $model->member_id
                    ]);
                },
                'update-auth' => function ($key,\common\models\MemberHouseReview $model, $url) {
                    return $model->status == \common\models\MemberHouseReview::STATUS_WAIT_REVIEW ? Html::button('审批', [
                        'target' => '_blank',
                        'class' => 'btn btn-xs btn-primary approval-auth',
                        'id' => 'approval-auth',
                        'data-hou' => $model->house_id,
                        'data-mem' => $model->member_id
                    ]) : '';
                },
            ]
        ]
    ],
]);
\components\inTemplate\widgets\IBox::end();
?>

<?php \components\inTemplate\widgets\ActiveForm::begin(['id' => 'form']); ?>

    <input type="hidden" name="house_id" value="" id="house_id">
    <input type="hidden" name="member_id" value="" id="member_id">

<?php \components\inTemplate\widgets\ActiveForm::end(); ?>

<?php \common\widgets\JavascriptBlock::begin(); ?>

<script type="text/javascript">

    $('.approval-auth').on('click', function (){
        if(confirm('确定通过审核吗？')){
            var _h = $(this).attr('data-hou');
            var _m = $(this).attr('data-mem');
            $('#house_id').val(_h);
            $('#member_id').val(_m);
            var _s = $('#form').serialize();
            $.post('/auth/auth-member-house', _s, function (res){
                if(res.code == 0){
                    alert('已通过');
                    location.reload();
                }
            }, 'json');
        }
    });

    $('.delete-auth').on('click', function (){
        if(confirm('确定删除审核吗？')){
            var _h = $(this).attr('data-hou');
            var _m = $(this).attr('data-mem');
            $('#house_id').val(_h);
            $('#member_id').val(_m);
            $.get('/auth/delete?member_id=' + _m + "&house_id=" + _h, function (res){}, 'json');
        }
    });

</script>

<?php \common\widgets\JavascriptBlock::end(); ?>
