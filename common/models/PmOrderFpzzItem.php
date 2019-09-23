<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_fpzz_item".
 *
 * @property integer $id
 * @property integer $pm_order_fpzz_id
 * @property string $contract_no
 * @property string $charge_detail_id_list
 * @property string $spmc
 * @property string $spbm
 * @property string $ggxh
 * @property integer $sl
 * @property string $slv
 * @property string $dw
 * @property string $dj
 * @property string $je
 * @property string $origin_amount
 * @property string $se
 * @property int $status
 * @property integer $created_at
 */
class PmOrderFpzzItem extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 1;
    const STATUS_ACTIVATE = 2;
    const STATUS_SEND_POST = 3;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_fpzz_item';
    }

    public static function isUseMap()
    {
        return [
            0 => '无',
            1 => '未使用',
            2 => '已使用',
            3 => '已发送请求',
        ];
    }

    public function getIsUseText()
    {
        return self::isUseMap()[$this->status];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_fpzz_id', 'status', 'created_at'], 'integer'],
            [['origin_amount', 'dj', 'sl'], 'number'],
            [['spmc', 'spbm', 'ggxh'], 'string', 'max' => 50],
            [['slv'], 'string', 'max' => 10],
            [['dw'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'pm_order_fpzz_id' => '关联申请ID',
            'spmc' => '商品名称',
            'spbm' => '商品编码',
            'ggxh' => '规格型号',
            'sl' => '商品数量',
            'slv' => '税率',
            'dw' => '单位',
            'dj' => '单价',
//            'je' => '金额',
            'origin_amount' => '原金额',
//            'se' => '税额',
            'created_at' => '创建时间',
            'status' => '使用状态',
            'isUseText' => '使用状态',
        ];
    }
}
