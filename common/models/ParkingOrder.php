<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "parking_order".
 *
 * @property integer $id
 * @property integer $project_house_id
 * @property integer $member_id
 * @property string $number
 * @property string $plate_number
 * @property string $calc_id
 * @property integer $status
 * @property integer $type  1：临卡；2：月卡
 * @property integer $pay_type
 * @property integer $parking_type
 * @property string $amount
 * @property string $receivable
 * @property string $disc
 * @property integer $quantity
 * @property string $transaction_id
 * @property integer $expire_date
 * @property integer $effect_date
 * @property integer $m_id
 * @property integer $payed_at
 * @property integer $updated_at
 * @property integer $created_at
 * @property integer $send_at
 * @property integer $send_status
 *
 *
 *
 * @property string $typeText
 * @property string $statusText
 * @property string $parkingTypeText
 * @property string $effectDate
 * @property Member $member
 * @property ProjectParkingOneToOne $projectParkingOneToOne
 * @property Project $project
 */
class ParkingOrder extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_WAIT = 1;
    const STATUS_PAYED = 2;
    const STATUS_TEST_PAYED = 3;
    const STATUS_REFUND = 4;
    const TYPE_T = 1;
    const TYPE_M = 2;
    const PAY_TYPE = 1;
    const PARKING_TYPE_G = 1;
    const PARKING_TYPE_I = 2;
    const GEN_PAYMENT_WE = '120901';  //微信
    const GEN_PAYMENT_ALI = '121001';  //支付宝
    const SEND_STATUS_WAIT = 1;
    const SEND_STATUS_DONE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parking_order';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_house_id', 'member_id', 'amount', 'plate_number', 'calc_id'], 'required'],
            [['project_house_id', 'member_id', 'status', 'type', 'pay_type', 'created_at', 'payed_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            ['status', 'default', 'value' => self::STATUS_DEFAULT],
            ['pay_type', 'default', 'value' => self::PAY_TYPE],
            ['number', 'default', 'value' => self::createNumber()],
            [['number', 'calc_id'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'project_house_id' => '项目',
            'member_id' => '用户',
            'number' => '订单号',
            'plate_number' => '车牌号',
            'calc_id' => '第三方收费记录ID',
            'status' => 'Status',
            'type' => '临卡/月卡',
            'pay_type' => '支付类型',
            'parking_type' => '道闸商',
            'parkingTypeText' => '道闸商',
            'created_at' => '创建时间',
            'payed_at' => '支付时间',
            'updated_at' => 'Updated At',
            'send_at' => '通知道闸时间',
            'amount' => 'Amount',
            'receivable' => '应收',
            'disc' => '折扣',
            'quantity' => '数量',
            'transaction_id' => '微信支付订单号',
            'effect_date' => '缴费开始日期',
            'expire_date' => '缴费结束日期',
        ];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_DEFAULT => '默认',
            self::STATUS_WAIT => '等待核销',
            self::STATUS_PAYED => '已支付',
            self::STATUS_TEST_PAYED => '已支付',
            self::STATUS_REFUND => '已退款',
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    public static function parkingTypeMap()
    {
        return [
            self::PARKING_TYPE_G => '金溢科技',
            self::PARKING_TYPE_I => '艾润',
        ];
    }

    public function getParkingTypeText()
    {
        return self::parkingTypeMap()[$this->parking_type];
    }

    public static function typeMap()
    {
        return [
            self::TYPE_T => '临卡',
            self::TYPE_M => '月卡',
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type];
    }

    public static function payTypeMap()
    {
        return [
            self::PAY_TYPE => '微信',
        ];
    }

    public static function createNumber()
    {
        $str = serialize($_SERVER);
        $str = str_shuffle(sha1($str));
        $str = crc32($str);
        $times = str_replace('.', '', microtime(1));
        $number = $times . $str;

        return $number;
    }

    public function getEffectDate()
    {
        $dataTime = '-';
        if($this->effect_date > 0){
            $dataTime = date('Y-m-d', $this->effect_date);
        }

        return $dataTime;
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getProjectParkingOneToOne()
    {
        return $this->hasOne(ProjectParkingOneToOne::className(), ['project_house_id' => 'project_house_id']);
    }

}
