<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "repair_butler".
 *
 * @property integer $repair_id
 * @property integer $butler_id
 * @property integer $source
 * @property integer $created_at
 */
class RepairButler extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'repair_butler';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['repair_id', 'butler_id', 'source', 'created_at'], 'required'],
            [['repair_id', 'butler_id', 'source', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'repair_id' => 'Repair ID',
            'butler_id' => 'Butler ID',
            'source' => 'Source',
            'created_at' => 'Created At',
        ];
    }
}
