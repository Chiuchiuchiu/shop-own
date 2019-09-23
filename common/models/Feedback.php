<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "feedback".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $house_id
 * @property integer $member_id
 * @property string $name
 * @property string $tel
 * @property string $content
 * @property string $pics
 * @property integer $status
 * @property integer $created_at
 *
 * @property House $house
 * @property Member $member
 */
class Feedback extends \yii\db\ActiveRecord
{
    const STATUS_WAIT=0;
    const STATUS_UNDERWAY=1;
    const STATUS_COMPLETE=2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'feedback';
    }
    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
    }
    public function getTypeText(){
        return self::TypeMap()[$this->type];
    }

    public function getStatusText(){
        return self::StatusMap()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'house_id', 'member_id', 'name', 'tel', 'content'], 'required'],
            [['type', 'house_id', 'member_id', 'status', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 10],
            ['type','in','range'=>array_keys(self::TypeMap()),'message'=>'请选择类型'],
            [['tel'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 500],
            [['pics'], 'string', 'max' => 300],
            ['status', 'default', 'value' => 0],
        ];
    }
    public static function TypeMap(){
        return [
            1=>'环境卫生',
            2=>'服务态度',
            3=>'公共设施',
            4=>'其他投诉',
        ];
    }
    public static function StatusMap(){
        return [
            0=>'待处理',
            1=>'进行中',
            2=>'已完成'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'house_id' => 'House ID',
            'member_id' => 'Member ID',
            'name' => 'Name',
            'tel' => 'Tel',
            'content' => 'Content',
            'pics' => 'Pics',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }
    public function getMember(){
        return $this->hasOne(Member::className(),['member_id'=>'id']);
    }
}
