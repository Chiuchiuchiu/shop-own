<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "shop".
 *
 * @property integer inventory
 * @property integer $id
 * @property integer $name
 * @property integer $logo
 * @property integer $inventory_type
 * @property integer $service_end_time
 * @property integer $category_id
 * @property integer $categoryText
 * @property integer $mobile
 * @property integer $email
 * @property integer $description
 * @property integer $status
 * @property integer $statusText
 * @property integer $platform_commission
 * @property integer $total_amount
 * @property integer $amount_wait
 * @property integer $service_type
 * @property integer $icon_name
 * @property integer $created_at
 * @property integer $deleted_at
 *
 * @property ProjectHouseStructure $ProjectStructure
 */
class Shop extends \yii\db\ActiveRecord
{
    const STATUS_WAIT   = 1;
    const STATUS_TRIM   = 2;
    const STATUS_ACTIVE = 3;
    const STATUS_CLOSE  = 4;

    const INVENTORY_1 = 1;
    const INVENTORY_2 = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop';
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
            [['id', 'name', 'logo', 'status','mobile','email','platform_commission'], 'required'],
            [['category_id', 'status', 'status', 'sync_count', 'created_at'], 'integer'],
//            [['house_name'], 'string', 'max' => 64],
//            [['url_key'], 'string', 'max' => 16],
//            [['area'], 'string', 'max' => 30],
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

    public static function inventoryMap(){
        return [
            self::INVENTORY_1 => '拍下减库存',
            self::INVENTORY_2 => '付款减库存',
        ];
    }

    public function getInventoryText(){
        return self::statusMap()[$this->inventory];
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
            'platform_commission' => '每笔佣金',
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
