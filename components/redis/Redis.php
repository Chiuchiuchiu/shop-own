<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/21
 * Time: 15:40
 */

namespace components\redis;


use yii\base\ErrorException;

class Redis
{
    public static $redis;
    public function __construct()
    {
        self::init();
    }

    public static function init()
    {
        try{
            self::$redis = \Yii::$app->redis;
            return self::$redis;
        } catch (ErrorException $e){
            echo "Error: no redis.\n";
            exit(1);
        }
    }

}