<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/20 15:40
 * Description:
 */

namespace components\behaviors;


use yii\base\Behavior;
use yii\base\ErrorException;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

class LogRecordBehavior extends Behavior
{

    public $logClass;
    public $dataAttributes = 'data';
    public $relationIdAttributes;
    public $on = [];

    public function init()
    {
        parent::init();
        if (!is_array($this->on)) {
            $this->on = [$this->on];
        }
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',

            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function beforeSave($event)
    {
        //开启事务
        /* @var $ar ActiveRecord */
        $ar = $event->sender;
        if (sizeof($this->on) == 0 || in_array($ar->scenario, $this->on))
            $ar->getDb()->beginTransaction();
    }

    public function afterSave($event)
    {

        /* @var $ar ActiveRecord */
        $ar = $event->sender;
        if (sizeof($this->on) == 0 || in_array($ar->scenario, $this->on)) {
            /* @var $logClass ActiveRecord */
            $logClass = new $this->logClass();
            $dataAttributes = $this->dataAttributes;
            $relationIdAttributes = $this->relationIdAttributes;
            $logClass->$dataAttributes = serialize($ar->attributes);

            $primaryKey = $ar->primaryKey;
            if (is_array($primaryKey)) {
                throw new NotSupportedException;
            }
            $logClass->$relationIdAttributes = $ar->primaryKey;
            if (!$logClass->save()) {
                throw new ErrorException(serialize($logClass->getErrors()));
            }
            if ($ar->getDb()->getTransaction()->isActive)
                $ar->getDb()->getTransaction()->commit();
        }

    }
}