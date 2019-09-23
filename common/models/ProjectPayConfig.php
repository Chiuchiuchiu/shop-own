<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project_pay_config".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property string $key
 * @property string $mch_id
 * @property integer $created_at
 */
class ProjectPayConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_pay_config';
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
            [['project_house_id', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 255],
            [['mch_id'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_house_id' => 'Project House ID',
            'key' => 'Key',
            'mch_id' => 'Mch ID',
            'created_at' => 'Created At',
        ];
    }
}
