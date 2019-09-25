<?php

namespace apps\business\models;

use Yii;

/**
 * This is the model class for table "project_fee_cycle".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property integer $created_at
 */
class ProjectFeeCycle extends \yii\db\ActiveRecord
{
    const STATUS_ENABLE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_fee_cycle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
