<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "repair_cancel".
 *
 * @property integer $id
 * @property integer $repair_id
 * @property string $type
 * @property string $typeText
 * @property string $content
 * @property integer $created_at
 */
class RepairCancel extends \yii\db\ActiveRecord
{
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
        return 'repair_cancel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repair_id', 'type'], 'required'],
            [['repair_id', 'created_at'], 'integer'],
            [['content'], 'filter', 'filter' => 'trim'],
            [['type', 'content'], 'string', 'max' => 255],
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type];
    }

    public static function typeMap()
    {
        return [
            0 => '速度太慢了，不想等了',
            1 => '我自己修好了',
            2 => '我临时有事，改天再说',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'repair_id' => 'Repair ID',
            'type' => 'Type',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }
}
