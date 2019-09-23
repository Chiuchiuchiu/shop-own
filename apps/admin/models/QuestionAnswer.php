<?php

namespace apps\admin\models;

use Yii;

/**
 * This is the model class for table "question_answer".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $butler_id
 * @property integer $question_project_id
 * @property integer $project_house_id
 * @property integer $project_region_id
 * @property integer $question_score
 * @property string $score_json
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class QuestionAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'butler_id', 'question_project_id', 'project_house_id', 'project_region_id', 'question_score', 'status'], 'integer'],
            [['score_json'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
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
            'butler_id' => 'Butler ID',
            'question_project_id' => 'Question Project ID',
            'project_house_id' => 'Project House ID',
            'project_region_id' => 'Project Region ID',
            'question_score' => 'Question Score',
            'score_json' => 'Score Json',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }


}
