<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_house".
 *
 * @property integer $member_id
 * @property integer $house_id
 * @property integer $group
 * @property integer $identity
 * @property integer $is_first
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 *
 * @property array $liveAt
 * @property string $identityText
 * @property House $house
 * @property Member $member
 * @property string $statusText
 */
class MemberHouse extends \yii\db\ActiveRecord
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
        return 'member_house';
    }
    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ],
        ];
    }

    public function getIdentityText(){
        return self::identityMap()[$this->identity];
    }

    public static function statusMap(){
        return [
            self::STATUS_ACTIVE=>'已认证',
            self::STATUS_WAIT_REVIEW=>'等待审批',
            self::STATUS_REJECT=>'拒绝'
        ];
    }

    public static function groupMap()
    {
        return [
            self::GROUP_UNDEFINED => '未知',
            self::GROUP_HOUSE => '房子',
            self::GROUP_PARKING => '车位',
        ];
    }

    public static function identityMap(){
        return [
            1=>'业主',
            2=>'租户',
            3=>'家庭成员'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'house_id', 'identity'], 'required'],
            [['member_id', 'house_id', 'identity','status', 'created_at', 'updated_at','group'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => 'Member ID',
            'house_id' => 'House ID',
            'identity' => 'Identity',
            'status' => 'Identity',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }
    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }
    public function getLiveAt(){
        return $this->hasMany(self::className(),['house_id'=>'house_id'])->andWhere(['status'=>self::STATUS_ACTIVE]);
    }
    public static function identityHouse($house_id,$member_id){

        $data = ['member_id'=>$member_id,'house_id'=>$house_id];
        $res = self::findOne($data);


        if(!isset($res)){
            return '--';
        }else{
            if($res->identity==1){
                return  '业主';
            }elseif ($res->identity==2){

                return  '租户';
            }else{
                return  '家庭成员';
            }
        }
    }
    /**
     * @param $member_id
     * @param $house_id
     * @return MemberHouse
     * Description:
     */
    public static function findOrCreate($member_id,$house_id){
        $data = ['member_id'=>$member_id,'house_id'=>$house_id];
        $res = self::findOne($data);
        if(!$res){
            $res =  new self($data);
        }
        return $res;
    }
}
