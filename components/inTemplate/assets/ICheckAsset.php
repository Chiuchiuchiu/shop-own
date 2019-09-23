<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/14
 * Time: 15:07
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class ICheckAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
        'css/plugins/iCheck/custom.css',
    ];
    public $js = [
        'js/plugins/iCheck/icheck.min.js',
    ];
}