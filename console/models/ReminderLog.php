<?php

namespace console\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "rush_fee_log".
 *
 * @property integer $id
 * @property integer $send_status
 * @property string $to_wechat_open_id
 * @property string $log_data
 * @property integer $updated_at
 * @property integer $created_at
 */
class ReminderLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reminder_log';
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
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'send_status'], 'integer'],
            [['log_data', 'to_wechat_open_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'send_status' => 'Send Status',
            'log_data' => 'Log Data',
            'updated_at' => 'Update At',
            'created_at' => 'Create At',
        ];
    }

    //插入日志数据
    public static function createSelfObj()
    {
        $res = new self();
        $res->created_at = time();

        return $res;
    }

    public static function writeLog($status, $data)
    {
        $model = new self();
        $model->send_status = $status;
        $model->log_data = serialize($data);

        return $model->save();
    }
}