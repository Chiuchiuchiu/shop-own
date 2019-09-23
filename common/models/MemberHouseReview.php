<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_house_review".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $butler_id
 * @property integer $house_id
 * @property integer $group
 * @property integer $identity
 * @property string $identityText
 * @property integer $status
 * @property string $statusText
 * @property string $customer_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Member $member
 * @property House $house
 */
class MemberHouseReview extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE=2;
    const STATUS_WAIT_REVIEW=0;
    const STATUS_REJECT=1;

    const GROUP_UNDEFINED=0;
    const GROUP_HOUSE=1;
    const GROUP_PARKING=2;

    const IDENTITY_OWNER=1;
    const IDENTITY_TENANT=2;
    const IDENTITY_FAMILY=3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_house_review';
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
    public function rules()
    {
        return [
            [['member_id', 'house_id', 'identity', 'customer_name'], 'required'],
            [['member_id', 'butler_id', 'house_id', 'group', 'identity', 'status', 'created_at', 'updated_at'], 'integer'],
            [['customer_name'], 'string', 'max' => 20],
        ];
    }

    public static function identityMap()
    {
        return [
            1=>'业主',
            2=>'租户',
            3=>'家庭成员'
        ];
    }

    public function getIdentityText()
    {
        return self::identityMap()[$this->identity];
    }

    public static function statusMap(){
        return [
            self::STATUS_ACTIVE=>'已认证',
            self::STATUS_WAIT_REVIEW=>'等待审批',
            self::STATUS_REJECT=>'拒绝'
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
            'member_id' => 'Member ID',
            'butler_id' => 'Butler ID',
            'house_id' => 'House ID',
            'group' => 'Group',
            'identity' => 'Identity',
            'status' => 'Status',
            'customer_name' => 'Customer Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }

    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }

    public static function createOrUpdate($memberId, $houseId)
    {
        $model = self::findOne(['member_id' => $memberId, 'house_id' => $houseId]);
        if(!$model){
            $model= new self();
            $model->member_id = $memberId;
            $model->house_id = $houseId;
        }

        return $model;
    }

}
