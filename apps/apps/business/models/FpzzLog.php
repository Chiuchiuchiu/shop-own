<?php

namespace apps\business\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fpzz_log".
 *
 * @property integer $id
 * @property string $type
 * @property string $post_data
 * @property string $result
 * @property string $fp_cached_id
 * @property integer $pm_order_fpzz_id
 * @property integer $pm_order_id
 * @property integer $created_at
 */
class FpzzLog extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute'=>null
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fpzz_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['pm_order_fpzz_id', 'integer'],
            [['post_data', 'result'], 'string'],
            [['fp_cached_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'post_data' => 'Post Data',
            'result' => 'Result',
            'fp_cached_id' => 'fp_cached_id',
            'pm_order_fpzz_id' => 'pm_order_fpzz_id',
            'pm_order_id' => '缴费订单',
        ];
    }
}
