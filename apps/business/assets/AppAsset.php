<?php

namespace apps\business\assets;

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
