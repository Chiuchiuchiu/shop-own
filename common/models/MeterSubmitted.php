<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "meter_submitted".
 *
 * @property integer $id
 * @property string $uid
 * @property integer $project_id
 * @property string $title
 * @property integer $meter_count
 * @property string $stra_date
 * @property string $end_date
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 */
class MeterSubmitted extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meter_submitted';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'meter_count'], 'integer'],
            [['stra_date', 'stra_int', 'deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['uid', 'title'], 'string', 'max' => 250],
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
            'project_id' => 'Project ID',
            'title' => '说明',
            'meter_count' => '上传数据总数',
            'stra_date' => '上传日期',
            'stra_int' => '上传时间',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
