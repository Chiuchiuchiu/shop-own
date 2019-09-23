<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/11 15:09
 * Description:
 */

namespace components\ajaxUpload\assets;


use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@components/ajaxUpload/assets/resources/';

    public $js = [
        'ajaxUpload.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}