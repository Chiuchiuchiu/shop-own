<?php namespace apps\www\controllers;

use common\models\House;
use common\models\MemberHouse;
use common\models\MemberPromotionCode;
use apps\mgt\models\Question;
use common\models\QuestionAnswer;
use common\models\QuestionAnswerItemsDevelop;
use common\models\QuestionMemberDevelop;
use common\models\QuestionAnswerItems;
use common\models\QuestionItem;
use common\models\QuestionProject;
use common\models\QuestionRedPack;
use common\models\QuestionUserChose;
use common\models\SysSwitch;
use yii\web\NotFoundHttpException;

class QuestionController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionPreface(){

        return $this->render('preface');
    }

    /**
     * 开始答卷
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     * @author zhaowenxi
     */
    public function actionIndex($id=0)
    {
        $AnswerItem =  QuestionAnswer::findOne(['id'=>$id]);

        if(!isset($AnswerItem)){

            throw new NotFoundHttpException();

        }else{

            $module = '<div style="text-align: center;margin: 20%;font-size: large;">
                        <h1 style="line-height: 100px;">{str}</h1><br>
                        <button style="width: 50%;height: 5%;background: #188eee;font-size: 40px;color: #fff;border-radius: 30px;text-align: center;" onclick="location.href=\'/\'">返回首页</button>
                      </div>';

            if($AnswerItem->status==1) {
                exit(str_replace("{str}", "该问卷已经回答完毕<br>感谢您的参与！", $module));
            }

            $QuestionProject = QuestionProject::findOne(['id'=>$AnswerItem->question_project_id, 'status' => 1]);

            if(!isset($QuestionProject)){
                exit(str_replace("{str}", "该问卷已经关闭<br>感谢您的参与！", $module));
            }

            $QuestionIn = explode(',',$QuestionProject->content);

            $isOffice = SysSwitch::inVal("projectOffice", $AnswerItem->project_house_id);

            $typeWhere = $isOffice ? ['in', 'type_id', [Question::TYPE_OFFICE, Question::TYPE_PUBLIC]]
                : ['in', 'type_id', [Question::TYPE_HOUSE,Question::TYPE_PUBLIC]];

            $ListArr =  Question::find()->where(['in','id',$QuestionIn])
                ->andFilterWhere($typeWhere)
                ->orderBy('id','desc')
                ->select('id,title,type_isp,type_id')->all();

            return $this->renderPartial('index', [
                'id'=>$id,
                'QuestArr' => $ListArr,
                'QuestionProject'=>$QuestionProject,
                'member' => $this->user,
            ]);

        }
    }

    /**
     * 登记答题页面
     * @param int $question_project_id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @author zhaowenxi
     */
    public function actionWelcome($question_project_id=0,$butler_id=0)
    {
        //未认证用户跳到认证页面
        $MemberHouseItem =  MemberHouse::findOne(['member_id'=>$this->user->id]);

        if(!isset($MemberHouseItem)) return $this->redirect('/auth/?group=1');

        //没有传参，默认状态是开启的调研id
        if(!$question_project_id){
            $questionProject = QuestionProject::findOne(['status' => 1]);

            if(!$questionProject){
                throw new NotFoundHttpException();
            }

            $question_project_id = $questionProject->id;
        }

        $HouseArr = [];

        $MemberHouseList =  MemberHouse::find()->where(['member_id'=>$this->user->id,'status'=>2])->orderBy('house_id','desc')->select('member_id,house_id,status')->all();

        foreach ($MemberHouseList as $v){
           $HouseArr[] = ['house_id'=>$v->house_id,
                          'ancestor_name'=>$v->house->ancestor_name,
                          'house_name'=>$v->house->house_name];
        }

        return $this->renderPartial('welcome', [
           'HouseArr' => $HouseArr,
           'butler_id'=>$butler_id,
           'question_project_id'=>$question_project_id,
           'member' => $this->user,
        ]);

    }

    public function actionWelcomeDevelop(){

        return $this->renderPartial('welcome-develop');
    }

    /**
     * 进入答题前的验证
     * @return string
     * @author zhaowenxi
     */
    public function actionPerfectDevelop(){

        if($this->isAjax){

            $request = \Yii::$app->request->post();

            $Code =0;
            $MreSite = '';

            $member = QuestionMemberDevelop::findOne([
                'phone' => $request['phone'],
            ]);

            if(!$member){
                $MreSite = '找不到相关手机号，请填写正确的联系方式';
                $Code=1;
            }

            if($Code!=0) return $this->renderJsonFail($MreSite);

            $count = QuestionAnswerItemsDevelop::find()->where(['develop_id' => $member->id])->count();

            if($count > 0) return $this->renderJsonFail("您已参与调研，再次感谢您的参与");

            $Url ='/question/develop?id=' . $member->id;

            return  $this->renderJson(['code'=>0,'message'=>'加载中，请稍后','url'=>$Url]);
        }
    }

    /**
     * 开始答卷-开发商
     * @return string
     * @throws NotFoundHttpException
     * @author zhaowenxi
     */
    public function actionDevelop($id)
    {
        $count = QuestionAnswerItemsDevelop::find()->where(['develop_id' => $id])->count();

        $module = '<div style="text-align: center;margin: 20%;font-size: large;">
                        <h1 style="line-height: 100px;">{str}</h1><br>
                        <button style="width: 50%;height: 5%;background: #188eee;font-size: 40px;color: #fff;border-radius: 30px;text-align: center;" onclick="location.href=\'/\'">返回首页</button>
                      </div>';

        if($count){
            exit(str_replace("{str}", "您已经回答完毕<br>感谢您的参与！", $module));
        }

        $ListArr =  Question::find()->where(['type_id' => Question::TYPE_DEVELOP])
            ->orderBy('id','desc')
            ->select('id,title')->all();

        return $this->renderPartial('develop', [
            'QuestArr' => $ListArr,
        ]);

    }

    /**
     * 业主提交答卷
     * @return string
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionDevelopSave()
    {

        $get = \Yii::$app->request->get();

        $MreSite = '';
        $Code =0;
        $ERROID =[];
        $Line = 0;

        foreach ($get['SubjectID'] as $value){
            $Line++;
            if($get['subject_text'.$value]==''){
                $MreSite = $MreSite.'第'.$Line.'题没有回答<br>';
                $ERROID[] = $value;
                $Code=1;
            }

            if($get['subject_text'.$value] < 4 && $get['subject_area'.$value] == ""){
                $MreSite = $MreSite.'第'.$Line.'题，4分以下请先填写评语<br>';
                $ERROID[] = $value;
                $Code=1;
            }
        }

        $isExist = QuestionAnswerItemsDevelop::find()->where(['develop_id' => $get['id']])->count();

        if($isExist > 0){
            $MreSite = '您已参与答题，再次感谢您的支持！';
            $ERROID = [];
            $Code=1;
        }

        if($Code==1){

            return  $this->renderJson(['code'=>1,'message'=>$MreSite,'erroid'=>$ERROID]);

        }else{

            $transaction = QuestionAnswerItemsDevelop::getDb()->beginTransaction();

            try{

                //逐条插入QuestionAnswerItems
                foreach ($get['SubjectID'] as $value){

                    $itemDev = new QuestionAnswerItemsDevelop();
                    $itemDev->score = $get['subject_text' . $value];
                    $itemDev->site = $get['subject_area' . $value];
                    $itemDev->question_id = $value;
                    $itemDev->develop_id = $get['id'];

                    if(!$itemDev->save())
                        throw new \Exception("提交失败（错误提示：answerItem" . $value . "）");
                }

                $transaction->commit();

                return  $this->renderJson(['code'=>0,'message'=>"答题成功，感谢您的参与！"]);

            }catch (\Exception $e){

                $transaction->rollBack();

                return  $this->renderJsonFail($e->getMessage());
            }
        }
    }

    /**
     * 进入答题前的验证
     * @return string
     * @author zhaowenxi
     */
    public function actionPerfect(){

        $request = \Yii::$app->request->post();

        $Code =0;
        $MreSite = '';

        if(trim($request['surname'])==''){
            $MreSite = $MreSite.'请填写业主姓名';
            $Code=1;
        }
        if(trim($request['telephone'])=='' || self::checkPhone($request['telephone']) == 0){
            $MreSite = $MreSite.'请填写正确的联系电话';
            $Code=1;
        }
        if(!isset($request['house_id'])){
            $MreSite = $MreSite.'请先认证房产';
            $Code=1;
        }
        if(!$request['question_project_id'] || !isset($request['question_project_id'])){
            $MreSite = "参数错误";
            $Code=1;
        }

        if($this->user->phone != $request['telephone']){
            $MreSite = "抱歉，您的业主身份有误！请联系管理员";
            $Code=1;
        }

        //是否在筛选名单内
        $isChose = QuestionUserChose::findOne(['house_id' => $request['house_id'], 'telephone' => $request['telephone']]);
        if(!$isChose){
            $MreSite = "抱歉，您没有在本次调研名单中，感谢您的参与！";
            $Code=1;
        }

        //过滤房产数在5套以上的业主，不算车位
//        $houses = MemberHouse::find()->where(['member_id' => $this->user->id, 'group' => 1, 'status' => 2])->count();
//        if($houses > 4){
//            $MreSite = "抱歉，您未达到参与条件，请联系管理员";
//            $Code=1;
//        }

        if($Code!=0) return $this->renderJsonFail($MreSite);

        $QuestionAnswer = QuestionAnswer::findOne([
            'member_house_id'=>$request['house_id'],
            'question_project_id' => $request['question_project_id'],
            "member_id" => $this->user->id,
        ]);

        if(!isset($QuestionAnswer)){

            //查询该项目是否有调研计划
            $houseData = House::findOne($request['house_id']);

            $isExistQuestion = QuestionItem::findOne(['status' => 1, 'project_id' => $houseData->project_house_id]);

            if(!$isExistQuestion) return $this->renderJsonFail("抱歉，您的小区目前没有调研计划！");

            //新认证业主不参与
//            if($isExistQuestion->created_at < $this->user->created_at)
//                return $this->renderJsonFail("抱歉，您未达到本次调研参与条件");

            //$ButlerItem = Butler::findOne(['id'=>$request['butler_id']]);

            //插入一条答题记录
            $answerModel = new QuestionAnswer();
            $answerModel->question_project_id = $request['question_project_id'];
            $answerModel->butler_id = 0; //已去除对管家的绑定
            $answerModel->surname = $request['surname'];
            $answerModel->member_house_id = $request['house_id'];
            $answerModel->project_house_id = $houseData->project_house_id;
            $answerModel->project_region_id = $houseData->project->project_region_id;
            $answerModel->ancestor_name = $houseData->ancestor_name;
            $answerModel->telephone = $request['telephone'];
            $answerModel->member_id = $this->user->id;
            $answerModel->status = 0;
            $answerModel->created_at = date('Y-m-d H:i:s');
            $answerModel->save();

            $QuestionAnswerID = $answerModel->id;

        }else{

            $QuestionAnswerID = $QuestionAnswer->id;
        }

        $Url ='/question/index?id='.$QuestionAnswerID;

        return  $this->renderJson(['code'=>0,'message'=>'跳转中，请稍等','url'=>$Url]);

    }

    /**
     * 业主提交答卷
     * @return string
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionQaSave()
    {

        $Mrequest = \Yii::$app->request->get();

        $MreSite = '';
        $Code =0;
        $ERROID =[];
        $Line = 0;

        foreach ($Mrequest['SubjectID'] as $value){
            $Line++;
            if($Mrequest['subject_text'.$value]==''){
                $MreSite = $MreSite.'第'.$Line.'题没有回答<br>';
                $ERROID[] = $value;
                $Code=1;
            }

            if($Mrequest['subject_text'.$value] < 4 && $Mrequest['subject_area'.$value] == ""){
                $MreSite = $MreSite.'第'.$Line.'题，4分以下请先填写评语<br>';
                $ERROID[] = $value;
                $Code=1;
            }
        }

        if($Code==1){

            return  $this->renderJson(['code'=>1,'message'=>$MreSite,'erroid'=>$ERROID]);

        }else{

            if($Mrequest['subject_text1']>3 && $Mrequest['subject_text2']>3 && $Mrequest['subject_text3']>3){

                $isLoyal =1;

            }else{

                $isLoyal =0;
            }

            $AnswerItem = QuestionAnswer::findOne($Mrequest['id']);

            if($AnswerItem->status==0){

                $CountTotal = 0;

                $ScoreJson = [];

                $transaction = QuestionAnswerItems::getDb()->beginTransaction();

                try{

                    //逐条插入QuestionAnswerItems
                    foreach ($Mrequest['SubjectID'] as $value){

                        $Lib = new QuestionAnswerItems();
                        $Lib->question_project_id = $AnswerItem->question_project_id;
                        $Lib->project_region_id = $AnswerItem->project_region_id;
                        $Lib->project_house_id = $AnswerItem->project_house_id;
                        $Lib->question_answer_id = $Mrequest['id'];
                        $Lib->replys = $Mrequest['subject_text' . $value];
                        $Lib->site = $Mrequest['subject_area' . $value];
                        $Lib->question_id = $value;
                        $Lib->created_at = date('Y-m-d H:i:s');

                        if(!$Lib->save())
                            throw new \Exception("提交失败（错误提示：answerItem" . $value . "）");

                        $CountTotal = $CountTotal+ $Mrequest['subject_text'.$value];

                        $ScoreJson[] = ['question_id'=>$value,'replys'=>$Mrequest['subject_text'.$value]];
                    }

                    //判断是否在question_user_chose名单内
                    $chose = QuestionUserChose::findOne([
                        'telephone' => $AnswerItem->telephone,
                        'house_id' => $AnswerItem->member_house_id,
                        'status' => 1,
                    ]);

                    //更新QuestionAnswer
                    $AnswerItem->is_loyal = $isLoyal;
                    $AnswerItem->question_score = $CountTotal;
                    $AnswerItem->status = 1;
                    $AnswerItem->score_json = serialize($ScoreJson);
                    $AnswerItem->is_chose = isset($chose->id) ? 1 : 0;
                    $AnswerItem->chose_ancestor_name = $AnswerItem->ancestor_name;

                    if(!$AnswerItem->save())
                        throw new \Exception("提交失败（错误提示：questionAnswer）");

                    if($chose){

                        //更新userChose状态
                        $chose->answer_id = $Mrequest['id'];
                        $chose->status = 2;
                        if(!$chose->save())
                            throw new \Exception("提交失败（错误提示：userChose）");
                    }

                    $transaction->commit();

                    //发放5元代金券
//                    $hasCoupon = $this->fiveCoupon($AnswerItem->member_house_id, $AnswerItem->project_house_id);
                    $hasCoupon = 0;

                    return  $this->renderJson(['code'=>0,'message'=>"提交成功，感谢您的参与！", 'hasCoupon' => $hasCoupon]);

                }catch (\Exception $e){

                    $transaction->rollBack();

                    return  $this->renderJsonFail($e->getMessage());
                }
            }
        }
    }

    /**
     * 完成答题发放5块代金券，每户只能领取一次
     * @param $houseId
     * @param $projectId
     * @param $amount
     * @return bool
     */
    private function fiveCoupon($houseId, $projectId, $amount = 5){

        $promotion = MemberPromotionCode::findOne(['house_id' => $houseId]);

        if(!$promotion){

            $promotionModel = new MemberPromotionCode();
            $promotionModel->house_id = $houseId;
            $promotionModel->promotion_code = (string) $houseId;
            $promotionModel->member_id = $this->user->id;
            $promotionModel->promotion_name = 'question';    //答题
            $promotionModel->amount = $amount;

            if($promotionModel->save()){
                $packModel = new QuestionRedPack();
                $packModel->project_id = $projectId;
                $packModel->amount = $amount;
                $packModel->house_id = $houseId;
                $packModel->member_id = $this->user->id;
                $packModel->remark = "答题送代金券";

                return $packModel->save() ? 1 : 0;
            }
        }

        return 0;
    }

    private function http_post($url, $param, $dataFormat = false)
    {
        $strPOST = $param;
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if ($dataFormat) {
            $strPOST = json_encode($param);
        }

        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        curl_setopt($oCurl, CURLOPT_NOSIGNAL, 1);
        curl_setopt($oCurl, CURLOPT_HEADER, 0);

        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);

            return $sContent;

    }
}