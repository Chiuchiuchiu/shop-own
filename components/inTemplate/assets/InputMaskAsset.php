<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/1 11:00
 * Description:
 */

namespace components\inTemplate\assets;


use yii\web\AssetBundle;

class InputMaskAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $js = [
        'js/plugins/jasny/jasny-bootstrap.min.js'
    ];
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
}