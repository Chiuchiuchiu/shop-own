<?php

namespace common\models;

use apps\pm\models\PmManager;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "butler_visit_indicators".
 *
 * @property integer $id
 * @property integer $butler_id
 * @property integer $pm_manager_id
 * @property integer $project_house_id
 * @property integer $management_number
 * @property integer $reside_number
 * @property string $years
 * @property string $the_first_quarter
 * @property string $second_quarter
 * @property string $third_quarter
 * @property string $fourth_quarter
 * @property string $identification
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Butler $butler
 * @property PmManager $pmManager
 * @property string $butlerNickName
 * @property string $butlerRegionHouseName
 */
class ButlerVisitIndicators extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'butler_visit_indicators';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['butler_id', 'years', 'the_first_quarter', 'second_quarter', 'third_quarter', 'fourth_quarter'], 'required'],
            [['butler_id', 'pm_manager_id', 'project_house_id', 'management_number', 'reside_number', 'created_at'], 'integer'],
            [['the_first_quarter', 'second_quarter', 'third_quarter', 'fourth_quarter'], 'string', 'max' => 10],
            ['the_first_quarter', 'default', 'value' => 0],
            ['second_quarter', 'default', 'value' => 0],
            ['third_quarter', 'default', 'value' => 0],
            ['fourth_quarter', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'butler_id' => '管家',
            'pm_manager_id' => '录入者',
            'years' => '年份',
            'management_number' => '管理户数',
            'butlerRegionHouseName' => '管理区域',
            'reside_number' => '入住户数',
            'the_first_quarter' => '第一季度指标',
            'second_quarter' => '第二季度指标',
            'third_quarter' => '第三季度指标',
            'fourth_quarter' => '第四季度指标',
            'identification' => '年份辨别值',
            'created_at' => '录入时间',
            'updated_at' => '更改时间',
        ];
    }

    /**
     * 获取季度字段名
     * @param $quarter
     * @return string
     */
    public static function getQuarterName($quarter)
    {
        $quarterName = '';
        switch($quarter){
            case 1:
                $quarterName = 'the_first_quarter';
                break;
            case 2:
                $quarterName = 'second_quarter';
                break;
            case 3:
                $quarterName = 'third_quarter';
                break;
            default:
                $quarterName = 'fourth_quarter';
                break;
        }

        return $quarterName;
    }

    public function getPmManager()
    {
        return $this->hasOne(PmManager::className(), ['id' => 'pm_manager_id']);
    }

    public function getButler()
    {
        return $this->hasOne(Butler::className(), ['id' => 'butler_id']);
    }

    public function getButlerNickName()
    {
        return $this->butler instanceof Butler ? $this->butler->nickname : $this->butler_id;
    }

    public function getButlerRegionHouseName()
    {
        return $this->butler->regionHouseName;
    }

}
