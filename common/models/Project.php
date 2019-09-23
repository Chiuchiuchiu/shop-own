<?php

namespace common\models;

use apps\mgt\models\ProjectFeeCycle;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "project".
 *
 * @property integer $house_id
 * @property integer $project_region_id
 * @property integer $project_fee_cycle_id
 * @property string $house_name
 * @property string $logo
 * @property string $icon
 * @property string $mp_qrcode
 * @property string $area
 * @property integer $pay_type
 * @property string $payTypeText
 * @property string $url_key
 * @property integer $status
 * @property integer $sync_count
 * @property integer $created_at
 *
 * @property string $statusText
 * @property string $mchId
 * @property ProjectHouseStructure $ProjectStructure
 * @property ProjectRegion $projectRegion
 * @property string $projectRegionName
 * @property ProjectFeeCycle $projectFeeCycle
 * @property ProjectPayConfig $projectPayConfig
 */
class Project extends \yii\db\ActiveRecord
{

    const STATUS_DELETE = 0;
    const STATUS_ACTIVE = 1;
    const PAY_TYPE_W = 1;
    const PAY_TYPE_SW = 2;
    const PAY_TYPE_MS = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project';
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
            [['house_id', 'house_name', 'url_key', 'status','logo','icon'], 'required'],
            [['house_id', 'project_fee_cycle_id', 'status', 'sync_count', 'created_at'], 'integer'],
            [['house_name'], 'string', 'max' => 64],
            [['url_key'], 'string', 'max' => 16],
            [['area'], 'string', 'max' => 30],
            [['url_key', 'house_name'], 'unique'],
        ];
    }

    public static function statusMap(){
        return [
            self::STATUS_DELETE => '隐藏',
            self::STATUS_ACTIVE => '正常',
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public static function payTypeMap()
    {
        return [
            self::PAY_TYPE_W => '微信',
            self::PAY_TYPE_SW => '招商（微信）',
            self::PAY_TYPE_MS => '民生（微信）',
        ];
    }

    public function getPayTypeText()
    {
        return self::payTypeMap()[$this->pay_type];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'house_id' => '#',
            'project_region_id' => '项目区域 ID',
            'house_name' => '楼盘名字',
            'pay_type' => '支付方式',
            'payTypeText' => '支付方式',
            'url_key' => '关键key',
            'logo' => 'LOGO',
            'icon' => 'ICON',
            'status' => '状态',
            'statusText' => '状态',
            'area' => '区域',
            'projectRegionName' => '分公司',
            'sync_count' => '同步数据',
            'project_fee_cycle_id' => '关联缴费周期',
            'mchId' => '商户号',
            'mp_qrcode' => '微信小程序二维码',
        ];
    }

    public function getProjectStructure()
    {
        return $this->hasOne(ProjectHouseStructure::className(), ['house_id' => ['project_house_id']]);
    }

    public function getProjectRegion()
    {
        return $this->hasOne(ProjectRegion::className(), ['id' => 'project_region_id']);
    }

    public function getProjectRegionName()
    {
        return $this->projectRegion instanceof ProjectRegion ? $this->projectRegion->name : '未设置';
    }

    /**
     * 获取缴费周期
     * @return \yii\db\ActiveQuery
     */
    public function getProjectFeeCycle()
    {
        return $this->hasOne(ProjectFeeCycle::className(), ['id' => 'project_fee_cycle_id']);
    }

    public function getProjectPayConfig()
    {
        return $this->hasOne(ProjectPayConfig::className(), ['project_house_id' => 'house_id']);
    }

    public function getMchId()
    {
        $mchId = '-';
        if(isset($this->projectPayConfig)){
            $mchId = $this->projectPayConfig->mch_id;
        }

        return $mchId;
    }

}
