<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property  $id
 * @property  $nick_name
 * @property  $sex
 * @property  $mobile
 * @property  $email
 * @property  $password
 * @property  $salt
 * @property  $id_card
 * @property  $avatar
 * @property  $real_name
 * @property  $status
 * @property  $score
 * @property  $amount
 * @property  $amount_wait
 * @property  $amount_locked
 * @property  $total_score
 * @property  $lock_score
 * @property  $day
 * @property  $login_times
 * @property  $last_login_ip
 * @property  $sort
 * @property  $openid
 * @property  $unionid
 * @property  $rid
 * @property  $member_type
 * @property  $user_level
 * @property  $created_at
 * @property  $deleted_at
 *
 * @property Shop $shop
 * @property GoodsCategory $GoodsCategory
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    //状态 1 启用  2 商家禁用 3 系统禁用 0 审核中
    const STATUS_ACTIVE = 1;
    const STATUS_SHOP_DELETE = 2;
    const STATUS_ADMIN_DELETE = 3;

    //性别 0未知  1男  2女
    const SEX_UNKNOWN = 0;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    //会员类型 1青铜会员；2白银会员；3黄金会员4；铂金会员
    const MEMBER_CU = 1;
    const MEMBER_AG = 2;
    const MEMBER_AU = 3;
    const MEMBER_PT = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['openid', 'nick_name', 'status'], 'required'],
            [['openid', 'unionid', 'nick_name'], 'string', 'max' => 50],
            [['avatar'], 'string', 'max' => 255],
            [['nick_name'], 'default', 'value' => ''],
            [['mobile'], 'string', 'max'=>20],
            [['mobile'], 'default','value'=>0],
            [['member_type'], 'default','value'=>1],
        ];
    }

    public static function statusMap(){
        return [
            0 => '审核中',
            self::STATUS_ACTIVE => '启用',
            self::STATUS_SHOP_DELETE => '商家禁用',
            self::STATUS_ADMIN_DELETE => '系统禁用',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public static function memberMap(){
        return [
            self::MEMBER_CU => '青铜会员',
            self::MEMBER_AG => '白银会员',
            self::MEMBER_AU => '黄金会员',
            self::MEMBER_PT => '铂金会员',
        ];
    }

    public function getMemberTypeText(){
        return self::memberMap()[$this->member_type ];
    }

    public static function sexMap(){
        return [
            self::SEX_UNKNOWN => "未知",
            self::SEX_MALE => '男',
            self::SEX_FEMALE => '女',
        ];
    }

    public function getSexText(){
        return self::sexMap()[$this->sex];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nick_name' => '昵称',
            'sex' => '性别',
            'sexText' => '性别',
            'mobile' => '手机号',
            'email' => '联系邮箱',
            'password' => '密码',
            'salt' => '加密串',
            'id_card' => '身份证',
            'avatar' => '头像',
            'real_name' => '真实名称',
            'status' => '状态',
            'statusText' => '状态',
            'score' => '积分',
            'amount' => '会员金额',
            'amount_wait' => '会员待结算金额',
            'amount_locked' => '会员冻结金额',
            'total_score' => '累计获取积分',
            'lock_score' => '锁定积分',
            'day' => '连续签到天数',
            'login_times' => '登录时间',
            'last_login_ip' => '最后登录ip',
            'sort' => '排序',
            'openid' => '微信openid',
            'unionid' => '微信统一unionid',
            'rid' => '分享者',
            'member_type' => '会员类型',
            'memberTypeText' => '会员类型',
            'user_level' => '会员等级', //1准会员；2正式会员
            'created_at' => '创建时间',
            'deleted_at' => '删除时间',
        ];
    }

    public function getGoodsCategory()
    {
        return $this->hasOne(GoodsCategory::className(), ['id' => 'category_id']);
    }

    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        throw new NotSupportedException();

    }

    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException();
    }
}
