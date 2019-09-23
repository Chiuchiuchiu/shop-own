<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_project".
 *
 * @property integer $id
 * @property string $title
 * @property string $site
 * @property string $content
 * @property integer $type_isp
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at

 * @property integer $start_time
 * @property integer $expired_time
 * @property string $start_date
 * @property string $end_date
 */
class QuestionProject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['site', 'content'], 'string'],
            [['type_isp'],['start_time'],['expired_time'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'title' => '标题',
            'site' => '备注',
            'content' => 'Content',
            'statusText' => '状态',
            'type_isp' => 'Type Isp',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'start_time' => '活动开始时间',
            'expired_time' => '活动结束时间',
        ];
    }
    public static function AnswerCount($id){
        $Count = QuestionAnswer::find()
            ->where(['question_project_id'=>$id])
            ->andFilterWhere(['status' =>1])
            ->count();
        return $Count;
    }

}
