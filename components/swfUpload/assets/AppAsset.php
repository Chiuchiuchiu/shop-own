<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/22 12:33
 * Description:
 */

namespace components\swfUpload\assets;


use yii\web\AssetBundle;

class AppAsset  extends AssetBundle
{
    public $sourcePath = '@components/swfUpload/assets/resources/';
    public $css = [
        'uploadify.css',
    ];
    public $js = [
        'jquery.uploadify.min.js'

    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}