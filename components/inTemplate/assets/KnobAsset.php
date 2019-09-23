<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/11 15:09
 * Description:
 */

namespace components\inTemplate\assets;



use yii\web\AssetBundle;
class KnobAsset extends AssetBundle
{
    public $sourcePath = '@components/inTemplate/assets/resources/';
    public $css = [
    ];
    public $js = [
        'js/plugins/jsKnob/jquery.knob.js'
    ];
    public $depends = [
        'components\inTemplate\assets\AppAsset'
    ];
}