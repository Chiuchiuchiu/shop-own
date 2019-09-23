<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_parking_one_to_one".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property int $type
 * @property string $app_id
 * @property string $app_key
 * @property string $parking_id
 * @property integer $created_at
 *
 * @property Project $project
 * @property string $parkingUrl
 */
class ProjectParkingOneToOne extends \yii\db\ActiveRecord
{
    const TYPE_G = 1;
    const TYPE_I = 2;
    const TYPE_U = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_parking_one_to_one';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_house_id', 'app_id', 'app_key', 'parking_id'], 'required'],
            [['project_house_id'], 'unique'],
            [['project_house_id', 'created_at'], 'integer'],
            [['app_id', 'app_key', 'parking_id'], 'string', 'max' => 32],
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

    public static function typeMap()
    {
        return [
            self::TYPE_G => '金溢科技',
            self::TYPE_I => '艾润',
            self::TYPE_U => '优泊到家',
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type];
    }

    public static function parkingUrl()
    {
        return [
            self::TYPE_G => '/parking?',
            self::TYPE_I => '/parking/irain-index?',
            self::TYPE_U => '/parking/irain-index?',
        ];
    }

    public function getParkingUrl()
    {
        return self::parkingUrl()[$this->type];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_house_id' => 'Project House ID',
            'name' => '名称',
            'icon' => '图标',
            'type' => '分类',
            'typeText' => '道闸厂商',
            'parkingUrl' => '地址',
            'app_id' => 'App ID',
            'app_key' => 'App Key',
            'parking_id' => 'Parking ID',
            'created_at' => '创建时间',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

}
