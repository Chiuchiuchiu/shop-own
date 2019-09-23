<?php

namespace apps\www\module\prepayLottery\models;

use Yii;

/**
 * This is the model class for table "prepay_lottery_git".
 *
 * @property integer $id
 * @property string $name
 * @property integer $amount
 * @property integer $stock
 * @property integer $git_key
 * @property string $icon
 * @property integer $status
 */
class PrepayLotteryGift extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prepay_lottery_gift';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('eventDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'amount', 'stock', 'status'], 'required'],
            [['amount', 'stock', 'status','git_key'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['icon'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'amount' => 'Amount',
            'stock' => 'Stock',
            'status' => 'Status',
        ];
    }
}
