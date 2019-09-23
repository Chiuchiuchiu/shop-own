<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "property_managementarea_report_ext".
 *
 * @property integer $id
 * @property integer $pbml_id
 * @property integer $auth_house_day_count
 * @property integer $auth_parking_day_count
 * @property integer $auth_all_house_sum
 * @property integer $auth_all_parking_sum
 * @property string $bill_house_day_amount
 * @property string $bill_parking_day_amount
 * @property string $bill_all_house_amount
 * @property string $bill_all_parking_amount
 * @property integer $created_at
 */
class PropertyManagementareaReportExt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'property_managementarea_report_ext';
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
            [['pbml_id', 'auth_house_day_count', 'auth_parking_day_count', 'auth_all_house_sum', 'auth_all_parking_sum', 'created_at'], 'integer'],
            [['bill_house_day_amount', 'bill_parking_day_amount', 'bill_all_house_amount', 'bill_all_parking_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pbml_id' => 'Pbml ID',
            'auth_house_day_count' => 'Auth House Day Count',
            'auth_parking_day_count' => 'Auth Parking Day Count',
            'auth_all_house_sum' => 'Auth All House Sum',
            'auth_all_parking_sum' => 'Auth All Parking Sum',
            'bill_house_day_amount' => 'Bill House Day Amount',
            'bill_parking_day_amount' => 'Bill Parking Day Amount',
            'bill_all_house_amount' => 'Bill All House Amount',
            'bill_all_parking_amount' => 'Bill All Parking Amount',
            'created_at' => 'Created At',
        ];
    }
}
