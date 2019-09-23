<?php

namespace apps\www\module\prepayLottery\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "prepay_lottery_result".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $even_name
 * @property integer $project_house_id
 * @property integer $gift_id
 * @property integer $created_at
 * @property integer $gave_at
 * @property string $manager
 *
 * @property PrepayLotteryGift $gift
 */
class PrepayLotteryResult extends \yii\db\ActiveRecord
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
        return 'prepay_lottery_result';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
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
            [['member_id', 'gift_id'], 'required'],
            [['member_id', 'gift_id', 'created_at', 'gave_at','project_house_id'], 'integer'],
            ['gave_at','default','value'=>0],
            ['manager','default','value'=>''],
            ['even_name','string','max'=>20],
            ['project_house_id','default','value'=>0],
            [['manager'], 'string', 'max' => 20],
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
            'gift_id' => 'Git ID',
            'created_at' => 'Created At',
            'gave_at' => 'Gave At',
            'manager' => 'Manager',
        ];
    }

    public function getGift(){
        return $this->hasOne(PrepayLotteryGift::className(),['id'=>'gift_id']);
    }
}
