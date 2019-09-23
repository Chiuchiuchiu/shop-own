<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/19 09:54
 * Description:
 */

namespace common\valueObject;

use yii\base\Exception;
use yii\behaviors\AttributeBehavior;

/**
 * Class DateTime
 * Description:提供时间的各种格式
 * @var $startDate string
 * @var $startTime integer
 * @var $startDateTime string
 * @var $endDate string
 * @var $endTime integer
 * @var $endDateTime string
 */
class RangDateTime extends ValueObject
{
    private $startTime;
    private $endTime;
    /**
     * format Y-m-d
     */
    private $startDate;
    private $endDate;

    /**
     * format Y-m-d H:i:s
     */
    private $startDateTime;
    private $endDateTime;


    private $trueValue = [];

    public function __set($name, $value)
    {
        $res = parent::__set($name, $value);
        $this->trueValue[] = $name;
        return $res;
    }

    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);
        foreach (array_keys($values) as $k) array_push($this->trueValue, $k);
    }


    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return mixed
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     * @param mixed $endDateTime
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * @param mixed $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }


    public function handle()
    {
        if (sizeof($this->trueValue) > 0) {
            $this->trueValue = array_unique($this->trueValue);
            foreach ($this->trueValue as $value) {
                if(empty($this->$value)){
                    $this->$value = $this->defaultValue($value);
                }
                switch ($value):
                    case 'startDate':
                        $this->startDateTime = $this->startDate . " 00:00:00";
                        $this->startTime = strtotime($this->startDateTime);
                        break;
                    case 'endDate':
                        $this->endDateTime = $this->endDate . " 23:59:59";
                        $this->endTime = strtotime($this->endDateTime);
                        break;
                endswitch;
            }
            $this->trueValue = [];
        }
    }

    private function defaultValue($key){
        $value = [
            'startDate'=>date('Y-m-d', strtotime('-6 days')),
            'endDate'=>date('Y-m-d')
        ];
        return isset($value[$key])?$value[$key]:NULL;
    }

    public function rules()
    {
        return [
            ['startDate', 'default', 'value' => date('Y-m-d', strtotime('-6 days'))],
            ['startDate', 'date', 'format' => 'php:Y-m-d'],
            ['startTime', 'integer'],
            ['startTime', 'compare', 'compareValue' => 0, 'operator' => '>'],
            ['startDateTime', 'date', 'format' => 'php:Y-m-d H:i:s'],

            ['endDate', 'default', 'value' => date('Y-m-d')],
            ['endDate', 'date', 'format' => 'php:Y-m-d'],
            ['endTime', 'integer'],
            ['endTime', 'compare', 'compareValue' => 'startDate', 'operator' => '>'],
            ['endDateTime', 'date', 'format' => 'php:Y-m-d H:i:s'],

        ];
    }


    public function attributeLabels()
    {
        return [
            'startDate' => "开始时间",
            'endDate' => "结束时间",
        ];
    }

    public function beforeValidate()
    {
        $this->handle();
        return parent::beforeValidate();
    }

    public function eachDate(){
        $res = [];
        for($i = $this->startTime;$i<$this->endTime;$i+=86400){
            $res[]=date('Y-m-d',$i);
        }
        return $res;
    }
}