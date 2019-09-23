<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_christmas_bill_item".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $house_id
 * @property string $phone
 * @property string $customer_name
 * @property integer $created_at
 */
class PmChristmasBillItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_christmas_bill_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'house_id'], 'required'],
            [['member_id', 'house_id', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['customer_name'], 'string', 'max' => 30],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'house_id' => 'House ID',
            'phone' => 'Phone',
            'customer_name' => 'Customer Name',
            'created_at' => 'Created At',
        ];
    }
}
