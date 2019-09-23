<?php

namespace components\inTemplate\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class GalleryAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
    public $css = [
        'css/plugins/blueimp/css/blueimp-gallery.min.css',
    ];
    public $js = [
        'js/plugins/blueimp/jquery.blueimp-gallery.min.js',
    ];
}
