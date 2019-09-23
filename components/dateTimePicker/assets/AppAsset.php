<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/11 15:09
 * Description:
 */

namespace components\dateTimePicker\assets;


use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@components/dateTimePicker/assets/resources/';

    public $css =[
        'bootstrap-datetimepicker.min.css'
    ];
    public $js = [
        'moment.js',
        'bootstrap-datetimepicker.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset'
    ];
}