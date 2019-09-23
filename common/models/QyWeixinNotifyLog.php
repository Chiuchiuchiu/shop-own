<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "qy_weixin_notify_log".
 *
 * @property integer $id
 * @property string $send_data
 * @property string $result
 * @property string $ip
 * @property integer $created_at
 */
class QyWeixinNotifyLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qy_weixin_notify_log';
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
                'updatedAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_data', 'result'], 'string'],
            [['created_at'], 'integer'],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'send_data' => 'Send Data',
            'result' => 'Result',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }
}
