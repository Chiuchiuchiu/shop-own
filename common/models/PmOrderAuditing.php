<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table "pm_order_auditing".
 *
 * @property integer $id
 * @property string $date
 * @property integer $status
 * @property string $pm_order_ids
 * @property integer $created_at
 * @property integer $updated_at
 *
 *
 * @property integer $totalMoney
 * @property Query $pmOrderItem
 *
 * @property string $statusText
 */
class PmOrderAuditing extends \yii\db\ActiveRecord
{
    const STATUS_WAIT = 1;
    const STATUS_AUTH = 2;
    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className()]
        ];
    }

    public static function statusMap()
    {
        return [
            0 => '作废',
            1 => '未审核',
            2 => '已审核'
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public function getTotalMoney(){
        return PmOrder::find()->where(['id'=>explode(',',$this->pm_order_ids)])->sum('total_amount');
    }
    public function getPmOrderItem(){
        return PmOrderItem::find()
            ->where(['pm_order_id'=>explode(',',$this->pm_order_ids)])->all();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_auditing';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'pm_order_ids'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['pm_order_ids'], 'string'],
            [['status'], 'default', 'value' => 1],
            [['date'], 'string', 'max' => 8],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => '账单日期',
            'status' => 'Status',
            'statusText' => '状态',
            'totalMoney' => '总金额',
            'pm_order_ids' => 'Pm Order Ids',
            'created_at' => 'Created At',
            'updated_at' => '最后编辑时间',
        ];
    }
}
