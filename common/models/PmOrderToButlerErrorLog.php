<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_to_butler_error_log".
 *
 * @property integer $id
 * @property integer $pm_order_id
 * @property string $to_user_id
 * @property integer $status
 * @property integer $created_at
 */
class PmOrderToButlerErrorLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_to_butler_error_log';
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
            [['pm_order_id', 'to_user_id'], 'required'],
            [['pm_order_id', 'status', 'created_at'], 'integer'],
            [['to_user_id'], 'string'],
            ['status', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_id' => 'Pm Order ID',
            'to_user_id' => 'To User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function writeLog($pmOrderId, $toUserIds, $status=0)
    {
        $model = new self();
        $model->pm_order_id = $pmOrderId;
        $model->to_user_id = $toUserIds;
        $model->status = $status;

        return $model->save();
    }

}
