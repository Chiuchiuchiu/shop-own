<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_member_develop".
 *
 * @property integer $id
 * @property integer $member_type
 * @property integer $number
 * @property integer $send_status
 * @property string $company
 * @property string $project
 * @property string $name
 * @property string $phone
 * @property string $job
 * @property integer $year
 * @property integer $season
 * @property integer $created_at
 */
class QuestionMemberDevelop extends \yii\db\ActiveRecord
{

    const DEVELOP_TYPE = 1;         //开发商
    const MEMBER_TYPE = 2;          //业委会
    const NEIGHBORHOOD_TYPE = 3;    //居委会

    const SEND_SUCCESS = 1;         //成功
    const SEND_FAIL = 2;            //失败

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_member_develop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_type','number','send_status'], 'integer'],
            [['company','project','name','phone','job'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company' => '分公司',
            'project' => '项目',
            'name' => '姓名',
            'phone' => '联系电话',
            'member_type' => '身份',
            'typeText' => '类型',
            'number' => '通知次数',
            'send_status' => '发送状态',
            'sendText' => '发送状态',
            'year' => '年份',
            'season' => '季度',
            'job' => '职位',
            'created_at' => '创建时间',
        ];
    }

    public static function TypeMap()
    {
        return [
            0 => '未知',
            self::DEVELOP_TYPE => "开发商",
            self::MEMBER_TYPE => "业委会",
            self::NEIGHBORHOOD_TYPE => "居委会",
        ];
    }

    public function getTypeText(){
        return self::TypeMap()[$this->member_type];
    }

    public static function SendMap()
    {
        return [
            0 => '未知',
            self::SEND_SUCCESS => "成功",
            self::SEND_FAIL => "失败",
        ];
    }

    public function getSendText(){
        return self::TypeMap()[$this->send_status];
    }

    public function getCount(){
        return QuestionAnswerItemsDevelop::find()->where(['develop_id' => $this->id])->count();
    }
}
