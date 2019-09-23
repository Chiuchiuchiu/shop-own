<?php
/**
 * Created by
 * Author: zhao <m420092@126.com>
 * Time: 16/6/1 13:55
 * Description:
 */

namespace components\arEasyCache;

use Yii;

trait ActiveRecordCacheTrait
{

    private static $cacheDefaultConfig = [
        'cacheKeyPrefix' => 'ar-',
        'cacheComponentName' => 'cache',
        'cacheDuration' => YII_ENV==YII_ENV_PROD?180:5,
    ];


    /**
     * @return ActiveQueryCache
     * Description:
     */
    public static function find()
    {
        return Yii::createObject(ActiveQueryCache::className(), [get_called_class(),
            array_merge(self::$cacheDefaultConfig, self::cacheConfig())

        ]);
    }

    /**
     * @return array
     * Description: override this method you to config
     * ```
     * return [
     *  'cacheKeyPrefix' => 'ar-',
     *  'cacheComponentName' => 'cache',
     *  'cacheDuration' => 60,
     * ];
     * ```
     */
    public static function cacheConfig()
    {
        return [
        ];
    }
}