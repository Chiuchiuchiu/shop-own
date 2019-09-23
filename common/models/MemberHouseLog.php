<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_house_log".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $house_id
 * @property string $data
 * @property integer $action
 * @property integer $created_at
 * @property integer $operator
 * @property integer $operator_id
 */
class MemberHouseLog extends \yii\db\ActiveRecord
{
    const OPERATOR_MEMBER = 1;//用户
    const OPERATOR_BUTLER = 2;//管家
    const OPERATOR_MANAGER = 3;//后台管理员

    const ACTION_PASS = 1;//通过
    const ACTION_REJECT = 2;//拒接
    const ACTION_DELETE = 3;//删除
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_house_log';
    }

    public function behaviors()
    {
        return [
          [
              'class'=>TimestampBehavior::className(),
              'updatedAtAttribute'=>null,
          ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'house_id', 'operator', 'operator_id','action'], 'required'],
            [['member_id', 'house_id', 'operator', 'operator_id','action'], 'integer'],
            [['data'], 'string'],
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
            'created_at' => 'Created At',
            'operator' => 'Operator',
            'operator_id' => 'Operator ID',
        ];
    }
}
