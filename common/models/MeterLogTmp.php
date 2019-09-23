<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "meter_log_tmp".
 *
 * @property integer $id
 * @property string $last_meter_data
 * @property integer $last_meter_time
 * @property integer $project_id
 * @property integer $meter_id
 * @property integer $status
 */
class MeterLogTmp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meter_log_tmp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_meter_data'], 'number'],
            [['last_meter_time', 'project_id', 'meter_id', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_meter_data' => 'Last Meter Data',
            'last_meter_time' => 'Last Meter Time',
            'project_id' => 'Project ID',
            'meter_id' => 'Meter ID',
            'status' => 'Status',
        ];
    }
}
