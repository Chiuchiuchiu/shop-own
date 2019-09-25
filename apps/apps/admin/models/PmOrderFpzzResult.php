<?php

namespace apps\admin\models;

use common\models\PmOrderFpzz;
use common\models\PmOrderFpzzPdf;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_fpzz_result".
 *
 * @property integer $id
 * @property integer $pm_order_fpzz_id
 * @property integer $project_house_id
 * @property string $result_id
 * @property integer $status
 * @property string $statusText
 * @property integer $send_window_status default 0
 * @property integer $pm_order_id
 * @property string $email
 * @property integer $member_id
 * @property string $item_ids
 * @property string $jehj
 *
 * @property PmOrderFpzz $pmOrderFpzz
 * @property PmOrderFpzzPdf $pmOrderFpzzPdf
 */
class PmOrderFpzzResult extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 0;
    const STATUS_USED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_fpzz_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_fpzz_id', 'status', 'pm_order_id', 'member_id', 'project_house_id'], 'integer'],
            [['result_id'], 'string', 'max' => 50],
            [['item_ids'], 'string', 'max' => 60],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ],
        ];
    }

    public function isUseMap()
    {
        return [
            0 => '开票中',
            1 => '已开具',
            2 => '已开具',
            3 => '开票失败',
            4 => '设备异常',
            5 => '开票失败',
            6 => '设备异常',
            30 => '开票失败',
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

    public static function statusStyle()
    {
        return [
            self::STATUS_DEFAULT => 'fpzz-status-wait',
            self::STATUS_USED => 'fpzz-status-active',
            2 => 'fpzz-status-active',
            3 => 'fpzz-status-wait',
            4 => 'fpzz-status-wait',
            5 => 'fpzz-status-wait',
            6 => 'fpzz-status-wait',
            30 => 'fpzz-status-wait',
        ];
    }

    public function getStatusStyle()
    {
        return self::statusStyle()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_fpzz_id' => 'Pm Order Fpzz ID',
            'result_id' => 'Result ID',
        ];
    }

    public function getPmOrderFpzz()
    {
        return $this->hasOne(PmOrderFpzz::className(), ['id' => 'pm_order_fpzz_id']);
    }

    public function getPmOrderFpzzPdf()
    {
        return $this->hasOne(PmOrderFpzzPdf::className(), ['fpid' => 'result_id']);
    }

}
