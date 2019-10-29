<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $name
 * @property integer $shop_id
 * @property integer $keywords
 * @property integer $parent_id
 * @property integer $sort
 * @property integer $banner_url
 * @property integer $icon_url
 * @property integer $img_url
 * @property integer $wap_banner_url
 * @property integer $level
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
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
            [['name','status','shop_id','level'], 'required'],
            [['status','parent_id','sort','shop_id','level'], 'integer'],
            [['banner_url','icon_url','img_url','wap_banner_url'], 'string', 'max' => 255],
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_DELETE => '隐藏',
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
            'name' => '分类名',
            'shop_id' => '所属商铺',
            'keywords' => '描述',
            'parent_id' => '夫id',
            'sort' => '排序',
            'banner_url' => '顶级分类banner',
            'icon_url' => '左侧分类icon',
            'img_url' => '左侧分类图片',
            'wap_banner_url' => '右侧分类图片',
            'level' => 'level',
            'status' => '状态',
            'statusText' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getShop(){
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }

    public function getParent(){
        return $this->hasOne(GoodsCategory::className(), ['id' => 'parent_id']);
    }
}
