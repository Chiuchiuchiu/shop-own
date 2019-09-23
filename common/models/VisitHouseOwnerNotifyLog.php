<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "visit_house_owner_notify_log".
 *
 * @property integer $id
 * @property integer $visit_house_owner_id
 * @property integer $member_id
 * @property string $butler_qywechat_ids
 * @property string $result
 * @property integer $created_at
 *
 * @property VisitHouseOwner $visitHouseOwner
 */
class VisitHouseOwnerNotifyLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visit_house_owner_notify_log';
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
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'created_at'], 'integer'],
            [['result'], 'string'],
            [['butler_qywechat_ids'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visit_house_owner_id' => '关联visit_house_owner表',
            'member_id' => 'Member ID',
            'butler_qywechat_ids' => 'Butler Qywechat Ids',
            'result' => 'Result',
            'created_at' => 'Created At',
        ];
    }

    public function getVisitHouseOwner()
    {
        return $this->hasOne(VisitHouseOwner::className(), ['id' => 'visit_house_owner_id']);
    }

    /**
     * @param $butlerQyWechatIds
     * @param $memberId
     * @param integer $visitHouseOwnerId
     * @return bool
     */
    public static function writeLog($butlerQyWechatIds, $memberId, $visitHouseOwnerId=0)
    {
        $model = new self();
        $model->visit_house_owner_id = $visitHouseOwnerId;
        $model->butler_qywechat_ids = $butlerQyWechatIds;
        $model->member_id = $memberId;

        return $model->save();
    }

}
