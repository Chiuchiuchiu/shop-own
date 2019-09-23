<?php

namespace common\models;

use apps\mgt\models\PmOrderFpzzResult;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_fpzz".
 *
 * @property integer $id
 * @property integer $pm_order_id
 * @property integer $member_id
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $type
 * @property string $typeText
 * @property integer $category 1、个人；2、单位
 * @property string $email
 * @property string $phone
 * @property string $user_name
 * @property string $register_id
 * @property string $total_amount
 * @property string $house_address
 * @property integer $show_status
 * @property integer $status    default 4
 * @property string $statusText
 * @property string $remarks
 * @property int $request_number
 * @property int $client_type
 * @property string $clientTypeText
 * @property integer $created_at
 * @property integer $updated_at

 * @property Member $member
 * @property PmOrder $pmOrder
 * @property PmOrderFpzzResult $pmOrderFpzzResult
 * @property PmOrderNewwindowPdf $pmOrderNewwindowPdf
 */
class PmOrderFpzz extends \yii\db\ActiveRecord
{
    const STATUS_PM = 5;
    const STATUS_POST_SUCCESS = 4;
    const STATUS_SUCCESS = 3;
    const STATUS_ACTIVE = 2;
    const STATUS_REJECT = 1;
    const STATUS_WAIT_REVIEW = 0;
    const STATUS_P_ACTIVE = 10;

    const SHOW_STATUS_DISABLE = 0;
    const SHOW_STATUS_ACTIVE = 1;

    const TYPE_E = 1;
    const TYPE_P = 2;
    const CLIENT_TYPE_MP = 1;
    const CLIENT_TYPE_MINI = 2;

    private const P_INVOICE = 'p_invoice';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_fpzz';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className()
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['id', 'pm_order_id', 'member_id', 'house_id', 'project_house_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['total_amount'], 'number'],
            [['email', 'house_address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            ['client_type', 'default', 'value' => 1],
            [['user_name'], 'string', 'max' => 30],
            ['phone', 'validateLogin', 'on' => self::P_INVOICE],
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_WAIT_REVIEW => '开票失败',
            self::STATUS_REJECT => '拒绝',
            self::STATUS_ACTIVE => '开票中',
            self::STATUS_SUCCESS => '已发送邮箱',
            self::STATUS_POST_SUCCESS => '提交成功',
            self::STATUS_PM => '前台补录',
            self::STATUS_P_ACTIVE => '纸质发票已开',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }
    public static function statusShowWeb()
    {
        return [
            self::STATUS_WAIT_REVIEW => '开票失败',
            self::STATUS_REJECT => '拒绝',
            self::STATUS_ACTIVE => '开票中',
            self::STATUS_SUCCESS => '已发送邮箱',
            self::STATUS_POST_SUCCESS => '提交成功',
            self::STATUS_PM => '前台补录',
            self::STATUS_P_ACTIVE => '纸质发票已开',
        ];
    }

    public function getStatusShowWeb()
    {
        return self::statusShowWeb()[$this->status];
    }

    public static function statusStyle()
    {
        return [
            self::STATUS_WAIT_REVIEW => 'fpzz-status-wait',
            self::STATUS_REJECT => 'fpzz-status-wait',
            self::STATUS_ACTIVE => 'fpzz-status-active',
            self::STATUS_SUCCESS => 'fpzz-status-active',
            self::STATUS_POST_SUCCESS => 'fpzz-status-active',
            self::STATUS_PM => 'fpzz-status-active',
            self::STATUS_P_ACTIVE => 'fpzz-status-active',
        ];
    }

    public function getStatusStyle()
    {
        return self::statusStyle()[$this->status];
    }

    public static function statusListsMap()
    {
        return [
            self::STATUS_WAIT_REVIEW=>'开票失败',
            self::STATUS_REJECT=>'拒绝',
            self::STATUS_ACTIVE=>'开票中',
            self::STATUS_SUCCESS => '已发送邮箱',
            self::STATUS_POST_SUCCESS=>'提交成功',
            self::STATUS_PM=>'前台补录',
            self::STATUS_P_ACTIVE => '纸质发票已开',
        ];
    }

    public function getStatusListsText()
    {
        return self::statusListsMap()[$this->status];
    }

    public static function typeMap()
    {
        return [
          self::TYPE_E => '电子发票',
          self::TYPE_P => '纸质发票',
        ];
    }

    public function getTypeText()
    {
        return self::typeMap()[$this->type];
    }

    public static function clientType()
    {
        return [
            self::CLIENT_TYPE_MP => '公众号',
            self::CLIENT_TYPE_MINI => '小程序',
        ];
    }

    public function getClientTypeText()
    {
        return self::clientType()[$this->client_type];
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getPmOrder()
    {
        return $this->hasOne(PmOrder::className(), ['id' => 'pm_order_id']);
    }

    public function getPmOrderFpzzResult()
    {
        return $this->hasMany(PmOrderFpzzResult::className(), ['pm_order_fpzz_id' => 'id']);
    }

    public function getPmOrderNewwindowPdf()
    {
        return $this->hasMany(PmOrderNewwindowPdf::className(), ['pm_order_fpzz_id' => 'id']);
    }

    public function validateLogin($attribute)
    {
        $rule = '/^((0|86|17951)?(13[0-9]|15[012356789]|17[0678]|18[0-9]|14[57]|16[0-9])[0-9]{8})|((010-?\d{8})|(0[2-9]\d{1}-?\d{8}))$/';
        if(!preg_match($rule, $this->phone)){
            $this->addError($attribute, '联系方式错误');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'pm_order_id' => 'Pm Order ID',
            'member_id' => 'Member ID',
            'house_id' => 'House ID',
            'project_house_id' => 'Project House ID',
            'type' => 'Type',
            'typeText' => '发票类型',
            'category' => '抬头类型',
            'email' => 'Email',
            'phone' => '联系方式',
            'user_name' => '发票抬头',
            'register_id' => '纳税人识别号',
            'show_status' => '是否显示',
            'status' => '状态',
            'statusText' => "状态",
            'remarks' => "备注",
            'total_amount' => '发票金额',
            'house_address' => '缴费房产全称',
            'created_at' => '提交时间',
            'updated_at' => 'Updated At',
        ];
    }
}
