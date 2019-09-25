<?php

namespace apps\www\models;

use Yii;

/**
 * This is the model class for table "member_ext".
 *
 * @property integer $member_id
 * @property string $email
 * @property string $phone
 */
class MemberExt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_ext';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id'], 'required'],
            [['member_id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'email' => 'Email',
            'phone' => 'Phone',
        ];
    }
}
