<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay_js_log".
 *
 * @property integer $id
 * @property string $mchid
 * @property string $site
 * @property string $created_at
 */
class PayJsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pay_js_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site'], 'string'],
            [['mchid'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mchid' => 'Mchid',
            'site' => 'Site',
            'created_at' => 'Created At',
        ];
    }
}
