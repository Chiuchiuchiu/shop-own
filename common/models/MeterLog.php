<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "meter_log".
 *
 * @property integer $id
 * @property string $uid
 * @property string $meter_type
 * @property string $last_meter_data
 * @property integer $last_meter_time
 * @property string $meter_data
 * @property integer $meter_time
 * @property integer $project_id
 * @property integer $house_id
 * @property integer $member_id
 * @property integer $meter_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $butler_id
 * @property integer $visit_butler_id
 * @property integer $butler_updated_at
 */
class MeterLog extends \yii\db\ActiveRecord
{
    const STATUS_NOT = 1;
    const STATUS_WAIT = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_REPORT = 4;
    const STATUS_INVALID = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meter_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_meter_data', 'meter_data'], 'number'],
            [['last_meter_time', 'meter_time', 'project_id', 'house_id','butler_id', 'visit_butler_id','member_id', 'meter_id', 'status', 'created_at', 'updated_at', 'butler_updated_at'], 'integer'],
            [['uid', 'meter_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'meter_type' => 'Meter Type',
            'last_meter_data' => 'Last Meter Data',
            'last_meter_time' => 'Last Meter Time',
            'meter_data' => 'Meter Data',
            'meter_time' => 'Meter Time',
            'project_id' => 'Project ID',
            'house_id' => 'House ID',
            'member_id' => 'Member ID',
            'meter_id' => 'Meter ID',
            'butler_id'=>'管家',
            'visit_butler_id'=>'上门抄表的管家',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'butler_updated_at' => '管家抄表时间',
        ];
    }
    public function getMeterHouse()
    {
        return $this->hasOne(MeterHouse::className(), ['meter_id' => 'meter_id']);
    }
    public function ancestor_name()
    {
       $meter_id = $this->meter_id;
        $Lib = MeterHouse::findOne(['meter_id'=>$meter_id]);
       if(!isset($Lib)){
           return '--';
       }else{
           return $Lib->ancestor_name;
       }


    }

}
