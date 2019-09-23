<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "repair".
 *
 * @property integer $id
 * @property integer $type
 * @property string $typeText
 * @property integer $house_id
 * @property string $address
 * @property integer $member_id
 * @property string $flow_style_id
 * @property string $name
 * @property string $tel
 * @property string $content
 * @property string $pics
 * @property integer $status
 * @property string $statusText
 * @property string $butlerStatusText
 * @property integer $new_window_id
 * @property integer $site
 * @property string $siteText
 * @property string $flowStyleText
 * @property string $comment
 * @property integer $created_at
 * @property string $reception_user_id
 * @property string $reception_user_name
 * @property string $order_user_id
 * @property string $order_user_name
 * @property integer $project_house_id
 * @property integer $updated_at
 *
 * @property House $house
 * @property Member $member
 * @property RepairResponse $repairResponse
 * @property RepairCustomerEvaluation $repairCustomerEvaluation
 * @property RepairCancel $repairCancel
 */
class Repair extends \yii\db\ActiveRecord
{

    const STATUS_WAIT = 0;
    const STATUS_UNDERWAY = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_DONE = 3;
    const STATUS_CANCEL = 1000;
    const STATUS_EVALUATED = 3000;
    const STATUS_HOLD = 5;
    const FLOW_STYLE_TYPE_W = 'w';
    const FLOW_STYLE_TYPE_8 = '8';
    const FLOW_STYLE_TYPE_J = 'j';
    const FLOW_STYLE_TYPE_K = 'k';
    const SITE_TYPE_1 = 1;
    const SITE_TYPE_2 = 2;
    const SOURCES_WECHAT = 8;   //报事来源

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
    public static function tableName()
    {
        return 'repair';
    }

    public function getTypeText()
    {
        return self::TypeMap()[$this->type];
    }

    public function getStatusText()
    {
        return self::StatusMap()[$this->status];
    }

    public static function StatusMap()
    {
        return [
            0 => '待处理',
            1 => '未接收',
            self::STATUS_COMPLETE => '处理中',
            3 => '待评价',
            4 => '已作废',
            self::STATUS_HOLD => '暂挂',
            6 => '已接收',
            7 => '待回访',
            8 => '已升级',
            9 => '升级处理中',
            1000 => '已取消',
            self::STATUS_EVALUATED => '已完成',
        ];
    }

    public function getButlerStatusText()
    {
        return self::ButlerStatusMap()[$this->status];
    }

    public static function ButlerStatusMap()
    {
//        return [
//            0 => '待受理',
//            1 => '未接收',
//            2 => '处理中',
//            3 => '待评价',
//            4 => '已作废',
//            5 => '暂挂',
//            6 => '已接收',
//            7 => '待回访',
//            8 => '已升级',
//            9 => '升级处理中',
//            1000 => '取消提交',
//            self::STATUS_EVALUATED => '已评价',
//        ];

        return [
            0 => '待处理',
            1 => '未接收',
            self::STATUS_COMPLETE => '处理中',
            3 => '待评价',
            4 => '已作废',
            self::STATUS_HOLD => '暂挂',
            6 => '已接收',
            7 => '待回访',
            8 => '已升级',
            9 => '升级处理中',
            1000 => '已取消',
            self::STATUS_EVALUATED => '已完成',
        ];
    }

    public static function statusList()
    {
        return [
            '' => '全部',
            0 => '待提交',
            1 => '未接收',
            2 => '处理中',
            3 => '待评价',
            4 => '已作废',
            self::STATUS_HOLD => '暂挂',
            7 => '待回访',
            1000 => '取消提交',
            self::STATUS_EVALUATED => '已评价',
        ];
    }

    public static function SiteMap()
    {
        return [
            0 => '默认',
            self::SITE_TYPE_1 => '公共维修',
            self::SITE_TYPE_2 => '个人维修'
        ];
    }

    public function getSiteText()
    {
        return self::SiteMap()[$this->site];
    }

    public static function FlowStyleMap()
    {
        return [
            'w' => '报修',
            '8' => '投诉',
            'j' => '服务',
            'k' => '咨询',
        ];
    }

    public function getFlowStyleText()
    {
        return self::FlowStyleMap()[$this->flow_style_id]??'报修';
    }

    public static function TypeMap()
    {
        return [
            1 => '公共设施，硬件维修',
            2 => '供水设施',
            3 => '楼宇电梯',
            4 => '楼宇照明设施',
            5 => '其他',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'member_id', 'name', 'tel', 'content'], 'required'],
            [['type', 'house_id', 'member_id', 'status', 'created_at', 'new_window_id', 'site', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['tel'], 'string', 'max' => 20],
            [['flow_style_id'], 'string', 'max' => 16],
            ['status', 'default', 'value' => 0],
            [['content', 'comment'], 'string', 'max' => 500],
            [['pics'], 'string', 'max' => 300],
            [['address', 'reception_user_id', 'reception_user_name', 'order_user_id', 'order_user_name'], 'string', 'max' => 255],
            ['new_window_id', 'default', 'value' => 0],
            ['flow_style_id', 'default', 'value' => 'w'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'type' => 'Type',
            'flow_style_id' => '事务类型 ',
            'house_id' => '联系地址',
            'member_id' => 'Member ID',
            'name' => '客户姓名',
            'tel' => '联系人号码',
            'reception_user_name' => '受理人',
            'order_user_name' => '工程部',
            'statusText' => '报事状态',
            'content' => 'Content',
            'pics' => 'Pics',
            'status' => 'Status',
            'new_window_id' => '是否同步到新视窗',
            'created_at' => '提交时间',
        ];
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

    public function getRepairResponse()
    {
        return $this->hasOne(RepairResponse::className(), ['repair_id' => 'id']);
    }

    public function getRepairCustomerEvaluation()
    {
        return $this->hasOne(RepairCustomerEvaluation::className(), ['repair_id' => 'id']);
    }

    public function getRepairCancel()
    {
        return $this->hasOne(RepairCancel::className(), ['repair_id' => 'id']);
    }

}
