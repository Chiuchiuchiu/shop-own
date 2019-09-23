<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/22 12:33
 * Description:
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class KindEditorAsset  extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/kindEditor/';
    public $css = [
        'themes/default/default.css',
        'plugins/code/prettify.css',
    ];
    public $js = [
        'kindeditor-all-min.js',
        'lang/zh-CN.js',
        'plugins/code/prettify.js'

    ];
    public $depends = [
    ];
}