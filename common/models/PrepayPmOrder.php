<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order".
 *
 * @property integer $id
 * @property string $number
 * @property integer $member_id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $num
 * @property string $total_amount
 * @property string $balance
 * @property integer $pay_type
 * @property integer $status
 * @property integer $payed_at
 * @property integer $created_at
 *
 * @property House $house
 */
class PrepayPmOrder extends \yii\db\ActiveRecord
{
    const STATUS_READY=0;
    const STATUS_WAIT_PAY=1;
    const STATUS_PAYED=2;
    const STATUS_TEST_PAYED=3;
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
        return 'prepay_pm_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'house_id', 'project_house_id','total_amount','num'], 'required'],
            [['member_id', 'house_id', 'project_house_id', 'pay_type', 'status', 'payed_at', 'created_at','num'], 'integer'],
            ['payed_at','default','value'=>0],
            [['total_amount','balance'],'number'],
            ['status','default','value'=>self::STATUS_READY],
            ['pay_type','default','value'=>1],
            ['balance','default','value'=>'0'],
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
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'numero' => 'Numero',
            'member_id' => 'Member ID',
            'house_id' => 'House ID',
            'project_house_id' => 'Project House ID',
            'pay_type' => 'Pay Type',
            'num' => '预缴期数',
            'total_amount' => '总金额',
            'status' => 'Status',
            'statusText' => '状态',
            'payed_at' => '支付时间',
            'created_at' => 'Created At',
            'HouseFullName' => '全称房产',
        ];
    }
    public static function statusMap(){
        return [
            self::STATUS_TEST_PAYED=>'测试已支付',
            self::STATUS_PAYED=>'已支付',
            self::STATUS_READY=>'发起',
            self::STATUS_WAIT_PAY=>'等待支付'
        ];
    }
    public function getStatusText(){
        return self::statusMap()[$this->status];
    }
    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }
    public function getHouseFullName(){
        return $this->house->showName;
    }
}
