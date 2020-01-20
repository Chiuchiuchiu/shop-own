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
class Express extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'express';
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
            [['name', 'code'], 'required'],
            [['sort'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商铺名',
            'code' => "缩写",
            'sort' => '排序',
        ];
    }
}
