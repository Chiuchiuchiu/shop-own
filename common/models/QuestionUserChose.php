<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "security_per_labels".
 *
 * @property integer $id
 * @property integer $bea_id
 * @property integer $in_labels_id
 *
 * @property IndividualLabels $individualLabels
 */
class QuestionUserChose extends \yii\db\ActiveRecord
{

    const STATUS_WAIT = 1;
    const STATUS_FINISH = 2;

    public static function tableName()
    {
        return 'question_user_chose';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id','status','project_item_id'], 'integer'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'project_id' => '项目ID',
            'house_name' => '房产信息',
            'status'=>'状态',
            'username' => '业主姓名',
            'telephone' => '联系电话',
            'house_status' => '房产状态',
            'created_at' => '添加时间'
        ];
    }

    /**
     * 获取数量
     * @param $params
     * @return int|string
     * @author zhaowenxi
     */
    public static function getCount($params){

        $where = [];

        (isset($params['status']) && $params['status']) && $where['status'] = $params['status'];
        (isset($params['project_id']) && $params['project_id']) && $where['project_id'] = $params['project_id'];
        (isset($params['answer_id']) && $params['answer_id']) && $where['answer_id'] = $params['answer_id'];

        return self::find()->where($where)->count();
    }
}
