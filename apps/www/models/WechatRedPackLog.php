<?php

namespace apps\www\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "wechat_red_pack_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $number
 * @property string $amount
 * @property string $result
 * @property integer $created_at
 */
class WechatRedPackLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_red_pack_log';
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
            [['member_id', 'number', 'created_at'], 'integer'],
            [['amount'], 'number'],
            [['result'], 'string'],
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
            'number' => 'Number',
            'amount' => 'Amount',
            'result' => 'Result',
            'created_at' => 'Created At',
        ];
    }

    public static function writeLog($memberId, $amount, $number, $result)
    {
        $model = new self();
        $model->member_id = $memberId;
        $model->number = $number;
        $model->amount = $amount;
        $model->result = serialize($result);

        return $model->save();
    }

}
