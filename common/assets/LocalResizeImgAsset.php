<?php

namespace common\assets;

use yii\web\AssetBundle;

class LocalResizeImgAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source/';

    public $js = [
        'script/lib/LocalResizeIMG.js',
    ];
    public $css = [
        'style/local-resize-img.css'
    ];
}
