<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "xg_request_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $created_at
 * @property string $title
 * @property string $type_code
 * @property integer $project_house_id
 * @property string $type
 * @property string $typeText
 *
 * @property Project $project
 * @property Member $member
 */
class XgRequestLog extends \yii\db\ActiveRecord
{
    const TYPE_Y = 'y';
    const TYPE_I = 'i';
    const TYPE_G = 'g';

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
        return 'xg_request_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'created_at', 'project_house_id'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['type'], 'string', 'max' => 10],
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type];
    }

    public static function typeMap()
    {
        return [
            '' => '为空',
            self::TYPE_G => '点击购买',
            self::TYPE_I => '点击产品图',
            self::TYPE_Y => '点击优惠券'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'member_id' => '用户ID',
            'created_at' => '请求时间',
            'title' => '产品名',
            'type_code' => '对应码',
            'project_house_id' => '项目',
            'type' => '点击来源',
            'typeText' => '点击来源',
        ];
    }

    public static function findOrCreate($memberId, $title, $typeCode, $projectHouseId=0, $type='y')
    {
        $model = new self();
        $model->member_id = $memberId;
        $model->title = $title;
        $model->type_code = $typeCode;
        $model->project_house_id = $projectHouseId;
        $model->type = $type;

        return $model->save();
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
