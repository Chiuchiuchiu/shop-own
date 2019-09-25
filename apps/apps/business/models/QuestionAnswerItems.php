<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "question_answer_items".
 *
 * @property integer $id
 * @property integer $question_answer_id
 * @property integer $question_id
 * @property integer $question_project_id
 * @property integer $type_isp
 * @property integer $replys
 * @property string $site
 * @property string $created_at
 */
class QuestionAnswerItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_answer_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_answer_id', 'question_id', 'question_project_id', 'type_isp', 'replys'], 'integer'],
            [['created_at'], 'safe'],
            [['site'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_answer_id' => 'Question Answer ID',
            'question_id' => 'Question ID',
            'question_project_id' => 'Question Project ID',
            'type_isp' => 'Type Isp',
            'replys' => 'Replys',
            'site' => 'Site',
            'created_at' => 'Created At',
        ];
    }
}
