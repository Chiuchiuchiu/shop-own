<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_discounts".
 *
 * @property integer $id
 * @property integer $pm_order_id
 * @property int $red_pack_status   0：未使用红包优惠；1：使用红包优惠
 * @property string $discounts_amount
 * @property integer $created_at
 */
class PmOrderDiscounts extends \yii\db\ActiveRecord
{
    const RED_PACK_STATUS_DEFAULT = 0;
    const RED_PACK_STATUS_USED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_discounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_id', 'created_at'], 'integer'],
            [['discounts_amount'], 'number'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_id' => 'Pm Order ID',
            'discounts_amount' => 'Discounts Amount',
            'created_at' => 'Created At',
        ];
    }

    public static function findOrCreate($pmOrderId, $amount, $redPackStatus)
    {
        if(empty($amount)){
            return false;
        }

        $model = self::findOne(['pm_order_id' => $pmOrderId]);
        if(!$model){
            $model = new self();
            $model->pm_order_id = $pmOrderId;
        }
        $model->discounts_amount = $amount;
        $model->red_pack_status = $redPackStatus ? 1 : 0;

        return $model->save();
    }

}
