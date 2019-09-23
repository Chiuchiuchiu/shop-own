<?php
namespace apps\www\module\prepayLottery\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
//    public $sourcePath = YII_ENV==YII_ENV_DEV?'@app/assets/dist/':'@app/assets/build/';
    public $sourcePath = '@app/module/prepayLottery/assets/src/';
    public $css = [
        'app.css?2'
    ];
    public $js = [
        'js.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}