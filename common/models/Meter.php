<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "meter".
 *
 * @property integer $id
 * @property integer $project_id
 * @property integer $status
 * @property integer $created_at
 * @property string $upload_time
 * @property integer $meter_count
 */
class Meter extends \yii\db\ActiveRecord
{
    const STATUS_START = 2;     //抄表进行中
    const STATUS_COMPLETE = 3;  //完成抄表

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'status', 'created_at', 'meter_count'], 'integer'],
            [['upload_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'upload_time' => 'Upload Time',
            'meter_count' => 'Meter Count',
        ];
    }
}
