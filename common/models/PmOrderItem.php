<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pm_order_item".
 *
 * @property integer $id
 * @property integer $pm_order_id
 * @property integer $charge_item_id
 * @property string $charge_item_name
 * @property string $contract_no
 * @property string $charge_detail_id_list
 * @property string $amount
 * @property string $bill_date
 * @property string $status
 * @property string $statusText
 * @property string $bill_content
 * @property string $bankBillNo
 * @property integer $completed_at
 * @property integer $m_id
 * @property string $price
 * @property string $usage_amount
 * @property string $customer_name
 * @property string $second_updated_at
 *
 * @property PmOrder $pmOrder
 */
class PmOrderItem extends \yii\db\ActiveRecord
{
    const STATUS_WAIT='0000';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_id', 'charge_item_name', 'contract_no', 'bill_date','bill_content','charge_item_id'], 'required'],
            [['pm_order_id', 'completed_at', 'second_updated_at', 'charge_item_id', 'm_id'], 'integer'],
            ['completed_at','default','value'=>0],
            ['second_updated_at','default','value'=>0],
            ['status','default','value'=>'0000'],
            [['charge_item_name'], 'string', 'max' => 32],
            [['contract_no'], 'string', 'max' => 64],
            [['bill_date'], 'string', 'max' => 20],
            [['status'], 'string', 'max' => 10],
            [['bankBillNo'], 'string', 'max' => 32],
            [['bill_content'], 'string'],
        ];
    }
    public static function statusMap(){
        return [
            '0000'=>'未核销',
            '1000'=>'已核销',
            '2000'=>'已退款',
            '2001'=>'已缴款',
            '2003'=>'合同号不存在',
            '2005' => '数据库语句执行错误',
            '2006' => '已经缴款',
            '110' => ''
        ];
    }
    public function getStatusText(){
        return isset(self::statusMap()[$this->status]) ? self::statusMap()[$this->status] : $this->status;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_id' => 'Pm Order ID',
            'charge_item_name' => '类目',
            'contract_no' => '合同号',
            'charge_detail_id_list' => '合同号对应的应收款ID',
            'bill_date' => '账单时间',
            'status' => 'Status',
            'statusText' => '状态',
            'completed_at' => '核销时间',
            'bankBillNo' => 'bankBillNo',
            'houseFullName' => '房地全称',
            'amount' => '金额',
            'm_id' => '管理员',
            'price' => '单价',
            'usage_amount' => '面积/用量',
            'customer_name' => '收费对象',
            'second_updated_at' => '二次核销时间',
        ];
    }

    public function getPmOrder(){
        return $this->hasOne(PmOrder::className(),['id'=>'pm_order_id']);
    }

    public function getHouseFullName(){
        return $this->pmOrder->house->ancestor_name;
    }
}
