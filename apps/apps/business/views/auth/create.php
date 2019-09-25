<?php
use \components\inTemplate\widgets\ActiveForm;
use common\models\Project;

/* @var $this yii\web\View */
/* @var $model common\models\MemberHouseWList */
/* @var $dateTime  */

$this->title = '新增业主白名单';
$this->params['breadcrumbs'][] = ['label' => '白名单管理', 'url' => ['white-list']];
$this->params['breadcrumbs'][] = $this->title;
echo \components\inTemplate\widgets\BackBtn::widget(['url' => ['white-list']]);
?>

<?php \components\inTemplate\widgets\IBox::begin();
$form = ActiveForm::begin(['layout' => 'horizontal']);
?>

<div class="form-group required">
    <input type="hidden" name="MemberHouseWList[id]"  value="<?= $model->id ?>"/>
    <input type="hidden" name="MemberHouseWList[type]"  value="<?= $model->type ?>"/>
    <label class="control-label col-sm-3" for="project-words">输入业主ID</label>
    <div class="col-sm-4">
        <input id="member-id" name="MemberHouseWList[member_id]" class="form-control" value="<?= $model->member_id ?>" type="text">
        <div id="projectWordsError" class="help-block help-block-error "></div>
    </div>

    <div class="col-sm-4">
        <a id="searchProject" class="btn btn-w-m btn-info">查询</a>
    </div>

</div>


<div class="form-group required">
    <label class="control-label col-sm-3" for="project-words">授权数</label>
    <div class="col-sm-4">
        <input id="auth_count" name="MemberHouseWList[auth_count]" value="<?= $model->auth_count ?>" class="form-control" type="text">
        <div id="auth_countError" class="help-block help-block-error "></div>
    </div>
</div>

<div class="form-group required">
    <label class="control-label col-sm-3" for="project-words">备注</label>
    <div class="col-sm-4">
        <textarea id="remark" class="form-control" name="MemberHouseWList[remark]"><?= $model->remark?></textarea>
    </div>
</div>


<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
</div>
<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>

<?php \common\widgets\JavascriptBlock::begin();?>

<script type="text/javascript">

    $(function () {
        $('#searchProject').click(function () {
            var memberID = $('#member-id').val();
            memberID = memberID.trim();
            if(memberID.length < 1){
                return false;
            }

            $.ajax({
                type: 'GET',
                url: '/auth/search-member',
                async: false,
                data: {id:memberID},
                success: function (data) {
                    if(data.code == 0){
                        alert(data.data);
                    } else {
                        alert(data.message);
                    }
                },
                dataType: 'json'
            });
        });
    });


</script>

<?php \common\widgets\JavascriptBlock::end();?>
