<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "visit_house_butler_to_butler_log".
 *
 * @property integer $id
 * @property integer $from_butler_id
 * @property integer $to_butler_id
 * @property integer $manage_id
 * @property integer $number
 * @property integer $created_at
 */
class VisitHouseButlerToButlerLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visit_house_butler_to_butler_log';
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
            [['from_butler_id', 'to_butler_id', 'manage_id', 'number', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_butler_id' => '发起管家',
            'to_butler_id' => '目标管家',
            'manage_id' => '管理员',
            'number' => '数量',
            'created_at' => 'Created At',
        ];
    }

    /**
     *
     * @param $fromButlerId
     * @param $toButlerId
     * @param $manageId
     * @param $number
     * @return bool
     */
    public static function Create($fromButlerId, $toButlerId, $manageId, $number)
    {
        $model = new self();
        $model->from_butler_id = $fromButlerId;
        $model->to_butler_id = $toButlerId;
        $model->manage_id = $manageId;
        $model->number = $number;

        return $model->save();
    }

}
