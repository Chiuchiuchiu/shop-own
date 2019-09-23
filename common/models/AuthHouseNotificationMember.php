<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "auth_house_notification_member".
 *
 * @property integer $id
 * @property integer $house_id
 * @property integer $member_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class AuthHouseNotificationMember extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_house_notification_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['house_id', 'member_id', 'status', 'created_at'], 'integer'],
            ['status', 'default', 'value' => self::STATUS_DEFAULT],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
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
            'house_id' => 'House ID',
            'member_id' => 'Member ID',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }


    public static function findOrCreate($memberId, $houseId)
    {
        $model = self::findOne(['member_id' => $memberId, 'house_id' => $houseId]);
        if(!$model){
            $model = new self();
            $model->member_id = $memberId;
            $model->house_id = $houseId;
            $model->created_at = time();
        }

        $model->updated_at = time();

        return $model;
    }

}
