<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/11/14 16:27
 * Description:
 */

namespace apps\business\controllers;


use apps\butler\models\QuestionProjectButler;
use apps\business\models\Question;
use apps\business\models\QuestionCategory;
use common\models\Butler;
use common\models\Project;
use common\models\ProjectRegion;
use common\models\QuestionAnswer;
use common\models\QuestionAnswerItems;
use common\models\QuestionItem;
use common\models\QuestionProject;
use common\models\QuestionUserChose;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class QuestionController extends Controller
{

    public function actionIndex($search=null)
    {

        $category = $this->get("category_id", null);

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Question::find()
            ->where(['type_isp'=>0])
            ->andFilterWhere(['category_id' => $category])
            ->andFilterWhere(['like','title',$search])
            ->orderBy('id DESC');
        $CateGory = QuestionCategory::find()->select('id, title,parent_id')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('id desc')
            ->all();
        $All =[];
        foreach ($CateGory as $v){
            $All[] = ['id'=>$v['id'],
                'title'=>$v['title'],
                'List'=>QuestionCategory::List($v['id'])
                ];
        }

        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);

    }

    public function actionCreate()
    {
        $model = new Question();
        if($this->isPost && $model->load(\Yii::$app->request->post())){

            $model->created_at = date('Y-m-d H:i:s');
            $model->status = Question::STATUS_ACTIVE;

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($this->isPost && $model->load(\Yii::$app->request->post())) {
            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $res = $this->findModel($id);
        $res->delete();
        return $this->backRedirect(['index']);
    }



    public function actionCategory($search=null)
    {
        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = QuestCate::find()
            ->where(['parent_id'=>0])
            ->andFilterWhere(['like','title',$search])
            ->orderBy('created_at DESC');
        return $this->render('index', [
            'search' => $search,
            'dataProvider' => $dataProvider,
        ]);

    }
    public function actionSampling($question_project_id=0)
    {
        $List = ProjectRegion::find()
            ->where(['status'=>1])
            ->select(['id','name'])
            ->orderBy('id asc')
            ->all();
        $Arr = [];
        $CountRegion =0;
        $CountProject =0;
        $QuestAnswerCount =0;
        foreach($List as $v){
            $CountRegion++;
            $ListArr = [];
            foreach (ProjectRegion::SonProject($v->id) as $row)
            {
                $CountProject++;
                $ProjectCounter = QuestionAnswer::ProjectCount($row->house_id,$question_project_id);

                $QuestAnswerCount = $QuestAnswerCount+ $ProjectCounter;
                $ListArr[] = ['house_name'=>$row->house_name,'ProjectCount'=>$ProjectCounter];
                $ProjectCounter = 0;
            }
            $RegionCounter =  QuestionAnswer::ProjectRegionCount($v->id,$question_project_id);
            $Arr[] = ['id'=>$v->id,'name'=>$v->name,'ListArr'=>$ListArr,'RegionCount'=>$RegionCounter];
        }
        return $this->render('sampling', [
            'dataProvider' => $Arr,
            'CountRegion'=>$CountRegion,
            'QuestAnswerCount'=>$QuestAnswerCount,
            'CountProject'=>$CountProject,
        ]);
    }

    public function actionStatistical($search=null)
    {
        $QuestionProject = QuestionProject::find()
            ->select(['id','title'])
            ->orderBy('id asc')
            ->all();
        return $this->render('statistical',[
            'QuestionProject'=>$QuestionProject
        ]);

    }
    public function actionQuestionQuery(){

        $question_id =0;
        $questionList = Question::find()->select(['id','title'])->orderBy('id','desc')->all();
        $QuestionProject = QuestionProject::find()
            ->select(['id','title'])
            ->orderBy('id asc')
            ->all();
        return $this->render('questionquery',[
            'questionList'=>$questionList,
            'QuestionProject'=>$QuestionProject
            ]);
    }

    public function actionQuestionQueryItem($question_project_id=0,$question_id=0,$region_id=0){
        if($region_id==0){
            $List = ProjectRegion::find()
                ->where(['status'=>1])
                ->select(['id','name'])
                ->orderBy('id asc')
                ->all();
            $RegionArr = [];
            foreach ($List as $v){
                $RegionArr[] = ['id'=>$v->id,'name'=>$v->name,'Score'=>QuestionAnswerItems::QuestionPSum($question_id,$v->id,$question_project_id),'disclose'=>QuestionAnswerItems::discloseNl($question_id,$v->id,$question_project_id),'links'=>QuestionAnswerItems::linksNl($question_id,$v->id,$question_project_id)];
            }
        }else{
            $List = Project::find()
                ->where(['project_region_id'=>$region_id,'status'=>1])
                ->select(['house_id','house_name'])
                ->orderBy('house_id asc')
                ->all();
            foreach ($List as $v){
                   $RegionArr[] = ['id'=>$v->house_id,'name'=>$v->house_name,'Score'=>QuestionAnswerItems::QuestionHSum($question_id,$v->house_id,$question_project_id),'disclose'=>QuestionAnswerItems::disclosePl($question_id,$v->house_id,$question_project_id),'links'=>QuestionAnswerItems::linksPl($question_id,$v->house_id,$question_project_id)];
            }
        }

        $questionLib = Question::findOne(['id'=>$question_id]);
        $personAsc = FoundSort($RegionArr,'Score','SORT_DESC');
        $personDesc = FoundSort($RegionArr,'Score','SORT_ASC');

        return $this->render('queryitem',[
            'personAsc'=>$personAsc,
            'personDesc'=>$personDesc,
            'questionLib'=>$questionLib,
            'region_id'=>$region_id,
            'question_project_id'=>$question_project_id
        ]);
    }


    public function actionQuestionQueryExport($question_project_id=0,$question_id=0,$region_id=0){
        if($region_id==0){
            $List = ProjectRegion::find()
                ->where(['status'=>1])
                ->select(['id','name'])
                ->orderBy('id asc')
                ->all();
            $RegionArr = [];
            foreach ($List as $v){
                $RegionArr[] = ['id'=>$v->id,'name'=>$v->name,'Score'=>QuestionAnswerItems::QuestionPSum($question_id,$v->id,$question_project_id)];
            }
            $TitleName ='分公司,';
        }else{
            $List = Project::find()
                ->where(['project_region_id'=>$region_id,'status'=>1])
                ->select(['house_id','house_name'])
                ->orderBy('house_id asc')
                ->all();
            foreach ($List as $v){
                $RegionArr[] = ['id'=>$v->house_id,'name'=>$v->house_name,'Score'=>QuestionAnswerItems::QuestionHSum($question_id,$v->house_id,$question_project_id)];
            }
            $TitleName ='项目,';
        }
        $questionLib = Question::findOne(['id'=>$question_id]);
        $projectName = $questionLib->site.'详细报表';
        $fileName = $questionLib->site.'详细报表.csv';
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str=$TitleName."评分\n";
        echo mb_convert_encoding($str,'GBK','UTF8');
        foreach ($RegionArr as $keys=>&$row){
            $str = implode(',',[
                $row['name'],
                $row['Score']])."\n";
            echo mb_convert_encoding($str,'GBK','UTF8');
        }
    }




    public function actionLoyal($region_id=null){

        if(!isset($region_id)){
            $topTitle = '分公司用户忠诚度统计';
            $List = ProjectRegion::find()
                ->where(['status'=>1])
                ->select(['id','name'])
                ->orderBy('id asc')
                ->all();
            $RegionArr = [];
            foreach ($List as $v){
                $RegionArr[] = ['id'=>$v->id,'name'=>$v->name,'loyal'=>QuestionAnswer::loyal($v->id)];
            }
            $region_id =0;
        }else{
            $Item = ProjectRegion::findOne(['id'=>$region_id]);
            $topTitle = $Item->name.'用户忠诚度统计';
            $List = Project::find()
                ->where(['status'=>1])
                ->select(['house_id','house_name'])
                ->orderBy('house_id asc')
                ->all();
            $RegionArr = [];
            foreach ($List as $v){
                $RegionArr[] = ['id'=>$v->house_id,'name'=>$v->house_name,'loyal'=>QuestionAnswer::Houseloyal($v->house_id)];
            }

        }
        $RegionAsc = FoundSort($RegionArr,'loyal','SORT_ASC');
        $RegionDesc = FoundSort($RegionArr,'loyal','SORT_DESC');

        return $this->render('loyal',[
            'RegionAsc'=>$RegionAsc,
            'RegionDesc'=>$RegionDesc,
            'topTitle'=>$topTitle,
            'region_id'=>$region_id
        ]);

    }
    public function actionProjectCount(){
        $QuestionProject = QuestionProject::find()->orderBy('id','desc')->all();
        $RegionList = ProjectRegion::find()
            ->where(['status'=>1])
            ->select(['id','name'])
            ->orderBy('id asc')
            ->all();
        return $this->render('project',[
            'QuestionProject'=>$QuestionProject,
            'RegionList'=>$RegionList
        ]);
    }
    public function actionProjectChoose($Region_id=0){

        $QuestionProject = Project::find()
            ->where(['project_region_id'=>$Region_id])
            ->orderBy('house_id','desc')
            ->select(['house_id','house_name'])
            ->asArray()
            ->all();

        return  $this->renderJson(['code'=>0,'houseArr'=>$QuestionProject]);
    }
    public function actionButlerChoose($project_id=0){
        $ButlerList = Butler::find()
            ->where(['project_house_id'=>$project_id,'status'=>1])
            ->asArray()
            ->orderBy('id desc')
            ->select(['id','nickname'])
            ->all();
        return  $this->renderJson(['code'=>0,'ButlerList'=>$ButlerList]);
    }

    public function actionQuestionChoose($question_project_id=0){

        $QuestionProject = QuestionProject::findOne(['id'=>$question_project_id]);
        //question_project_id
        $QuestionIn = explode(',',$QuestionProject->content);

        $QuestionProject = Question::find()
            ->where(['in','id',$QuestionIn])
            ->orderBy('id','desc')
            ->select(['id','title'])
            ->asArray()
            ->all();
        return  $this->renderJson(['code'=>0,'houseArr'=>$QuestionProject]);
    }

    public function actionStatisticalQuery($question_project_id=0,$region_id=0,$project_id=0){
        if($region_id==''){
            $region_id =0;
        }
        $QuestionProject = QuestionProject::findOne(['id'=>$question_project_id]);
        $QuestionIn = explode(',',$QuestionProject->content);
        if($region_id==0 && $project_id==0){
            $ProjectName = '全国';
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionSum($v->id,$question_project_id)];
            }
        }elseif($project_id==0 && $region_id>0){
            $Project = ProjectRegion::findOne(['id'=>$region_id]);
            $ProjectName = $Project->name;
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionPSum($v->id,$region_id,$question_project_id)];
            }
        }else{
            $Project = Project::findOne(['house_id'=>$project_id]);
            $ProjectName = $Project->house_name;
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionHSum($v->id,$project_id,$question_project_id)];
            }
        }


        $HouseArrAsc = FoundSort($HouseArr,'Score','SORT_ASC');
        $HouseArrDesc = FoundSort($HouseArr,'Score','SORT_DESC');
        return $this->render('StatisticalQuery',[
            'HouseArr'=>$HouseArr,
            'QuestionProject'=>$QuestionProject,
            'ProjectName'=>$ProjectName,
            'topTitle'=>'',
            'question_project_id'=>$question_project_id=0,
            'region_id'=>$region_id,
            'project_id'=>$project_id
        ]);


        /*
        if($project_id==0){


            $HouseArrAsc = FoundSort($HouseArr,'Score','SORT_ASC');
            $HouseArrDesc = FoundSort($HouseArr,'Score','SORT_DESC');
            return $this->render('StatisticalQuery',[
                'HouseArr'=>$HouseArr,
                'QuestionProject'=>$QuestionProject,
                'ProjectName'=>$Project->name,
                'topTitle'=>''
            ]);
        }else{
            $Project = Project::findOne(['house_id'=>$project_id]);
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionHouseSum($v->id,$project_id,$question_project_id),'disclose'=>QuestionAnswerItems::discloseMl($v->id,$project_id,$question_project_id),'links'=>QuestionAnswerItems::linksMl($v->id,$project_id,$question_project_id)];
            }
            $HouseArrAsc = FoundSort($HouseArr,'Score','SORT_ASC');
            $HouseArrDesc = FoundSort($HouseArr,'Score','SORT_DESC');
            return $this->render('StatisticalQuery',[
                'HouseArr'=>$HouseArr,
                'QuestionProject'=>$QuestionProject,
                'ProjectName'=>$Project->house_name,
                'topTitle'=>''
            ]);
        }
        */
    }
    public function actionButlerStatistical()
    {

        $QuestionProject = QuestionProject::find()->orderBy('id','desc')->all();
        $RegionList = ProjectRegion::find()
            ->where(['status'=>1])
            ->select(['id','name'])
            ->orderBy('id asc')
            ->all();
        return $this->render('butler-statistical',[
            'QuestionProject'=>$QuestionProject,
            'RegionList'=>$RegionList
        ]);
    }
    public function actionButlerStatisticalQuery($question_project_id=0,$region_id=0,$project_id=0){


        if($region_id==''){
            $region_id =0;
        }
        if($project_id==''){
            $project_id =0;
        }

        $QuestionProject = QuestionProject::findOne(['id'=>$question_project_id]);
        $QuestionProjectButler = QuestionProjectButler::find();

        if($project_id==0 && $region_id>0){
            $Project = ProjectRegion::findOne(['id'=>$region_id]);
            $ProjectName = $Project->name;
            $QuestionProjectButler = $QuestionProjectButler->where(['project_region_id'=>$region_id]);
        }elseif($project_id>0){
            $Project = Project::findOne(['house_id'=>$project_id]);
            $ProjectName = $Project->house_name;
            $QuestionProjectButler = $QuestionProjectButler->where(['project_id'=>$project_id]);
        }else{
            $ProjectName = '全国';
        }
        $QuestionProjectButler = $QuestionProjectButler->orderBy('id','desc')->all();

        $HouseArr = [];
        foreach ($QuestionProjectButler as $value){
                $HouseArr[] = ['id'=>$value->id,'nickname'=>$value->butler->nickname,'numbers'=>QuestionAnswer::butlerNewCount($question_project_id,$value->project_region_id,$value->project_id,$value->butler_id)];
        }

//        $HouseArrAsc = FoundSort($HouseArr,'numbers','SORT_ASC');
//        $HouseArrDesc = FoundSort($HouseArr,'numbers','SORT_DESC');

        return $this->render('butler-statistical-query',[
            'HouseArr'=>$HouseArr,
            'QuestionProject'=>$QuestionProject,
            'ProjectName'=>$ProjectName,
            'question_project_id'=>$question_project_id,
            'region_id'=>$region_id,
            'project_id'=>$project_id
        ]);

    }
    public function actionStatisticalQueryExport($question_project_id=0,$region_id=0,$project_id=0)
    {

        $QuestionProject = QuestionProject::findOne(['id'=>$question_project_id]);
        $QuestionIn = explode(',',$QuestionProject->content);
        if($region_id==0 && $project_id==0){
            $ProjectName = '全国';
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionSum($v->id,$question_project_id)];
            }
        }elseif($project_id==0 && $region_id>0){
            $Project = ProjectRegion::findOne(['id'=>$region_id]);
            $ProjectName = $Project->name;
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionPSum($v->id,$region_id,$question_project_id)];
            }
        }else{
            $Project = Project::findOne(['house_id'=>$project_id]);
            $ProjectName = $Project->house_name;
            $ListArr =  Question::find()->where(['in','id',$QuestionIn])->orderBy('id','desc')->select('id,site,title,type_isp')->all();
            foreach ($ListArr as $v){
                $HouseArr[] = ['id'=>$v->id,'title'=>$v->title,'site'=>$v->site,'Score'=>QuestionAnswerItems::QuestionHSum($v->id,$project_id,$question_project_id)];
            }
        }

        $projectName = $QuestionProject->title.$ProjectName.'详细报表';
        $fileName = $QuestionProject->title.$ProjectName.'详细报表.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="栏目,评分\n";
        echo mb_convert_encoding($str,'GBK','UTF8');
        foreach ($HouseArr as $keys=>&$row){
            $str = implode(',',[
                    $row['site'],
                    $row['Score']])."\n";
            echo mb_convert_encoding($str,'GBK','UTF8');
        }
    }




    protected function findModel($id)
    {
        if (($model = Question::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionUserList($project_id=0)
    {
        $status = \Yii::$app->request->get('status');
        $keywords = \Yii::$app->request->get('keywords');
        $QuestionCount1 = QuestionUserChose::find()->where(['project_id' => $project_id, 'status' => 1])->count();
        $QuestionCount2 = QuestionUserChose::find()->where(['project_id' => $project_id, 'status' => 2])->count();

        $QuestionUserList = QuestionUserChose::find()->where(['project_id' => $project_id]);
        if(isset($keywords) && $keywords<>'') {
            $QuestionUserIn = QuestionUserChose::find()->where(['like', 'username', $keywords]) ->Orwhere(['like', 'house_name', $keywords]) ->Orwhere(['like', 'telephone', $keywords])->select(['id'])->column();
            $QuestionUserList = $QuestionUserList->andWhere(['in','id',$QuestionUserIn]);
        }
         if(isset($status) && $status<>'') {
             $QuestionUserList = $QuestionUserList->andWhere(['status'=>$status]);
         }else{
             $QuestionUserList = $QuestionUserList->andWhere(['>', 'status', 0]);
         }
       $QuestionUserList = $QuestionUserList->orderBy('id DESC');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = $QuestionUserList;
        return $this->render('user-list', ['QuestionUserList' => $QuestionUserList,
            'dataProvider' => $dataProvider,
            'keywords' => $keywords,
            'status' => $status,
            'project_id'=>$project_id,
            'QuestionCount1' => $QuestionCount1,
            'QuestionCount2' => $QuestionCount2]);
    }
    public function actionUserUpdate($id=0,$project_id=0)
    {
        $Items =  QuestionUserChose::findOne(['id'=>$id]);
        if(isset($Items)) {
            $Items->status =1;
            $Items->answer_id =0;
            $Items->save();
            $this->redirect('/question/user-list?project_id='.$project_id);
        }else{
            $this->redirect('/question/user-list?project_id='.$project_id);
        }
    }
}
function FoundSort($array, $field, $sort = 'SORT_DESC')
{
    $arrSort = array();
    foreach ($array as $uniqid => $row) {
        foreach ($row as $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    array_multisort($arrSort[$field], constant($sort), $array);
    return $array;
}
