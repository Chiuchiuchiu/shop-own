<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_butler_manage_lists".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property integer $project_region_id
 * @property integer $house_parent_id
 * @property integer $butler_id
 * @property integer $status
 * @property string $project_name
 * @property string $butler_name
 * @property string $area_name
 * @property integer $bill_house_day_count
 * @property integer $bill_house_total_count
 * @property integer $auth_count
 * @property integer $auth_amount
 * @property string $bill_day_amount
 * @property string $bill_total_amount
 * @property integer $house_amount
 * @property integer $created_at
 *
 * @property PropertyManagementareaReportExt $propertyManagementareaReportExt
 */
class ProjectButlerManageLists extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_butler_manage_lists';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bill_house_day_count', 'bill_house_total_count', 'auth_count', 'auth_amount', 'house_amount', 'created_at'], 'integer'],
            [['bill_day_amount', 'bill_total_amount'], 'number'],
            [['project_name', 'butler_name'], 'string', 'max' => 50],
            [['area_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_name' => 'Project Name',
            'butler_name' => 'Butler Name',
            'area_name' => 'Area Name',
            'bill_house_day_count' => 'Bill House Day Count',
            'bill_house_total_count' => 'Bill House Total Count',
            'auth_count' => 'Auth Count',
            'auth_amount' => 'Auth Amount',
            'bill_day_amount' => 'Bill Day Amount',
            'bill_total_amount' => 'Bill Total Amount',
            'house_amount' => 'House Amount',
            'created_at' => 'Created At',
        ];
    }

    public function getPropertyManagementareaReportExt()
    {
        return $this->hasOne(PropertyManagementareaReportExt::className(), ['pbml_id' => 'id']);
    }

}
