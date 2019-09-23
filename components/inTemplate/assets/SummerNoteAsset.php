<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/16 10:47
 * Description:
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class SummerNoteAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
        'css/plugins/summernote/summernote.css',
        'css/plugins/summernote/summernote-bs3.css'
    ];
    public $js = [
        'js/plugins/summernote/summernote.min.js',
        'js/plugins/summernote/summernote-zh-CN.js'
    ];
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
}