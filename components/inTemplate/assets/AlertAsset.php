<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/11 15:09
 * Description:
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class AlertAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
        'css/plugins/toastr/toastr.min.css'
    ];
    public $js = [
        'js/plugins/toastr/toastr.min.js'
    ];
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
}