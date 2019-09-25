<?php

namespace apps\www\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath ='@app/assets/dist/';

    public $css = [
        'css/app.css?002',
        'css/vote.css?12',
        'css/site.css?12',
    ];
    public $js = [
        'js/lib/jquery-3.1.1.min.js',
        'js/lib/keyboard.js',
        'js/lib/LocalResizeIMG.js',
        'js/lib/jquery.event.drag-1.5.min.js',
        'js/lib/jquery.touchSlider.js',
        'js/lib/clipboard.min.js',
        'js/lib/park.js',
        'js/lib/require.js',
    ];
}