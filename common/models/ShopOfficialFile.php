<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "shop_official_file".
 *
 * @property integer $shop_id
 * @property integer $id_card_img
 * @property integer $license_img
 * @property integer $created_at
 *
 */
class ShopOfficialFile extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shop_official_file';
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
            [['shop_id', 'id_card_img', 'license_img'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shop_id' => '商铺ID',
            'id_card_img' => '身份证',
            'license_img' => '营业执照',
            'created_at' => '创建时间',
        ];
    }
}
