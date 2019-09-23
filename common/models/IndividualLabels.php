<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "individual_labels".
 *
 * @property integer $id
 * @property integer $group
 * @property integer $status
 * @property string $name
 * @property string $class
 * @property integer $created_at
 */
class IndividualLabels extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;

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
    public static function tableName()
    {
        return 'individual_labels';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['group', 'status', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['class'], 'string', 'max' => 20],
            [['name', 'class'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'group' => '用户组',
            'status' => '状态',
            'name' => '名称',
            'class' => '样式',
            'created_at' => 'Created At',
        ];
    }
}
