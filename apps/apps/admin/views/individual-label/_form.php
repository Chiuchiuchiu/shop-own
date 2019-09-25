<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/12
 * Time: 14:51
 */

/* @var \common\models\IndividualLabels $model */

\components\inTemplate\widgets\IBox::begin();
$form = \components\inTemplate\widgets\ActiveForm::begin(); ?>

    <div class="form-group field-article-category_id required">
        <label class="control-label col-sm-5" for="article-category_id">标签名：</label>
        <div class="col-sm-2">
            <?= \yii\helpers\Html::textInput($model->formName() . '[name]', $model->name, ['class' => 'form-control', 'required' => true])?>
            <div class="help-block help-block-error "></div>
        </div>
    </div>

    <div class="form-group field-article-category_id required">
        <label class="control-label col-sm-5" for="article-category_id">前端样式名：</label>
        <div class="col-sm-2">
            <?= \yii\helpers\Html::textInput($model->formName() . '[class]', $model->class, ['class' => 'form-control'])?>
            <div class="help-block help-block-error "></div>
        </div>
    </div>

    <div class="form-group text-center">
        <?= \yii\helpers\Html::submitButton('提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

<?php
\components\inTemplate\widgets\ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>