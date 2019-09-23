<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "lxj_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $project_house_id
 * @property string $ip
 * @property integer $created_at
 *
 * @property Member $member
 */
class LxjLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lxj_log';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('logDb');
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
    public function rules()
    {
        return [
            [['member_id', 'project_house_id', 'created_at'], 'integer'],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'project_house_id' => 'Project House ID',
            'ip' => 'Ip',
            'created_at' => 'Created At',
        ];
    }

    public static function writeLog($memberId, $projectHouseId, $ip='-')
    {
        $model = new self();
        $model->member_id = $memberId;
        $model->project_house_id = $projectHouseId;
        $model->ip = $ip;

        $model->save();

        return $model->getErrors();
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }

}
