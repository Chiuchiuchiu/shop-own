<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "wechat_redpack".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $amount
 * @property integer $house_id
 * @property integer $project_id
 * @property string $remark
 * @property integer $created_at
 * @property integer $update_at
 *
 * @property Member $member
 * @property House $house
 * @property Project $project
 */
class QuestionRedPack extends \yii\db\ActiveRecord
{

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
    public static function tableName()
    {
        return 'question_red_pack';
    }

    public static function getDb()
    {
        return Yii::$app->get('eventDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'amount'], 'required'],
            [['member_id', 'created_at'], 'integer'],
            ['remark','default','value'=>'']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户名',
            'amount' => '金额',
            'remark' => 'Remark',
            'created_at' => '时间'
        ];
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }

    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'house_id']);
    }

}
