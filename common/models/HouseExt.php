<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "house_ext".
 *
 * @property integer $house_id
 * @property integer $customer_id
 * @property integer $birth_day
 * @property string $charge_area
 * @property string $id_number
 * @property string $hurry_phone
 * @property string $link_man
 * @property string $customer_name
 * @property string $mobile_phone
 * @property integer $updated_at
 */
class HouseExt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house_ext';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['house_id'], 'required'],
            [['house_id', 'customer_id', 'birth_day', 'updated_at'], 'integer'],
            [['charge_area'], 'number'],
            [['id_number'], 'string', 'max' => 200],
            [['hurry_phone'], 'string', 'max' => 200],
            [['link_man'], 'string', 'max' => 50],
            [['customer_name'], 'string', 'max' => 100],
            [['mobile_phone'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'house_id' => 'House ID',
            'customer_id' => 'Customer ID',
            'birth_day' => 'Birth Day',
            'charge_area' => 'Charge Area',
            'id_number' => 'Id Number',
            'hurry_phone' => 'Hurry Phone',
            'link_man' => 'Link Man',
            'customer_name' => 'Customer Name',
            'mobile_phone' => 'Mobile Phone',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findOrCreate($id)
    {
        $self = self::find()->where(['house_id' => $id])->one();
        if (!$self) {
            $self = new self();
            $self->house_id = $id;
        }
        return $self;
    }
}
