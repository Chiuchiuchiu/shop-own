<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "activities_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $collect_status
 * @property string $collectStatusText
 * @property integer $collect_time
 * @property integer $project_house_id
 * @property string $nick_name
 * @property string $name
 * @property string $phone
 * @property integer $pay_time
 * @property integer $pay_status
 * @property string $payStatusText
 * @property integer $identification_status
 * @property string $identificationStatusText
 * @property integer $identification_time
 * @property string $headimg
 * @property integer $ac_order_id
 * @property integer $created_at
 *
 * @property Project $project
 * @property Member $member
 */
class ActivitiesLog extends \yii\db\ActiveRecord
{
    const IDENTIFICATION_STATUS_DEFAULT = 0;
    const IDENTIFICATION_STATUS_ACTIVATE = 1;
    const PAY_STATUS_WAITE = 0;
    const PAY_STATUS_ACTIVATE = 1;
    const COLLECT_STATUS_WAITE = 0;
    const COLLECT_STATUS_ACTIVATE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activities_log';
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

    public function getPayStatusText()
    {
        return self::payStatusMap()[$this->pay_status];
    }

    public static function payStatusMap()
    {
        return [
            self::PAY_STATUS_WAITE => '未缴费',
            self::PAY_STATUS_ACTIVATE => '已缴费',
        ];
    }

    public function getCollectStatusText()
    {
        return self::collectStatusMap()[$this->collect_status];
    }

    public static function collectStatusMap()
    {
        return [
            self::COLLECT_STATUS_WAITE => '未领取',
            self::COLLECT_STATUS_ACTIVATE => '已领取',
        ];
    }

    public function getIdentificationStatusText()
    {
        return self::identificationStatusMap()[$this->identification_status];
    }

    public static function identificationStatusMap()
    {
        return [
            self::IDENTIFICATION_STATUS_DEFAULT => '未认证',
            self::IDENTIFICATION_STATUS_ACTIVATE => '已认证',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'collect_status', 'collect_time', 'project_house_id', 'pay_time', 'pay_status', 'identification_status', 'identification_time', 'created_at'], 'integer'],
            [['nick_name'], 'string', 'max' => 50],
            [['name', 'phone'], 'string', 'max' => 20],
            [['headimg'], 'string', 'max' => 255],
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
            'collect_status' => 'Collect Status',
            'collectStatusText' => '领取状态',
            'collect_time' => '领取时间',
            'project_house_id' => '项目',
            'nick_name' => '昵称',
            'name' => '用户名',
            'phone' => '手机号',
            'pay_time' => '缴费时间',
            'pay_status' => '缴费状态',
            'payStatusText' => '缴费状态',
            'identification_status' => 'Identification Status',
            'identificationStatusText' => '认证状态',
            'identification_time' => '认证时间',
            'headimg' => 'Headimg',
            'ac_order_id' => '对应领取单ID',
            'created_at' => '访问时间',
        ];
    }

    /**
     * @param integer $memberId
     * @param string $phone
     * @param string $nickName
     * @param string $name
     * @param integer $projectHouseId
     * @return ActivitiesLog
     */
    public static function writeLog($memberId, $phone, $nickName, $name, $projectHouseId)
    {
        $model = new self();
        $model->member_id = $memberId;
        $model->phone = $phone;
        $model->nick_name = $nickName;
        $model->name = $name;
        $model->project_house_id = $projectHouseId;

        return $model;
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

}
