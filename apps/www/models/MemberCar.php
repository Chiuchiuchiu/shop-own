<?php

namespace apps\www\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_car".
 *
 * @property integer $id
 * @property integer $type  1：临卡 2：月卡
 * @property integer $member_id
 * @property string $plate_number
 * @property integer $created_at
 */
class MemberCar extends \yii\db\ActiveRecord
{
    const TYPE_T = 1;
    const TYPE_M = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_car';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'created_at'], 'integer'],
            [['plate_number'], 'string', 'max' => 10],
        ];
    }

    public static function typeMap()
    {
        return [
            self::TYPE_T => '临卡',
            self::TYPE_M => '月卡',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '1/临卡 2/月卡',
            'member_id' => 'Member ID',
            'plate_number' => 'Plate Number',
            'created_at' => 'Created At',
        ];
    }

    public static function createOrUpdate($memberId, $plateNumber, $type)
    {
        $model = self::findOne(['member_id' => $memberId, 'plate_number' => $plateNumber]);
        if(!$model){
            $model = new self();
        }

        $model->created_at = time();
        $model->member_id = $memberId;
        $model->plate_number = $plateNumber;
        $model->type = $type;

        return $model->save();
    }

}
