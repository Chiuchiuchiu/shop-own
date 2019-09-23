<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "parking_order_notify_error_log".
 *
 * @property integer $id
 * @property integer $parking_order_id
 * @property string $to_user_id
 * @property integer $status
 * @property integer $created_at
 */
class ParkingOrderNotifyErrorLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parking_order_notify_error_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
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
    public function rules()
    {
        return [
            [['parking_order_id', 'status', 'created_at'], 'integer'],
            [['to_user_id'], 'string'],
        ];
    }

    public static function writeLog($parkingOrderId, $toUserIds)
    {
        $model = new self();
        $model->parking_order_id = $parkingOrderId;
        $model->to_user_id = $toUserIds;

        return $model->save();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parking_order_id' => 'Parking Order ID',
            'to_user_id' => 'To User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
