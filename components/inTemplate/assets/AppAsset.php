<?php

namespace components\inTemplate\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
        'css/bootstrap.min.css',
        'font-awesome/css/font-awesome.min.css',
        'css/style.min.css',
        'css/plugins/iCheck/custom.css',
        'css/animate.css',
    ];
    public $js = [
        'js/bootstrap.min.js',
        'js/plugins/metisMenu/jquery.metisMenu.js',
        'js/plugins/slimscroll/jquery.slimscroll.min.js',
        'js/inspinia.js',
        'js/plugins/pace/pace.min.js',
        'js/plugins/iCheck/icheck.min.js',
        'js/layer/layer.js',
        'js/layer/Common.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}
