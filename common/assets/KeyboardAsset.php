<?php

namespace common\assets;



use yii\web\AssetBundle;

class KeyboardAsset extends AssetBundle
{
    public $sourcePath = '@common/assets/source/';

    public $js = [
        'script/keyboard.js',
    ];
}
