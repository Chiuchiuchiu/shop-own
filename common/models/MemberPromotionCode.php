<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_promotion_code".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $house_id
 * @property string $amount
 * @property string $promotion_name
 * @property string $promotion_code
 * @property string $xg_product_code
 * @property int $status
 * @property integer $created_at
 *
 * @property House $house
 */
class MemberPromotionCode extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_USED = 1;

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
    public static function tableName()
    {
        return 'member_promotion_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['amount'],'number'],
            [['member_id', 'created_at'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_DEFAULT],
            [['promotion_code', 'xg_product_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'house_id' => 'House ID',
            'promotion_code' => 'Promotion Code',
            'xg_product_code' => 'Xg Product Code',
            'created_at' => 'Created At',
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_DEFAULT=>'未使用',
            self::STATUS_USED=>'已使用'
        ];
    }

    /**
     * @param integer $memberId
     * @param string $xgProductCode
     * @param bool $create
     * @return MemberPromotionCode|null|static
     */
    public static function findOrCreate($memberId, $xgProductCode, $create=false)
    {
        $model = self::findOne(['member_id' => $memberId, 'xg_product_code' => $xgProductCode]);
        if($create){
            if(!$model){
                $model = new self();
                $model->member_id = $memberId;
                $model->xg_product_code = $xgProductCode;
            }
        }

        return $model;
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }

}
