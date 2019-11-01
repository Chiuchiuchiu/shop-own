<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "goods".
 *
 * @property  $id
 * @property  $shop_id
 * @property  $member_id
 * @property  $order_number
 * @property  $goods_amount
 * @property  $discount_amount
 * @property  $express_amount
 * @property  $total_amount
 * @property  $status
 * @property  $buyer_remarks
 * @property  $seller_remarks
 * @property  $coupon_id
 * @property  $address_id
 * @property  $use_score
 * @property  $created_at
 * @property  $updated_at
 * @property  $paid_at
 * @property  $deliver_at
 * @property  $receiving_at
 * @property  $refund_at
 * @property  $finish_at
 * @property  $deleted_at
 * @property  $platform_amount
 * @property  $shop_amount
 * @property  $share_member_id
 * @property  $share_amount
 * @property  $is_imports
 * @property  $id_card
 *
 * @property Shop $shop
 * @property GoodsCategory $GoodsCategory
 */
class Order extends \yii\db\ActiveRecord
{
    const STATUS_WAIT_PAY = 1;  //待付款
    const STATUS_PAID = 2;      //已付款
    const STATUS_SHOP_SEND = 3; //已发货
    const STATUS_ARRIVE = 4;    //已收货，待评价
    const STATUS_COMPLETE = 5;  //已评价或默认好评
    const STATUS_DELETE = 6;    //删除
    const STATUS_REFUNDING = 7; //退款中
    const STATUS_REFUND = 8;    //退款完成
    const STATUS_CANCEL = 9;    //取消

    //是否跨境商品
    const IS_IMPORTS = 1;   //是
    const NOT_IMPORTS = 2;  //否

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shop_id', 'member_id', 'order_number','goods_amount','status','address_id'], 'required'],
            [['order_number'], 'unique'],
            [['shop_id','status','member_id','coupon_id','address_id','use_score','address_id','address_id','address_id'], 'integer'],
            [['goods_amount', 'discount_amount', 'express_amount', 'total_amount', 'platform_amount', 'shop_amount'], 'double'],
            [['seller_remarks', 'buyer_remarks'], 'string', 'max' => 200],
            ['status', 'default', 'value' => 0],
        ];
    }

    public static function statusMap(){
        return [
            0 => '未知',
            self::STATUS_WAIT_PAY => '待付款',
            self::STATUS_PAID => '已付款',
            self::STATUS_SHOP_SEND => '已发货',
            self::STATUS_ARRIVE => '已收货，待评价',
            self::STATUS_COMPLETE => '已评价', //或默认好评
            self::STATUS_DELETE => '删除',
            self::STATUS_REFUNDING => '退款中',
            self::STATUS_REFUND => '退款完成',
            self::STATUS_CANCEL => '取消',
        ];
    }

    public static function importsMap(){
        return [
            self::IS_IMPORTS => '是',
            self::NOT_IMPORTS => '否',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => '商铺id',
            'member_id' => '用户id',
            'order_number' => '订单号',
            'goods_amount' => '订单原价',
            'discount_amount' => '优惠金额',
            'express_amount' => '运费',
            'total_amount' => '订单总价',
            'status' => '状态',
            'statusText' => '状态',
            'buyer_remarks' => '买家留言',
            'seller_remarks' => '卖家留言',
            'coupon_id' => '优惠券id',
            'address_id' => '地址id', //关联member_address
            'use_score' => '使用积分',
            'created_at' => '下单时间',
            'updated_at' => '更新时间', //用于发货前的改动时间
            'paid_at' => '支付时间',
            'deliver_at' => '发货时间',
            'receiving_at' => '收货时间',
            'finish_at' => '完成时间',
            'refund_at' => '退款时间',
            'deleted_at' => '删除时间',
            'platform_amount' => '平台获得佣金',
            'shop_amount' => '商铺获得金额',
            'share_member_id' => '分享人id',
            'memberName' => '分享人',
            'share_amount' => '分享获得佣金',
            'is_imports' => '是否跨境',
            'id_card' => '买家身份证',
        ];
    }

    public function getMemberInfo(){
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getMemberName(){
        return $this->getMemberInfo()->name;
    }

    public function getGoodsCategory()
    {
        return $this->hasOne(GoodsCategory::className(), ['id' => 'category_id']);
    }

    public function getShop(){
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }
}
