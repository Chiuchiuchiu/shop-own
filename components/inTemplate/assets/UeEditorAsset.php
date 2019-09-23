<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/5/29
 * Time: 10:57
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class UeEditorAsset extends AssetBundle
{
    public $sourcePath = "@components/inTemplate/assets/resources/ueEditor/";

    public $css = [
        'dialogs/xiumi/xiumi-ue-v5.css',
    ];

    public $js = [
        'ueditor.config.js',
        'ueditor.all.min.js',
        'dialogs/xiumi/xiumi-ue-dialog-v5.js',
    ];
    public $depends = [
    ];
}