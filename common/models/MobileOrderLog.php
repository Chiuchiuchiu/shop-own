<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mobile_order_log".
 *
 * @property integer $id
 * @property string $data
 * @property string $mobile
 * @property string $amount
 * @property string $number
 * @property integer $created_at
 */
class MobileOrderLog extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mobile_order_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['amount'], 'number'],
            [['created_at'], 'integer'],
            [['mobile'], 'string', 'max' => 255],
            [['number'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data' => 'Data',
            'mobile' => 'Mobile',
            'amount' => 'Amount',
            'number' => 'Number',
            'created_at' => 'Created At',
        ];
    }
}
