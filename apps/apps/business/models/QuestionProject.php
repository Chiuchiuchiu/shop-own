<?php

namespace apps\business\models;

use Yii;

/**
 * This is the model class for table "question_project".
 *
 * @property integer $id
 * @property string $title
 * @property string $site
 * @property string $content
 * @property integer $type_isp
 * @property string start_date
 * @property string end_date
 * @property string status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class QuestionProject extends \yii\db\ActiveRecord
{
    const STATUS_START = 1;
    const STATUS_END = 2;

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
            [['type_isp', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
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
            'start_date'=>'开始时间',
            'end_date'=>'截至时间',
            'status' => '状态',
            'type_isp' => 'Type Isp',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
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
