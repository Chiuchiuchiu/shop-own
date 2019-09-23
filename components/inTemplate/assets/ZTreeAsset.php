<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/23
 * Time: 10:25
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class ZTreeAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
        'css/plugins/zTree/zTreeStyle.css',
    ];
    public $js = [
        'js/plugins/zTree/jquery.ztree.core.min.js',
        'js/plugins/zTree/jquery.ztree.excheck.min.js',
        'js/plugins/zTree/jquery.ztree.exedit.min.js',
    ];
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
}