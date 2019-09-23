<?php
/**
 * Created by PhpStorm.
 * User: HQM
 */

namespace common\valueObject;


class FileCache
{
    /**
     * @var \yii\caching\FileCache
     * @return \yii\caching\FileCache
     */
    public static function init()
    {
        $fileCache = new \yii\caching\FileCache();
        return $fileCache;
    }
}