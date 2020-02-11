<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "FreightTemplate".
 *
 * @property  $id
 * @property  $name
 * @property  $shop_id
 * @property  $first_num
 * @property  $first_amount
 * @property  $continue_num
 * @property  $continue_amount
 * @property  $transport
 * @property  $area_json
 * @property  $free_shipping
 * @property  $postage
 * @property  $area_postage_json
 * @property  $sort
 * @property  $status
 * @property  $created_at
 * @property  $update_at
 * @property  $deleted_at
 */
class FreightTemplate extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_DEFAULT = 0;

    //1包邮2不包邮
    const FREE_SHIPPING_1 = 1;
    const FREE_SHIPPING_2 = 2;

    //1全国配送2指定配送
    const TRANSPORT_ALL = 1;
    const TRANSPORT_ONLY = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'freight_template';
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
            [['name', 'shop_id', 'free_shipping', 'transport_type'], 'required'],
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
            'name' => '模板名',
            'shop_id' => "店铺ID",
            'address_ids' => '配送地区ID',
            'freight_json' => '配送明细json',
            'free_shipping' => '是否包邮',
            'transport' => '配送类型',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '添加时间',
            'update_at' => '更新时间',
            'deleted_at' => '删除时间',
        ];
    }

    public static function transportMap(){
        return [
            self::TRANSPORT_ALL => '全国',
            self::TRANSPORT_ONLY => '部分地区',
        ];
    }

    public static function freeMap(){
        return [
            self::FREE_SHIPPING_1 => '包邮',
            self::FREE_SHIPPING_2 => '不包邮',
        ];
    }
}