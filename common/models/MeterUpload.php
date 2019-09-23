<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "meter_upload".
 *
 * @property integer $id
 * @property string $uid
 * @property string $pic
 * @property integer $meter_id
 * @property integer $meter_house_id
 * @property integer $project_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class MeterUpload extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meter_upload';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'pic'], 'required'],
            [['meter_id', 'meter_house_id', 'status', 'created_at', 'updated_at', 'project_id'], 'integer'],
            [['uid'], 'string', 'max' => 100],
            [['pic'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'pic' => 'Pic',
            'meter_id' => 'Meter ID',
            'meter_house_id' => 'Meter House ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
