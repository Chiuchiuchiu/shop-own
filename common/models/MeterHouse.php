<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "meter_house".
 *
 * @property integer $id
 * @property string $uid
 * @property string $surname
 * @property string $ownername
 * @property string $ancestor_name
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
 * @property integer $type_id
 */
class MeterHouse extends \yii\db\ActiveRecord
{
    const STATUS_WAIT = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_REPORT = 4;
    const STATUS_WAIT_SUMMARY = 6;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meter_house';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_meter_data', 'meter_data'], 'number'],
            [['last_meter_time', 'meter_time', 'project_id', 'house_id', 'member_id', 'meter_id', 'status', 'type_id','created_at', 'updated_at'], 'integer'],
            [['uid', 'surname', 'ownername', 'meter_type'], 'string', 'max' => 100],
            [['ancestor_name'], 'string', 'max' => 250],
        ];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_WAIT => '待抄表',
            self::STATUS_SUCCESS => '已审核',
            self::STATUS_REPORT => '已拒绝',
            self::STATUS_WAIT_SUMMARY => '待审核'
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '设备编号',
            'surname' => '抄表人',
            'ownername' => '业主姓名',
            'ancestor_name' => '房产物业',
            'meter_type' => '设备类型',
            'last_meter_data' => '上期抄表读数',
            'last_meter_time' => '上期抄表时间',
            'meter_data' => '本期抄表读数',
            'meter_time' => '本期抄表时间',
            'project_id' => 'Project ID',
            'house_id' => 'House ID',
            'member_id' => 'Member ID',
            'meter_id' => '系统ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    public static function SaveAdd($row,$project_id)
    {

      $House = House::findOne(['ancestor_name'=>$row[12],'deepest_node'=>1]);
      if(isset($House))
      {
          if(MeterHouse::find()->where(['meter_id'=>$row[15]])->count()==0)
          {

              $Lib = new MeterHouse();
              $Lib->uid = $row[0];
              $Lib->ownername = $row[13];
              $Lib->ancestor_name = $row[12];
              $Lib->meter_type = $row[2];
              $Lib->last_meter_data = $row[5];
              $Lib->last_meter_time = strtotime($row[4]);
              $Lib->status = 1;
              $Lib->project_id = $project_id;
              $Lib->house_id = $House->house_id;
              $Lib->member_id = 0;
              $Lib->meter_id = $row[15];
              $Lib->created_at=time();
              if($Lib->save()){
                  return 0;
              }
          }else{
          return 1;
          }
      }else{
          return 1;
      }
    }

    public static function SaveMeter($ancestor_name,$id,$meter_id)
    {

            $House = House::findOne(['ancestor_name'=>$ancestor_name,'deepest_node'=>1]);
            if(isset($House))
            {
                $Lib = MeterHouse::findOne(['id'=>$id]);
                $Lib->status=2;
                $Lib->house_id=$House->house_id;
                $Lib->save();
            }else{
                $res = MeterHouse::findOne(['id'=>$id]);
                $res->delete();
            }
    }

    public static function SaveOneAdd($row,$project_id,$uid)
    {
                $Lib = new MeterHouse();
                $Lib->created_uid = $uid;
                $Lib->uid = $row[0];
                $Lib->ownername = $row[13];
                $Lib->ancestor_name = $row[12];
                $Lib->meter_type = $row[2];
                $Lib->last_meter_data = $row[5];
                $Lib->last_meter_time = strtotime($row[4]);
                $Lib->status = 1;
                $Lib->project_id = $project_id;
                $Lib->house_id = 0;
                $Lib->member_id = 0;
                $Lib->meter_id = $row[15];
                $Lib->created_at=time();
                if($Lib->save()){
                    return true;
                }else{
                    return false;
                }
    }


}
