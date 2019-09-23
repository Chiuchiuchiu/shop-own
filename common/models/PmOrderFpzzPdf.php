<?php

namespace common\models;

use apps\mgt\models\PmOrderFpzzResult;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_fpzz_pdf".
 *
 * @property integer $id
 * @property integer $fpr_id
 * @property integer $pm_order_id
 * @property integer $kprq
 * @property integer $house_id
 * @property integer $project_house_id
 * @property integer $status
 * @property string $fphm
 * @property string $fpdm
 * @property string $xfsh
 * @property string $gfsh
 * @property string $gfmc
 * @property string $xfmc
 * @property string $jehj
 * @property string $sehj
 * @property string $url
 * @property string $save_path
 * @property integer $created_at
 * @property integer $member_id
 * @property string $processing_note
 *
 * @property PmOrderFpzz $pmOrderFpzz
 * @property string statusText
 * @property PmOrder $pmOrder
 * @property Project $project
 * @property PmOrderFpzzResult $pmOrderFpzzResult
 */
class PmOrderFpzzPdf extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_USED = 1;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_fpzz_pdf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_id', 'kprq', 'created_at'], 'integer'],
            [['jehj', 'sehj'], 'number'],
            [['fphm'], 'string', 'max' => 15],
            [['fpdm'], 'string', 'max' => 20],
            [['xfsh', 'gfsh'], 'string', 'max' => 25],
            [['gfmc', 'xfmc'], 'string', 'max' => 60],
            [['url', 'save_path', 'processing_note'], 'string', 'max' => 255],
        ];
    }

    public static function statusMap()
    {
        return [
            self::STATUS_DEFAULT => '开票中',
            self::STATUS_USED => '已开具',
            2 => '已开具',
            3 => '开票失败',
            4 => '设备异常',
            5 => '开票失败',
            6 => '设备异常',
            30 => '开票失败',
        ];
    }

    public function getStatusText()
    {
        return self::statusMap()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'pm_order_id' => '关联订单',
            'kprq' => '开票日期',
            'fphm' => '发票号码',
            'fpdm' => '发票代码',
            'xfsh' => '销方税号',
            'gfsh' => '购方税号',
            'gfmc' => '购方名称',
            'xfmc' => '销方名称',
            'jehj' => '金额合计',
            'sehj' => '税额合计',
            'url' => 'PDF地址',
            'email' => '电子邮箱',
            'fpr_id' => '关联开票结果',
            'created_at' => '生成时间',
            'processing_note' => '财务处理备注信息',
        ];
    }

    public static function findOrCreate($fpid){
        $res = self::findOne(['fpr_id' => $fpid]);
        if(!$res){
            $res=new self();
            $res->fpr_id = $fpid;
            $res->created_at = time();
        }
        return $res;
    }

    public function getPmOrderFpzz()
    {
        return $this->hasOne(PmOrderFpzz::className(), ['pm_order_id' => 'pm_order_id']);
    }

    public function getPmOrder()
    {
        return $this->hasOne(PmOrder::className(), ['id' => 'pm_order_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }

    public function getPmOrderFpzzResult()
    {
        return $this->hasOne(PmOrderFpzzResult::className(), ['id' => 'fpr_id']);
    }
}
