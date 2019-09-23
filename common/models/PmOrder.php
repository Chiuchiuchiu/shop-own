<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order".
 *
 * @property integer $id
 * @property string $number
 * @property integer $mch_seq_no
 * @property integer $member_id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $house_type
 * @property string $total_amount
 * @property integer $pay_type
 * @property string $payTypeText
 * @property integer $status
 * @property string $statusText
 * @property integer $bill_type
 * @property string $billTypeText
 * @property string $chargeTypeText
 * @property string $charge_type
 * @property int $discount_status
 * @property integer $payed_at
 * @property integer $created_at
 * @property integer $refund_at
 *
 * @property array $items
 * @property House $house
 * @property PmOrderFpzz $pmOrderFpzz
 * @property Member $member
 * @property PmOrderDiscounts $pmOrderDiscounts
 * @property Project $project
 */
class PmOrder extends \yii\db\ActiveRecord
{
    const STATUS_READY=0;
    const STATUS_WAIT_PAY=1;
    const STATUS_PAYED=2;
    const STATUS_TEST_PAYED=3;
    const STATUS_REFUND=4;
    const PAY_TYPE_WECHAT=1;
    const PAY_TYPE_SUB_WECHAT_GDZAWY=2;
    const PAY_TYPE_SUB_SW_GDZAWY=3;
    const PAY_TYPE_SUB_MS_GDAZWY=5;
    const PAY_TYPE_MP = 4;
    const BILL_TYPE_ONE = 1;
    const BILL_TYPE_TOW = 2;
    const DISCOUNT_STATUS_DEFAULT = 0;
    const DISCOUNT_STATUS_ACTIVE = 1;
    const HOUSE_TYPE_HOUSE = 1;
    const HOUSE_TYPE_PARKING = 2;
    const CHARGE_TYPE_1 = 1;    //1常规费用
    const CHARGE_TYPE_2 = 2;    //2抄表费用
    const CHARGE_TYPE_3 = 3;    //3临时费用


    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'updatedAtAttribute'=>null
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'house_id', 'project_house_id','total_amount'], 'required'],
            [['member_id', 'house_id', 'project_house_id', 'pay_type', 'status', 'payed_at', 'created_at', 'charge_type'], 'integer'],
            ['payed_at','default','value'=>0],
            ['charge_type','default','value'=>1],
            [['total_amount'],'number'],
            ['status','default','value'=>self::STATUS_READY],
            ['discount_status','default','value'=>self::DISCOUNT_STATUS_DEFAULT],
            ['pay_type','default','value'=>1],
            ['number','default','value'=>self::createNumber()],
            [['number'], 'string', 'max' => 20],
            [['number'], 'unique'],
        ];
    }
    public static function createNumber(){
        //请求基因+时间基因
        $str = serialize($_SERVER);
        $str = crc32(md5($str)).'';
        //时间基因
        $t = str_replace('.','',microtime(true)).mt_rand(1,9);
        $str = strrev($t).$str;

        return substr($str,0,20);

    }

    public static function billType()
    {
        return [
            self::BILL_TYPE_ONE => '全额',
            self::BILL_TYPE_TOW => '减除滞纳金',
        ];
    }

    public function getBillTypeText()
    {
        return self::billType()[$this->bill_type];
    }

    public static function statusType()
    {
        return [
            self::STATUS_READY => '未支付',
            self::STATUS_WAIT_PAY => '等待支付',
            self::STATUS_PAYED => '已支付',
            self::STATUS_TEST_PAYED => '已支付',
            self::STATUS_REFUND => '已退款'
        ];
    }

    public function getStatusText(){
        return self::statusType()[$this->status];
    }

    public static function payTypeMap()
    {
        return [
            1 => '微信',
            self::PAY_TYPE_SUB_WECHAT_GDZAWY => '微信',
            self::PAY_TYPE_SUB_SW_GDZAWY => '招商（微信）',
            self::PAY_TYPE_MP => '招商小程序',
            self::PAY_TYPE_SUB_MS_GDAZWY => '民生（微信）',
        ];
    }

    public function getPayTypeText()
    {
        return self::payTypeMap()[$this->pay_type];
    }

    public static function chargeTypeMap()
    {
        return [
            self::CHARGE_TYPE_1 => "常规物业费用",
            self::CHARGE_TYPE_2 => "水电抄表费用",
            self::CHARGE_TYPE_3 => "临时维修费用",
        ];
    }

    public function getChargeTypeText()
    {
        return self::chargeTypeMap()[$this->pay_type];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '订单号',
            'member_id' => 'Member ID',
            'house_id' => 'House ID',
            'project_house_id' => 'Project House ID',
            'pay_type' => 'Pay Type',
            'payTypeText' => '支付渠道',
            'status' => 'Status',
            'bill_type' => '缴费范围',
            'billTypeText' => '缴费范围',
            'charge_type' => '缴费类型',
            'chargeTypeText' => '缴费类型',
            'payed_at' => '付款时间',
            'created_at' => '创建时间',
            'refund_at' => '退款时间',
        ];
    }

    public function getItems(){
        return $this->hasMany(PmOrderItem::className(),['pm_order_id'=>'id']);
    }
    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }

    public function getPmOrderFpzz()
    {
        return $this->hasOne(PmOrderFpzz::className(), ['pm_order_id' => 'id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getPmOrderDiscounts()
    {
        return $this->hasOne(PmOrderDiscounts::className(), ['pm_order_id' => 'id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }
}
