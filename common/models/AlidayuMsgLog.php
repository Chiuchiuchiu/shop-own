<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "alidayu_msg_log".
 *
 * @property integer $id
 * @property string $phone
 * @property string $result
 * @property integer $status
 * @property integer $created_at
 *
 */
class AlidayuMsgLog extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_FALSE = 2;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ]
        ];
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
    public static function tableName()
    {
        return 'alidayu_msg_log';
    }

    public static function statusMap()
    {
        return [
            0 => '未知',
            self::STATUS_ACTIVE => '发送成功',
            self::STATUS_FALSE => '发送失败',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'result', 'phone'], 'required'],
            [['phone', 'result'], 'string'],
            ['status', 'default', 'value' => 0],
            [['status', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => '手机号',
            'result' => '返回结果',
            'status' => '状态',
            'statusText' => '状态',
            'created_at' => '创建时间',
        ];
    }
}
