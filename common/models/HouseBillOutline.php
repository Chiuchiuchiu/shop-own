<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "house_bill_outline".
 *
 * @property integer $house_id
 * @property integer $bill_count
 * @property string $total_amount
 * @property string $aggregate_data
 * @property int $process_status 处理状态：0：未处理；1：已处理
 * @property integer $updated_at
 *
 * @property House $house
 * @property MemberHouse $member_house
 */
class HouseBillOutline extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
          [
              'class'=>TimestampBehavior::className(),
              'createdAtAttribute'=>null,
          ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house_bill_outline';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['house_id', 'bill_count', 'total_amount', 'updated_at'], 'required'],
            [['house_id', 'bill_count', 'updated_at', 'process_status'], 'integer'],
            [['total_amount', ], 'number'],
            [['aggregate_data'],'string'],
            [['bill_count','total_amount', 'process_status'], 'default','value'=>0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'house_id' => 'House ID',
            'bill_count' => 'Bill Count',
            'total_amount' => 'Total Amount',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @param $house_id
     * @return HouseBillOutline
     * Description:
     */
    public static function findOrCreate($house_id){
        $res = self::findOne($house_id);
        if(!$res){
            $res=new self();
            $res->house_id=$house_id;
            $res->updated_at=time();
        }
        return $res;
    }

    public function getMemberHouse(){
        return $this->hasMany(MemberHouse::className(), ['house_id' => 'house_id']);
    }

    public function getHouse(){
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }
}
