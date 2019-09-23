<?php

namespace apps\admin\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_login_log".
 *
 * @property integer $id
 * @property integer $manager_id
 * @property integer $time
 * @property string $ip
 */
class ManagerLoginLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager_login_log';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['manager_id','ip'], 'required'],
            [['manager_id','time'], 'integer'],
            [['ip'], 'string', 'max' => 24],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'manager_id' => 'Manager ID',
            'time' => 'Time',
            'ip' => 'Ip',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'updatedAtAttribute'=>null
            ]

        ];
    }


}
