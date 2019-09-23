<?php

namespace common\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source/';

    public $js = [
        'script/lib/zeptojs/zepto.min.js',
        'script/frozen.js',
        'script/common.js',
    ];
    public $css = [
        'style/frozen.css',
        'style/style.css'
    ];
}
