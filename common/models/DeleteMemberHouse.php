<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "delete_member_house".
 *
 * @author HQM 2018/12/27
 * @property integer $id
 * @property integer $member_id
 * @property integer $house_id
 * @property string $remarks
 * @property string $deleted_at
 * @property integer $created_at
 */
class DeleteMemberHouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'delete_member_house';
    }

    public function behaviors()
    {
        return [
            [
                'class'=> TimestampBehavior::className(),
                'updatedAtAttribute'=>null,
            ]
        ];
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
            [['member_id', 'house_id', 'created_at'], 'integer'],
            [['deleted_at'], 'safe'],
            [['remarks'], 'string', 'max' => 500],
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
            'house_id' => 'House ID',
            'remarks' => 'Remarks',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @param $memberId
     * @param $houseId
     * @param $rem
     * @return bool
     */
    public static function log($memberId, $houseId, $rem)
    {
        $model = new self();
        $model->member_id = $memberId;
        $model->house_id = $houseId;
        $model->remarks = serialize($rem);

        return $model->save();
    }

}
