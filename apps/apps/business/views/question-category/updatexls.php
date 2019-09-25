<?php

use components\inTemplate\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\ButlerVisitIndicators */

$this->title = '导入问卷题目';
$this->params['breadcrumbs'][] = ['label' => '调研问卷', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
 \components\inTemplate\widgets\IBox::begin();
\components\ajaxUpload\widgets\AjaxUpload::widget([]);
$form = ActiveForm::begin(['layout' => 'horizontal','id'=>'uploadFile','action'=>'/question-category/upload-xls-save','options' => ['enctype' => 'multipart/form-data']]);
?>
    <div class="form-group field-butlervisitindicators-third_quarter required">
        <label class="control-label col-sm-3" for="butlervisitindicators-third_quarter">问卷分类</label>
        <div class="col-sm-6">
           <?=$model->title;?>
            <div class="help-block help-block-error "></div>
        </div>

    </div><div class="form-group field-butlervisitindicators-fourth_quarter required">
        <label class="control-label col-sm-3" for="butlervisitindicators-fourth_quarter">上传文件</label>
        <div class="col-sm-6">
            <input name="UploadObject[file]" required id="excelFile" class="form-control" type="file" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
        </div>

    </div>

    <div class="row">
        <div class="form-group">
            <label class="control-label col-sm-3" for="butlervisitindicators-fourth_quarter">&nbsp;</label>
            <div class="col-sm-6">
                <button type="submit" id="submitFile" class="btn btn-info">查询</button>
            </div>
        </div>
    </div>

<?php
ActiveForm::end();
\components\inTemplate\widgets\IBox::end();
?>
