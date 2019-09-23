<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "mobile_order".
 *
 * @property integer $id
 * @property string $number
 * @property integer $member_id
 * @property string $mobile
 * @property integer $recharge_type
 * @property string $amount
 * @property integer $pay_type
 * @property integer $status
 * @property integer $payed_at
 * @property integer $created_at
 * @property integer $send_status
 * @property integer $send_at
 *
 * @property string $statusText
 */
class MobileOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mobile_order';
    }

    const STATUS_READY=0;
    const STATUS_WAIT_PAY=1;
    const STATUS_PAYED=2;
    const STATUS_TEST_PAYED=3;

    const PAY_TYPE_DEFAULT = 1;
    const PAY_TYPE_MP = 2;

    const RECHARGE_TYPE_DATA=2;
    const RECHARGE_TYPE_DEPOSIT=1;

    const SEND_STATUS_DONE=2;
    const SEND_STATUS_WAIT=1;
    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'updatedAtAttribute'=>null
            ]
        ];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_READY => '初始',
            self::STATUS_WAIT_PAY => '待付款',
            self::STATUS_PAYED => '已支付',
            self::STATUS_TEST_PAYED => '已支付',
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function payTypeMap()
    {
        return [
            self::PAY_TYPE_DEFAULT => '公众号',
            self::PAY_TYPE_MP => '微信小程序',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'mobile', 'amount'], 'required'],
            [['member_id', 'recharge_type', 'pay_type', 'status', 'payed_at', 'created_at','send_at'], 'integer'],
            [['amount'], 'number'],
            ['status','default','value'=>self::STATUS_READY],
            ['pay_type','default','value'=>1],
            ['number','default','value'=>self::createNumber()],
            ['send_at','default','value'=>0],
            ['send_status','default','value'=>0],
            ['payed_at','default','value'=>0],
            [['number', 'mobile'], 'string', 'max' => 20],
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'member_id' => 'Member ID',
            'mobile' => 'Mobile',
            'recharge_type' => 'Recharge Type',
            'amount' => 'Amount',
            'pay_type' => 'Pay Type',
            'status' => 'Status',
            'payed_at' => 'Payed At',
            'created_at' => 'Created At',
        ];
    }
}
