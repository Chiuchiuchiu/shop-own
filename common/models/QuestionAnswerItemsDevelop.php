<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "question_answer_items".
 *
 * @property integer $id
 * @property integer $question_id
 * @property integer develop_id
 * @property integer $score
 * @property string $site
 * @property integer $season
 * @property string $created_at
 */
class QuestionAnswerItemsDevelop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_answer_items_develop';
    }

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
    public function rules()
    {
        return [
            [['question_id', 'develop_id', 'score'], 'integer'],
            [['created_at'], 'safe'],
            [['site'], 'string', 'max' => 250],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'score' => 'score',
            'site' => 'Site',
            'created_at' => 'Created At',
        ];
    }

    public function getQuestion(){
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }
}
