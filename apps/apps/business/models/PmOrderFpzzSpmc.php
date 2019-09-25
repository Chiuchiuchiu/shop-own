<?php

namespace apps\business\models;

use Yii;

/**
 * This is the model class for table "pm_order_fpzz_spmc".
 *
 * @property integer $id
 * @property string $spmc
 * @property string $as_spmc
 */
class PmOrderFpzzSpmc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_fpzz_spmc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spmc', 'as_spmc'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spmc' => 'Spmc',
            'as_spmc' => 'As Spmc',
        ];
    }
}
