<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/19 09:54
 * Description:
 */

namespace apps\admin\valueObject;

use yii\behaviors\AttributeBehavior;

class DateTime extends \common\valueObject\ValueObject
{
    public $startTime;
    public $endTime;
    public $startDate;
    public $endDate;


    public $date;

    /**
     * @var
     * 未实现Time的支持
     */
    public $dateTime;
    public $time;


    const SCENARIO_RANGE = 'range';



    public function scenarios()
    {
        return [
            self::SCENARIO_RANGE => ['startDate', 'endDate','startTime','endTime'],
            self::SCENARIO_DEFAULT => ['dateTime','time','date','startTime','endTime']
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_AFTER_VALIDATE => ['startTime'],
                ],
                'value' => function(){
                        if(!empty($this->startTime) &&  is_int($this->startTime+0)) return $this->startTime;
                        switch($this->scenario){
                            case self::SCENARIO_RANGE:
                                return strtotime($this->startDate);
                            default:
                                return strtotime($this->date);

                        }
                }
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_AFTER_VALIDATE => ['endTime'],
                ],
                'value' => function(){
                    if(!empty($this->endTime) &&  is_int($this->endTime+0)) return $this->endTime;
                    switch($this->scenario){
                        case self::SCENARIO_RANGE:
                            return strtotime($this->endDate);
                        default:
                            return strtotime($this->date);

                    }
                }
            ],
        ];
    }


    public function rules()
    {
        return [
            ['startDate', 'default', 'value' => date('Y-m-d', strtotime('-7 days')), 'on' => self::SCENARIO_RANGE],
            ['startDate', 'date', 'format' => 'php:Y-m-d'],
            ['startTime', 'integer'],
            ['startTime', 'compare', 'compareValue' => 0, 'operator' => '>'],

            ['endDate', 'default', 'value' => date('Y-m-d', strtotime('-1 days')), 'on' => self::SCENARIO_RANGE],
            ['endDate', 'date', 'format' => 'php:Y-m-d'],
            ['endTime', 'integer'],
            ['endTime', 'compare', 'compareValue' => 'startDate', 'operator' => '>'],

            ['date', 'default','value'=>date('Y-m-d'),'on'=>self::SCENARIO_DEFAULT],
            ['date', 'date','format' => 'php:Y-m-d'],
            ['time', 'integer'],
            ['time', 'integer'],
        ];
    }

    public function dateToTime($value)
    {
        $this->$value =
            is_numeric($this->$value) && is_int($this->$value + 0) ? $this->$value :
                (strtotime($this) > 0 ? 1 : 1);
        return true;
    }

    public function attributeLabels()
    {
        return [
            'startDate' => "开始时间",
            'endDate' => "结束时间",
            'time' => '时间',
        ];
    }
    public function dateToMonth(){
        return [
            strtotime(date('Y-m-01',strtotime($this->date))." 00:00:00"),
            strtotime(date('Y-m-t',strtotime($this->date))." 23:59:59")
        ];
    }
}