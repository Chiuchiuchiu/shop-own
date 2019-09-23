<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_refund".
 *
 * @property integer $id
 * @property integer $pm_order_id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $refund_number
 * @property integer $status
 * @property integer $amount
 * @property string $reason
 * @property integer $pay_type
 * @property string $ip
 * @property string $result
 * @property integer $updated_at
 * @property integer $created_at
 *
 * @property PmOrder $pmOrder
 */
class PmOrderRefund extends \yii\db\ActiveRecord
{
    const STATUS_READY = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAIL = 3;
    const STATUS_WAIT = 4;

    const PAY_TYPE_SUB_WECHAT_GDZAWY = 2;
    const PAY_TYPE_SUB_SW_GDZAWY = 3;
    const PAY_TYPE_MP = 4;
    const PAY_TYPE_SUB_MS_GDAZWY = 5;

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
        return 'pm_order_refund';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_id', 'reason'], 'required'],
            ['status', 'default', 'value'=>0],
            ['ip','default','value'=>'-'],
            ['pay_type','default','value'=> self::PAY_TYPE_SUB_MS_GDAZWY],
            ['refund_number','default','value'=>self::createNumber()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_id' => '订单号',
            'refund_number' => '退款单号',
            'status' => '状态',
            'statusText' => '状态',
            'amount' => '退款金额',
            'reason' => '退款原因',
            'payTypeText' => '支付渠道',
            'pay_type' => '支付渠道',
            'ip' => '操作IP',
            'result' => '民生返回代码',
            'updated_at' => '退款时间',
            'created_at' => '创建时间',
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

    public static function statusType()
    {
        return [
            self::STATUS_READY => '未退款',
            self::STATUS_SUCCESS => '退款成功',
            self::STATUS_FAIL => '退款失败',
            self::STATUS_WAIT => '退款中',    //原订单成功，交易结果未知
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

    public function getPmOrder(){
        return $this->hasOne(PmOrder::className(),['id'=>'pm_order_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }
}
