<?php

namespace common\models;

use apps\mgt\models\Manager;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "pm_order_auditing_log".
 *
 * @property integer $id
 * @property integer $pm_order_auditing_id
 * @property integer $manager_id
 * @property integer $created_at
 * @property string $message
 * @property string $data
 *
 * @property Manager $manager
 */
class PmOrderAuditingLog extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
          [
              'class'=>TimestampBehavior::className(),
              'updatedAtAttribute'=>null
          ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pm_order_auditing_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pm_order_auditing_id', 'manager_id', 'message', 'data'], 'required'],
            [['pm_order_auditing_id', 'manager_id', 'created_at'], 'integer'],
            [['data'], 'string'],
            [['message'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pm_order_auditing_id' => 'Pm Order Auditing ID',
            'manager_id' => 'Manager ID',
            'created_at' => 'Created At',
            'message' => 'Message',
            'data' => 'Data',
        ];
    }

    public function getManager(){
        return $this->hasOne(Manager::className(),['id'=>'manager_id']);
    }
}
