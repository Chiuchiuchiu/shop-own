<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "question_item".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer $project_id
 * @property integer $numbers_count
 * @property integer $plan_count
 * @property integer $actual_count
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class QuestionItem extends \yii\db\ActiveRecord
{

    const WAIT_STATUS = 1;
    const FINISH_STATUS = 2;

    public static function tableName()
    {
        return 'question_item';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','question_id','project_id','numbers_count','plan_count','actual_count','status','created_at','updated_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'project_id' => '项目ID',
            'house_name' => '房产信息',
            'username' => '业主姓名',
            'telephone' => '联系电话',
            'address' => '联系地址',
            'urgent_telephone' => '紧急联系电话',
            'join_date' => '入伙时间',
            'created_at' => '创建时间',
            'statusText' => '状态',
        ];
    }

    public static function StatusMap()
    {
        return [
            self::WAIT_STATUS => "进行中",
            self::FINISH_STATUS => "已结束",
        ];
    }

    public function getStatusText(){
        return self::statusMap()[$this->status];
    }

    public static function ProjectName($id){

       $Lib = Project::findOne(['house_id'=>$id]);
       return $Lib->house_name;
    }

    public function getProjectName(){
        return $this->hasOne(QuestionProject::className(), ['id' => 'question_id']);
    }
}
