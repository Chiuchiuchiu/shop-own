<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "member_vote".
 *
 * @property integer $id
 * @property int $group
 * @property integer $member_id
 * @property integer $number
 * @property integer $bsa_id
 * @property integer $house_id
 * @property integer $vote_time
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Member $member
 * @property House $house
 */
class MemberVote extends \yii\db\ActiveRecord
{

    const GROUP_BUTLER = 1;
    const GROUP_SECURITY = 2;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_vote';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'bsa_id', 'vote_time', 'group', 'house_id'], 'required'],
            [['group', 'member_id', 'number', 'bsa_id', 'house_id', 'vote_time', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => '分组',
            'member_id' => 'Member ID',
            'number' => '次数',
            'bsa_id' => '参选人 ID',
            'house_id' => '房产 ID',
            'vote_time' => '投票时间',
            'created_at' => 'Created At',
            'updated_at' => 'updated At',
        ];
    }

    /**
     * @param $memberId
     * @param integer $voteTime
     * @param int $group
     * @return MemberVote|null|static
     */
    public static function findOrCreate($memberId, $voteTime, $group)
    {
        $model = self::findOne(['member_id' => $memberId, 'vote_time' => $voteTime, 'group' => $group]);
        if(!$model){
            $model = new self();
            $model->member_id = $memberId;
            $model->number = 1;
            $model->vote_time = $voteTime;
            $model->group = $group;
        } else {
            $model->number += 1;
        }

        return $model;
    }

    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }

    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }
}
