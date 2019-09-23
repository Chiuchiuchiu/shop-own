<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "visit_house_owner".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $house_id
 * @property integer $butler_id
 * @property integer $project_house_id
 * @property integer $project_region_id
 * @property integer $years
 * @property integer $quarter
 * @property string $quarterText
 * @property integer $ra_satisfaction
 * @property integer $cg_satisfaction
 * @property integer $bs_satisfaction
 * @property integer $sm_satisfaction
 * @property integer $pu_satisfaction
 * @property integer $satisfaction
 * @property integer $status
 * @property string $phone
 * @property string $content
 * @property integer $created_at
 *
 * @property Member $member
 * @property House $house
 * @property MemberHouse $memberHouse
 * @property Butler $butler
 * @property Project $project
 * @property ProjectRegion $projectRegion
 * @property string $projectRegionName
 */
class VisitHouseOwner extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_ACTIVE = 1;
    const QUARTER_FIRST = 1;
    const QUARTER_SECOND = 2;
    const QUARTER_THIRD = 3;
    const QUARTER_FOURTH = 4;
    const SATISFACTION_THIRD = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visit_house_owner';
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
            [['member_id', 'house_id', 'butler_id', 'project_house_id', 'satisfaction'], 'required'],
            [['member_id', 'house_id', 'butler_id', 'project_house_id', 'project_region_id', 'years', 'quarter', 'satisfaction', 'created_at'], 'integer'],
            [['phone'], 'string', 'max' => 20],
            [['content'], 'string', 'max' => 255],
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
            'house_id' => 'House ID',
            'butler_id' => 'Butler ID',
            'project_house_id' => 'Project House ID',
            'project_region_id' => 'Project Region ID',
            'quarter' => 'Quarter',
            'satisfaction' => 'Satisfaction',
            'phone' => 'Phone',
            'content' => 'Content',
            'created_at' => '提交时间',
            'ra_satisfaction' => '报事（度）',
            'cg_satisfaction' => '清洁绿化',
            'bs_satisfaction' => '管家服务',
            'sm_satisfaction' => '安全管理',
            'pu_satisfaction' => '公共设施',
        ];
    }

    public static function quarterMap()
    {
        return [
            1 => '第一季度',
            2 => '第二季度',
            3 => '第三季度',
            4 => '第四季度',
        ];
    }

    public function getQuarterText()
    {
        return self::quarterMap()[$this->quarter];
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }

    public function getMemberHouse()
    {
        return $this->hasOne(MemberHouse::className(), ['member_id' => 'member_id', 'house_id' => 'house_id']);
    }

    public function getButler()
    {
        return $this->hasOne(Butler::className(), ['id' => 'butler_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getProjectRegion()
    {
        return $this->hasOne(ProjectRegion::className(), ['id' => 'project_region_id']);
    }

    public function getProjectRegionName()
    {
        return $this->projectRegion instanceof ProjectRegion ? $this->projectRegion->name : '未设置';
    }

}
