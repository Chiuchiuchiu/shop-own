<?php

namespace apps\admin\assets;

use yii\web\AssetBundle;

/**
 * Main admin application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $depends = [
        'components\inTemplate\assets\AppAsset',
    ];
}
