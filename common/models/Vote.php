<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "vote".
 *
 * @property integer $id
 * @property int $status
 * @property string $name
 * @property string $class
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $created_at
 */
class Vote extends \yii\db\ActiveRecord
{
    const SCENARIO_DEFAULT = 0;
    const STATUS_ACTIVE = 1;

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
    public static function tableName()
    {
        return 'vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'start_time', 'end_time'], 'required'],
            [['status', 'start_time', 'end_time', 'created_at'], 'integer'],
            [['name', 'class'], 'string', 'max' => 20],
            [['name', 'class'], 'filter', 'filter' => 'trim'],
        ];
    }

    public function statusTest()
    {
        return self::statusMap()[$this->status];
    }

    public static function statusMap()
    {
        return [
            self::SCENARIO_DEFAULT => '禁止',
            self::STATUS_ACTIVE => '正常',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'status' => '状态',
            'name' => '名称',
            'class' => '前端样式名',
            'start_time' => '活动开始时间',
            'end_time' => '活动结束时间',
            'created_at' => 'Created At',
        ];
    }
}
