<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "goods".
 *
 * @property  $id
 * @property  $name
 * @property  $shop_id
 * @property  $category_id
 * @property  $goods_sn
 * @property  $brand_id
 * @property  $goods_number
 * @property  $keywords
 * @property  $goods_brief
 * @property  $goods_detail
 * @property  $primary_pic_url
 * @property  $list_pic_url
 * @property  $sort_order
 * @property  $status
 * @property  $extra_price
 * @property  $counter_price
 * @property  $retail_price
 * @property  $unit_price
 * @property  $platform_commission
 * @property  $sell_volume
 * @property  $primary_product_id
 * @property  $promotion_tag
 * @property  $is_new
 * @property  $is_limited
 * @property  $is_on_sale
 * @property  $is_hot
 * @property  $created_at
 * @property  $updated_at
 *
 * @property Shop $shop
 * @property GoodsCategory $GoodsCategory
 */
class Goods extends \yii\db\ActiveRecord
{
    //1已上架2商家下架3商家准备中4商家删除5平台下架6平台删除
    const STATUS_SHOP_ACTIVE = 1;
    const STATUS_SHOP_SHELF = 2;
    const STATUS_SHOP_WAIT = 3;
    const STATUS_SHOP_DELETE = 4;
    const STATUS_ADMIN_SHELF = 5;
    const STATUS_ADMIN_DELETE = 6;

    //上新，1是2否
    const IS_NEW_1 = 1;
    const IS_NEW_2 = 2;

    //限购，1是2否
    const IS_LIMIT_1 = 1;
    const IS_LIMIT_2 = 2;

    //特价，1是2否
    const IS_ON_SALE_1 = 1;
    const IS_ON_SALE_2 = 2;

    //热门，1是2否
    const IS_HOT_1 = 1;
    const IS_HOT_2 = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
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
            [['name', 'category_id', 'shop_id','goods_sn','status','goods_unit','primary_pic_url','unit_price','primary_product_id'], 'required'],
            [['category_id','status','shop_id','sell_volume'], 'integer'],
            [['goods_brief'], 'string', 'max' => 200],
        ];
    }

    public static function statusMap(){
        return [
            0 => '未知',
            self::STATUS_SHOP_ACTIVE => '已上架',
            self::STATUS_SHOP_SHELF => '商家下架',
            self::STATUS_SHOP_WAIT => '商家准备中',
            self::STATUS_SHOP_DELETE => '商家删除',
            self::STATUS_ADMIN_SHELF => '平台下架',
            self::STATUS_ADMIN_DELETE => '平台删除',
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
            'name' => '商铺名',
            'shop_id' => '商铺id',
            'category_id' => '分类id',
            'goods_sn' => '商品属性',
            'status' => '状态',
            'statusText' => '状态',
            'brand_id' => '生产商id',
            'goods_number' => '商品码',
            'keywords' => '关键字',
            'goods_brief' => '商品描述',
            'goods_detail' => '商品详情（图片）',
            'sort_order' => '排序',
            'goods_unit' => '单位',
            'primary_pic_url' => '商品主图',
            'list_pic_url' => '商品轮播图',
            'extra_price' => '附加价格',
            'counter_price' => '专柜价格',
            'unit_price' => '单价',
            'sell_volume' => '销售量',
            'primary_product_id' => 'skuId',
            'promotion_tag' => '标签',
            'platform_commission' => '商品佣金',
            'is_new' => '是否上新',
            'is_limited' => '是否限定',
            'is_on_sale' => '是否促销',
            'is_hot' => '是否热门',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getGoodsCategory()
    {
        return $this->hasOne(GoodsCategory::className(), ['id' => 'category_id']);
    }

    public function getShop(){
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }
}
