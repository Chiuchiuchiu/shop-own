<?php

namespace common\models;

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
 * @property integer $member_house_id
 * @property string $surname
 * @property string $ancestor_name
 * @property string $telephone
 * @property string $score_json
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 * @property Project $project
 * @property ProjectRegion $projectregion
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
            'surname' => '业主姓名',
            'telephone' => '联系电话',
            'butler_id' => 'Butler ID',
            'ancestor_name' => '物业单位',
            'question_project_id' => 'Question Project ID',
            'project_house_id' => 'Project House ID',
            'project_region_id' => 'Project Region ID',
            'question_score' => 'Question Score',
            'score_json' => 'Score Json',
            'status' => 'Status',
            'created_at' => '调研问卷时间',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * 统计已完成数
     * @param $params
     * @return int|string
     * @author zhaowenxi
     */
    public static function getCount($params){

        $where = [];

        (isset($params['status']) && $params['status']) && $where['status'] = $params['status'];
        (isset($params['question_project_id']) && $params['question_project_id']) && $where['question_project_id'] = $params['question_project_id'];
        (isset($params['project_house_id']) && $params['project_house_id']) && $where['project_house_id'] = $params['project_house_id'];

        return self::find()->where($where)->count();
    }
    public static function ProjectCount($id,$question_project_id){

        $Count = self::find()
            ->where(['status'=>1,
                    'question_project_id'=>$question_project_id,
                    'project_house_id'=>$id]
            )->count();
        return $Count;
    }
    public static function ProjectRegionCount($id,$question_project_id){

        $Count = self::find()
            ->where(['status'=>1,
                    'question_project_id'=>$question_project_id,
                    'project_region_id'=>$id]
            )->count();
        return $Count;
    }
    public static function loyal($id){
        $Count = self::find()
            ->where(['status'=>1,
                    'project_region_id'=>$id]
            )->count();

        $LCount = self::find()
            ->where(['status'=>1,
                    'project_region_id'=>$id,
                    'is_loyal'=>1
                    ]
            )->count();
        if($LCount==0 || $Count==0){
            $Number =0;
        }else{
            $Number =  ceil($LCount/$Count*100);
        }
        return $Number;
    }

    public static function Houseloyal($id){

        $Count = self::find()
            ->where(['status'=>1,
                    'project_house_id'=>$id]
            )->count();
        $LCount = self::find()
            ->where(['status'=>1,
                    'project_house_id'=>$id,
                    'is_loyal'=>1
                ]
            )->count();
        if($LCount==0 || $Count==0){
            $Number =0;
        }else{
            $Number =  ceil($LCount/$Count*100);
        }
        return $Number;
    }
    public static function butlerNewCount($question_project_id,$project_region_id,$project_id,$butler_id){
        if($project_id==0){
            $Count = self::find()
                ->where(['status'=>1,
                        'question_project_id'=>$question_project_id,
                        'project_region_id'=>$project_region_id,
                        'butler_id'=>$butler_id]
                )->count();
        }else{
            $Count = self::find()
                ->where(['status'=>1,
                        'question_project_id'=>$question_project_id,
                        'project_house_id'=>$project_id,
                        'butler_id'=>$butler_id]
                )->count();
        }

        return  $Count;
    }
    public static function NewCounte($project_id){
        $Count = self::find()
            ->where(['status'=>1,
                    'project_house_id'=>$project_id]
            )->count();

        return $Count;
    }
    public static function NewCounter($project_id){
        $Count = self::find()
            ->where(['status'=>1,
                    'project_house_id'=>$project_id]
            )
            ->andWhere(['>','is_chose',0])->count();

        return $Count;
    }
    public function getHouse()
    {
        return $this->hasOne(House::className(), ['house_id' => 'member_house_id']);
    }
    public function getButler()
    {
        return $this->hasOne(Butler::className(), ['id' => 'butler_id']);
    }
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['house_id' => 'project_house_id']);
    }


    public function getProjectregion()
    {
        return $this->hasOne(ProjectRegion::className(), ['id' => 'project_region_id']);
    }


}
