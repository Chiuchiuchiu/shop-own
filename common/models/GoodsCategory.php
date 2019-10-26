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
 * @property ShopCategory $ShopCategory
 */
class GoodsCategory extends \yii\db\ActiveRecord
{

    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
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
            [['name', 'status'], 'required'],
            [['status', 'created_at'], 'integer'],
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_DELETE => '删除',
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
            'name' => '商品分类名',
            'sort' => '排序',
            'status' => '状态',
            'extra' => '扩展',
            'statusText' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
