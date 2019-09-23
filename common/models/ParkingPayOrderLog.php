<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "parking_pay_order_log".
 *
 * @property integer $id
 * @property string $msg
 * @property string $response
 * @property integer $order_id
 * @property integer $created_at
 */
class ParkingPayOrderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parking_pay_order_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['response'], 'string'],
            [['order_id', 'created_at'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null
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
            'msg' => 'msg',
            'response' => 'Response',
            'order_id' => 'Order ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param array|string $response
     * @param integer $orderId
     * @param string $msg
     * @return bool
     */
    public static function writeLog($response, $orderId, $msg)
    {
        $model = new self();
        $model->response = serialize($response);
        $model->order_id = $orderId;
        $model->msg = $msg;

        return $model->save();
    }

}
