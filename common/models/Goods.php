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
 * @property  $goods_imgs
 * @property  $is_on_sale
 * @property  $sort_order
 * @property  $status
 * @property  $amount_wait
 * @property  $service_type
 * @property  $icon_name
 * @property  $created_at
 * @property  $deleted_at
 *
 * @property ProjectHouseStructure $ProjectStructure
 */
class Goods extends \yii\db\ActiveRecord
{
    const STATUS_WAIT   = 1;
    const STATUS_TRIM   = 2;
    const STATUS_ACTIVE = 3;
    const STATUS_CLOSE  = 4;

    const ON_SALE_1 = 1;
    const ON_SALE_2 = 2;

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
            [['name', 'logo', 'status','mobile','platform_commission','icon_name','service_type','inventory_type'], 'required'],
            [['category_id', 'status', ], 'integer'],
            [['email'], 'email'],
            [['description'], 'string', 'max' => 200],
//            [['url_key', 'house_name'], 'unique'],
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_WAIT => '审核中',
            self::STATUS_TRIM => '装修中',
            self::STATUS_ACTIVE => '营业中',
            self::STATUS_CLOSE => '已关闭',
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
            'logo' => 'logo',
            'inventory_type' => '减库存',
            'service_end_time' => '服务到期时间',
            'category_id' => '店铺类型',
            'categoryText' => '店铺类型',
            'mobile' => '联系电话',
            'email' => '联系邮箱',
            'description' => '店铺描述',
            'status' => '状态',
            'statusText' => '状态',
            'platform_commission' => '订单佣金（%）',
            'total_amount' => '总营业额（元）',
            'amount_wait' => '待结算金额（元）',
            'service_type' => '订单服务类型', //物流订单，到店服务，上门服务
            'icon_name' => 'icon名称', //平台首页展示的icon
            'created_at' => '创建时间',
            'deleted_at' => '关闭时间',
        ];
    }

    public function getShopCategory()
    {
        return $this->hasOne(ShopCategory::className(), ['id' => 'category_id']);
    }

}
