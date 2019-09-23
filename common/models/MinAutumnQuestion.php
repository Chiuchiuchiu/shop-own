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
 * This is the model class for table "min_autumn_question".
 *
 * @property integer $id
 * @property integer $title
 * @property string $answer
 * @property string $answer_true
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 */
class MinAutumnQuestion extends ActiveRecord
{
    const STATUS_SUCCESS = 1;
    const STATUS_VOID = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'min_autumn_question';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'answer',], 'string'],
            [['status', 'created_at', 'updated_at', 'answer_true'], 'integer'],
        ];
    }

    public static function statusType()
    {
        return [
            self::STATUS_SUCCESS => '正常',
            self::STATUS_VOID => '作废',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(){
        return [
            'id' => '#',
            'title' => '题目',
            'answer'=> '题目答案',
            'answer_true'=> '正确答案',
            'status'=> '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}