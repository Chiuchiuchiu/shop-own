<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "activities_collect_order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $member_house_id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $status
 * @property string $statusText
 * @property string $tel
 * @property string $user_name
 * @property string $comment
 * @property integer $created_at
 *
 * @property House $userHouse
 * @property House $house
 * @property Member $member
 * @property ActivitiesLog $activitiesLog
 */
class ActivitiesCollectOrder extends \yii\db\ActiveRecord
{
    const STATUS_WAITE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activities_collect_order';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ]
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_WAITE => '未领取',
            self::STATUS_ACTIVE => '已领取',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'member_house_id', 'tel', 'user_name'], 'required'],
            [['member_id', 'member_house_id', 'house_id', 'project_house_id', 'status', 'created_at'], 'integer'],
            [['tel', 'user_name', 'comment'], 'trim'],
            [['tel', 'user_name'], 'string', 'max' => 20],
            [['comment'], 'string', 'max' => 255],
            [['house_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'member_id' => 'Member ID',
            'member_house_id' => '用户收货地址',
            'house_id' => 'House ID',
            'project_house_id' => '项目',
            'status' => 'Status',
            'statusText' => '领取状态',
            'tel' => '手机号',
            'user_name' => '收货人',
            'comment' => '备注',
            'created_at' => '领取时间',
        ];
    }

    public static function findOrCreate($memberId)
    {
        $model = self::findOne(['member_id' => $memberId]);
        if (!$model){
            $model = new self();
            $model->member_id = $memberId;
        }
        $model->status = self::STATUS_ACTIVE;

        return $model;
    }

    public function getUserHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'member_house_id']);
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getActivitiesLog()
    {
        return $this->hasOne(ActivitiesLog::className(), ['ac_order_id' => 'id']);
    }

}