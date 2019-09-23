<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_house_wlist".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $member_nickname
 * @property integer $auth_count
 * @property integer $status
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Member $member
 */
class MemberHouseWList extends \yii\db\ActiveRecord
{
    const TYPE_DEFINE = 0;
    const TYPE_NORMAL = 1;
    const STATUS_ACTIVE=2;
    const STATUS_WAIT_REVIEW=0;
    const STATUS_REJECT=1;

    const GROUP_UNDEFINED=0;
    const GROUP_HOUSE=1;
    const GROUP_PARKING=2;

    const IDENTITY_OWNER=1;
    const IDENTITY_TENANT=2;
    const IDENTITY_FAMILY=3;

    const DEFAULT_COUNT = 5;            // 默认5套房

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_house_wlist';
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
            [['member_id', 'auth_count'], 'required'],
            [['member_id', 'auth_count', 'status', 'created_at', 'updated_at'], 'integer'],
         //   [['customer_name'], 'string', 'max' => 20],
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
            'member_nickname' => 'Member NickName',
            'auth_count' => 'Auth Count',
            'type' => 'Type',
            'remark' => 'Remark',
            'status' => 'Status',
            'customer_name' => 'Customer Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }
    public static function createOrUpdate($id,$memberId, $authCount,$remark)
    {
        $model = self::findOne(['member_id' => $memberId, 'id' => $id]);
        if(!$model){
            $model= new self();
            $model->member_id = $memberId;
            $model->auth_count = $authCount;
            $model->remark = $remark;
        }

        return $model;
    }

}
