<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/5/30
 * Time: 11:54
 */

namespace apps\business\valueObject;


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