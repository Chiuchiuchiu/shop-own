<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "wechat_invoice_card".
 *
 * @property integer $id
 * @property string $pm_order_number
 * @property string $card_id
 * @property string $status
 * @property integer $created_at
 *
 * @property string statusText
 */
class WechatInvoiceCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_invoice_card';
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
    public function rules()
    {
        return [
            [['card_id', 'status'], 'string'],
            [['created_at'], 'integer'],
            [['pm_order_number'], 'string', 'max' => 25],
        ];
    }

    public static function statusEnum()
    {
        return [
            1 => '收取授权事件',
            2 => '完成授权事件',
            3 => '其他',
        ];
    }

    public function getStatusText()
    {
        return self::statusEnum()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_number' => 'Pm Order Number',
            'card_id' => 'Card ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function findNumber($orderNumber)
    {
        $model = self::findOne(['pm_order_number' => $orderNumber]);
        if($model){
            $model = null;
            return false;
        } else {
            $model = new self();
            $model->pm_order_number = $orderNumber;
        }

        return $model->save();
    }

}
