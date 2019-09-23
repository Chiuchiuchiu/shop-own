<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/7
 * Time: 15:39
 */

namespace common\models;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "min_autumn_red_pack".
 *
 * @property integer $id
 * @property integer $project_id
 * @property integer $member_id
 * @property integer $sure_name
 * @property integer $house_id
 * @property string $answer
 * @property integer $amount
 * @property integer $wechat_mch_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Member $member
 * @property House $house
 * @property Project $project
 */
class MinAutumnRedPack extends ActiveRecord
{
    const STATUS_WAIT = 1;          //1未答题2已答（合格）3已答（不合格）4异常（多数为微信发放红包问题）
    const STATUS_PASS = 2;
    const STATUS_FAIL = 3;
    const STATUS_VOID = 4;

    const RED_PACK_TOTAL = 1500;    //红包总个数

    const QUESTION_TOTAL = 5;   //总共多少题
    const QUESTION_PASS = 3;    //对多少题算合格

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'min_autumn_red_pack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['answer','sure_name'], 'string'],
            [['project_id', 'status', 'house_id', 'member_id', 'created_at', 'updated_at', 'wechat_mch_id', 'amount'], 'integer'],
        ];
    }

    public static function statusType()
    {
        return [
            self::STATUS_WAIT => '未答题',
            self::STATUS_PASS => '合格',
            self::STATUS_FAIL => '不合格',
            self::STATUS_VOID => '作废',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(){
        return [
            'id' => '#',
            'project_id' => '项目id',
            'house_id' => '房产id',
            'member_id'=> '业主id',
            'answer'=> '业主提交的答案',
            'amount'=> '领取金额',
            'status'=> '状态',
            'is_red'=> '是否已领取红包',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    public function getHouse(){
        return $this->hasOne(House::className(),['house_id'=>'house_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_id']);
    }

    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }
}