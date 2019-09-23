<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/29
 * Time: 14:50
 */

namespace components\redis;


use yii\data\Pagination;

class RedisPage extends Pagination
{
    public static $redis;
    public $keys;
    public $pStart;
    public $pOffset;
    public $defaultOffset = 20;

    public function init()
    {
        parent::init();
        self::$redis = \Yii::$app->redis;
    }

    public function setKeys($key)
    {
        $this->keys = $key;

        return $this;
    }

    public function getTotalCount()
    {
        $sortedSetsKey = $this->keys;
        $ZCardKeyTotal  = self::$redis->zcard($sortedSetsKey);

        $ZCardKeyPageCounts = (int) ceil($ZCardKeyTotal / $this->defaultPageSize);

        $this->totalCount = $ZCardKeyPageCounts;
    }

    public function getZcrad()
    {
        $sortedSetsKey = $this->keys;
        $ZCardKeyTotal  = self::$redis->zcard($sortedSetsKey);

        $ZCardKeyPageCounts = (int) ceil($ZCardKeyTotal / $this->defaultPageSize);

        return $ZCardKeyPageCounts;
    }

    public function getZrange()
    {
        $this->setStart();
        $keys = $this->keys;

        $values  = self::$redis->zrange($keys, $this->pStart, $this->pOffset);

        return $values;
    }

    public function setStart()
    {
        $getPage = $this->getQueryParam($this->pageParam);
        $offset = (int) $this->defaultOffset - 1;
        $pStart = 0;

        if($getPage){
            $page = $getPage - 1;
            $pStart = ($page * $this->defaultPageSize + 1);
            $offset = $pStart * 2 - 1;
        }

        $this->pStart = $pStart;
        $this->pOffset = $offset;
    }

    public function exists($key)
    {
        return self::$redis->exists($key);
    }

    public function zadd($prefixKey, array $value)
    {
        if(is_array($value)){
            foreach($value as $key => $row){
                self::$redis->zadd($prefixKey, $key, $row);
            }
        }

        return true;
    }


}