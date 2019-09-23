<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/16 10:16
 * Description:
 */

namespace components\inTemplate\widgets;


use components\ajaxUpload\widgets\AjaxUpload;
use yii\db\ActiveRecord;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
    public $layout = 'horizontal';

    public function submitButton($model = null, $text = '提交')
    {
        return '<div class="form-group text-center">' .
        Html::submitButton($text, ['class' => $model instanceof ActiveRecord && $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
        . '</div>';
    }

    public function imgUpload($model, $field, $label)
    {
        $key = md5(serialize([$field,$label]));
        $upload = \components\swfUpload\widgets\SwfUpload::widget([
            'model' => $model,
            'field' => $field,
            'imgKey' => 'img' . $key
        ]);
        $showField = preg_replace('/\[.+\]/','',$field);
        $pic = \Yii::getAlias($model->$showField);
        return <<<HTML
    <div class="form-group">
        <label class="control-label col-sm-3">{$label}</label>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-xs-9">{$upload}</div>
                <div class="col-xs-3">
                    <img id="img{$key}" class="pull-right" src="{$pic}" style="max-width:100%">
                </div>
            </div>
        </div>
    </div>
HTML;
    }

    public function ajaxUpload($model, $fileName, $target, $modelFiled, $label)
    {
        $key = md5(serialize([$fileName, $target]));
        AjaxUpload::widget([
            'target' => $target,
            'key' => $key,
            'modelFiled' => $modelFiled,
        ]);

        $showField = preg_replace('/\[.+\]/','', $modelFiled);
        $pic = \Yii::getAlias($model->$showField);
        if(empty($pic)){
            $pic = "/static/images/empty_ad_pc.png";
        }
        return <<<HTML
    <div class="form-group" id="pic_div">
        <label class="control-label col-sm-3">{$label}</label>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-xs-9">
                <input name="UploadObject[file]" type="file" id="{$target}" value="图片" accept="image/gif, image/x-png, image/jpeg">
                </div>
                <div class="col-xs-3">
                    <img id="img{$key}" class="pull-right" src="{$pic}" style="max-width:100%">
                </div>
                <input type="hidden" value="{$pic}" name="{$model->formName()}[{$modelFiled}]" id="{$modelFiled}{$key}">
            </div>
        </div>
    </div>
HTML;
    }

}