<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_house_post_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $project_id
 * @property string $raw
 * @property string $message
 * @property string $member_info
 * @property integer $created_at
 */
class MemberHousePostLog extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_house_post_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'project_id', 'raw', 'message'], 'required'],
            [['member_id', 'project_id', 'created_at'], 'integer'],
            [['raw', 'message', 'member_info'], 'string'],
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
            'project_id' => 'Project ID',
            'raw' => 'Raw',
            'message' => 'Message',
            'created_at' => 'Created At',
        ];
    }

    public static function log($member_id, $project_id, $raw, $message, $memberInfo=null)
    {
        return (new self(
            [
                'member_id' => $member_id,
                'project_id' => $project_id,
                'raw' => is_string($raw) ? $raw : serialize($raw),
                'message' => $message,
                'member_info' => empty($memberInfo) ? '' : serialize($memberInfo)
            ]
        ))->save();
    }
}
