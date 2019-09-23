<?php

namespace common\models;

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
    public static function QuestionSum($question_id,$question_project_id){

        $DsCount = self::find()
            ->where(['in','replys',[4,5]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'question_id'=>$question_id]
            )->count();
        $Count = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  ceil($DsCount/$Count*100);
            return $Sum;
        }
    }

    public static function QuestionPSum($question_id,$project_region_id,$question_project_id){

        $DsCount = self::find()
            ->where(['in','replys',[4,5]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'project_region_id'=>$project_region_id,
                    'question_id'=>$question_id]
            )->count();
        /*
                $Count = self::find()
                    ->where(['question_project_id'=>$question_project_id,
                            'project_region_id'=>$project_region_id,
                            'question_id'=>$question_id]
                    )->count();
           */
                $houseArr = Project::find()
                    ->where(['project_region_id'=>$project_region_id,'status'=>1])
                    ->select(['house_id'])
                    ->column();
                $Count = QuestionItem::find()->where(['in','project_id',$houseArr])->select(['plan_count'])->sum('plan_count');

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  ceil($DsCount/$Count*100);
            return $Sum;
        }
    }


    public static function QuestionHSum($question_id,$project_house_id,$question_project_id){
        $DsCount = self::find()
            ->where(['in','replys',[4,5]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();
        /*
                $Count = self::find()
                    ->where(['question_project_id'=>$question_project_id,
                            'project_house_id'=>$project_house_id,
                            'question_id'=>$questn_id]
                    )->count();
        */
                $QuestionItem = QuestionItem::findOne(['project_id'=>$project_house_id]);
                if(isset($QuestionItem)){
                    $Count = $QuestionItem['plan_count'];
                }else{
                    $Count = self::find()
                        ->where(['question_project_id'=>$question_project_id,
                                'project_house_id'=>$project_house_id,
                                'question_id'=>$question_id]
                        )->count();
                }
        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  ceil($DsCount/$Count*100);
            return $Sum;
        }
    }

    public static function QuestionHouseSum($question_id,$project_house_id,$question_project_id){
        $Count = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();
        $Total = $Count*5;
        $score = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->sum('replys');
        if($Total==0){
            return $Total;
        }else{
            $Sum =  $score/$Total*100;
            return $Sum;
        }

    }

    public static function discloseNl($question_id,$project_region_id,$question_project_id){
        $DsCount = self::find()
            ->where(['in','replys',[1,2,3]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'project_region_id'=>$project_region_id,
                    'question_id'=>$question_id]
            )->count();

        $Count = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'project_region_id'=>$project_region_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  $DsCount/$Count*100;
            return $Sum;
        }

    }
    public static function linksNl($question_id,$project_region_id,$question_project_id){

        $DsCount = self::find()
            ->where(['in','replys',[4,5]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'project_region_id'=>$project_region_id,
                    'question_id'=>$question_id]
            )->count();

        $Count = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'project_region_id'=>$project_region_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  $DsCount/$Count*100;
            return $Sum;
        }
    }







    public static function disclosePl($question_id,$project_house_id){
        $DsCount = self::find()
            ->where(['in','replys',[1,2,3]])
            ->andFilterWhere([
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        $Count = self::find()
            ->where([
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  $DsCount/$Count*100;
            return $Sum;
        }

    }
    public static function linksPl($question_id,$project_house_id){

        $DsCount = self::find()
            ->where(['in','replys',[4,5]])
            ->andFilterWhere([
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        $Count = self::find()
            ->where([
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  $DsCount/$Count*100;
            return $Sum;
        }
    }




    public static function discloseMl($question_id,$project_house_id,$question_project_id){



        $DsCount = self::find()
            ->where(['in','replys',[1,2,3]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        $Count = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  $DsCount/$Count*100;
            return $Sum;
        }

    }
    public static function linksMl($question_id,$project_house_id,$question_project_id){

        $DsCount = self::find()
            ->where(['in','replys',[4,5]])
            ->andFilterWhere(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        $Count = self::find()
            ->where(['question_project_id'=>$question_project_id,
                    'project_house_id'=>$project_house_id,
                    'question_id'=>$question_id]
            )->count();

        if($DsCount==0 || $Count==0){
            return 0;
        }else{
            $Sum =  $DsCount/$Count*100;
            return $Sum;
        }
    }
    public static function NewChose($project_id,$number)
    {
        $Count = self::find()
            ->where(['project_house_id'=>$project_id,'replys'=>$number,'question_id'=>63]
            )->count();
        return $Count;
    }
    public static function NewCount($project_id)
    {
        $Count = self::find()
            ->where(['project_house_id'=>$project_id]
            )->count();
        return $Count;
    }



    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

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
