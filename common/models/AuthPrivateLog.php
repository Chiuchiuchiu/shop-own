<?php

namespace common\models;


use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "butler_auth_log".
 *

 * @property integer project_id
 * @property integer house_id
 * @property integer $member_id
 * @property integer $created_at
 */
class AuthPrivateLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_private_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'house_id', 'member_id', 'created_at'], 'integer'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => null
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'Project Id',
            'house_id' => 'House Id',
            'member_id' => 'Member Id',
            'created_at' => 'Created At',
        ];
    }

    public static function writesLog($projectId, $houseId, $memberId)
    {
        $model = new self();
        $model->project_id = $projectId;
        $model->house_id = $houseId;
        $model->member_id = $memberId;

        return $model->save();
    }
}
