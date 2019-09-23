<?php

use \yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $model apps\backend\models\ManagerGroup */
/* @var $rbac apps\backend\models\RBAC */
/* @var $userGroup */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<div class="row">
    <div class="col-md-5">
        <?= \components\inTemplate\widgets\IBox::widget([
            'title' => "基本信息",
            'content' => $form->field($model, 'name')->textInput(['maxlength' => true])
        ]) ?>
    </div>
    <div class="col-md-7">
        <?php \components\inTemplate\widgets\IBox::begin(['title' => "权限配置"]) ?>
        <h3>全局权限</h3>

        <div class="row">
            <?php /*
            <div class="col-md-4">
                <label>
                    <?php
                    \yii\bootstrap\Html::checkbox($model->formName() . "[permission][option][root]", $model->checkOptionPermission('root')) ?>
                    超级管理权
                </label>
            </div>
            */?>

        </div>
        <h3>局部设置</h3>

        <div class="dd">
            <?php
            //递归循环输出权限列表
            function echoRBAC($rbac,\apps\business\models\ManagerGroup $model, $userGroup)
            {

                echo '<ol class="dd-list">';
                foreach ($rbac as $v) {
                    if(isset($userGroup['option']) || in_array($v->id, $userGroup['route'])){
                        echo '<li class="dd-item">';
                        echo '<div class="dd-handle">';
                        printf('<span class="text-info %s"></span> %s<span class="pull-right">%s</span>',
                            $v->icon,
                            $v->name,
                            \yii\bootstrap\Html::checkbox($model->formName() . "[permission][route][]", $model->hasPermission($v), ['value' => $v->id])
                        );
                        echo '</div>';
                        if (isset($v->child) > 0) {
                            echoRBAC($v->child, $model, $userGroup);
                        }
                        echo "</li>";
                    }
                }
                echo '</ol>';
            }

            echoRBAC($rbac, $model, $userGroup);
            ?>
        </div>
        <?php \components\inTemplate\widgets\IBox::end() ?>
    </div>
</div>
<div class="row">
    <div class="form-group">
        <div class="text-center">
            <?= \yii\bootstrap\Html::submitButton($model->isNewRecord ? '创建账户' : '提交修改', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php \common\widgets\JavascriptBlock::begin() ?>
<script>
    $('.dd ol .dd-handle input[type=checkbox]').on('click', function () {
        var parent = $(this).parentsUntil('ol').parent().siblings('.dd-handle').find('input[type=checkbox]');
        if(!parent) return ;
        //选中，当上级没选中
        if ($(this).is(':checked') && !parent.is(':checked')) {
            parent.click();
            return ;
        }
        //取消选择，判断是否需要处理上级，判断是否需要处理下级
        if($(this).is(':not(:checked)')){
            $(this).parentsUntil('li').parent().find('input[type=checkbox]').attr("checked",false);
            if(!$(this).parentsUntil('ol').siblings('li').find(">.dd-handle input[type=checkbox]").is(":checked")){
                parent.click();//处理上级
                //处理下级
                return;
            }
            //寻找子元素
        }
    });
    $('.dd-item .dd-handle').on('click',function(){
        $('input[type=checkbox]',$(this)).click();
    })
</script>
<?php \common\widgets\JavascriptBlock::end() ?>
