<?php

namespace common\models;

use apps\mgt\models\PmOrderFpzzResult;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fpzz_feedback".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $fpzz_result_id
 * @property integer $status
 * @property string $ip
 * @property integer $created_at
 *
 * @property Member $member
 * @property PmOrderFpzzResult $pmOrderFpzzResult
 */
class FpzzFeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fpzz_feedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'fpzz_result_id', 'status', 'created_at'], 'integer'],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ],
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
            'fpzz_result_id' => 'Fpzz_Result_ID',
            'status' => '状态',
            'ip' => 'Ip 地址',
            'created_at' => '反馈时间',
        ];
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getPmOrderFpzzResult()
    {
        return $this->hasOne(PmOrderFpzzResult::className(), ['id' => 'fpzz_result_id']);
    }

    public static function writeIn($memberId, $FpResultId, $ip)
    {
        $model = self::findOne(['fpzz_result_id' => $FpResultId]);
        if(!$model){
            $model = new self();
            $model->fpzz_result_id = $FpResultId;
        }

        $model->member_id = $memberId;
        $model->ip = $ip;

        return $model->save();
    }

}
