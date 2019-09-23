<?php

namespace components\inTemplate\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class ChosenAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
        'css/plugins/chosen/chosen.css',
    ];
    public $js = [
        'js/plugins/chosen/chosen.jquery.js',
    ];
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
}
