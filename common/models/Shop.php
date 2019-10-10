<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "shop".
 *
 * @property integer inventory
 * @property integer $project_region_id
 * @property integer $project_fee_cycle_id
 * @property string $house_name
 * @property string $logo
 * @property string $icon
 * @property string $mp_qrcode
 * @property string $area
 * @property integer $pay_type
 * @property string $payTypeText
 * @property string $url_key
 * @property integer $status
 * @property integer $sync_count
 * @property integer $created_at
 *
 * @property string $statusText
 * @property string $mchId
 * @property ProjectHouseStructure $ProjectStructure
 * @property ProjectRegion $projectRegion
 * @property string $projectRegionName
 * @property ProjectPayConfig $projectPayConfig
 */
class Shop extends \yii\db\ActiveRecord
{

    const STATUS_WAIT = 1;
    const STATUS_TRIM = 2;
    const STATUS_ACTIVE = 3;
    const STATUS_CLOSE = 4;

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
            'platform_commission' => '每笔佣金（%）',
            'total_amount' => '总营业额',
            'amount_wait' => '待结算金额',
        ];
    }

    public function getProjectStructure()
    {
        return $this->hasOne(ProjectHouseStructure::className(), ['house_id' => ['project_house_id']]);
    }

}
