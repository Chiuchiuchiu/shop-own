<?php namespace apps\www\controllers;

use apps\www\models\Member;
use apps\www\models\WechatRedPackLog;
use common\models\MemberHouse;
use common\models\MinAutumnQuestion;
use common\models\MinAutumnRedPack;
use common\models\Project;
use common\models\WechatRedPack;
use components\wechatSDK\WechatSDK;
use yii\helpers\ArrayHelper;

/**
 * 中秋答题活动
 * Class MinAutumnController
 * @package apps\www\controllers
 */
class MinAutumnController extends Controller
{
    public $enableCsrfValidation = false;

    public $projectRegion = [1, 2, 3, 4];   //参与活动的分公司

    /**
     * 开始答卷
     * @return string
     * @author zhaowenxi
     */
    public function actionIndex()
    {
        //todo::活动有效期过后要手动关闭
        $module = '<div style="text-align: center;margin: 20%;font-size: large;">
                        <h1 style="line-height: 100px;">活动已结束，感谢您的参与！</h1><br>
                        <button style="width: 50%;height: 5%;background: #188eee;font-size: 40px;color: #fff;border-radius: 30px;text-align: center;" onclick="location.href=\'/\'">返回首页</button>
                      </div>';
        exit($module);

        if(!$this->checkUser()) return $this->redirect("/auth?group=1");

        $list =  MinAutumnQuestion::find()
            ->where(['status' => 1])
            ->select('id,title,answer')
            ->orderBy("RAND()")
            ->limit(MinAutumnRedPack::QUESTION_TOTAL)
            ->all();

        return $this->renderPartial('index', [
            'list' => $list,
            'houseId' => $this->get('h'),
            'totalCount' => MinAutumnRedPack::QUESTION_TOTAL,
            'qrCode' => "/static/images/cdj-qr.jpg"
        ]);
    }

    /**
     * 登记答题页面
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionWelcome()
    {
        //未认证用户跳到认证页面
        $MemberHouseItem = MemberHouse::findOne(['member_id'=>$this->user->id]);

        if(!isset($MemberHouseItem) || !$this->checkUser()) return $this->redirect('/auth/?group=1');

        $HouseArr = [];

        $MemberHouseList =  MemberHouse::find()
            ->where(['member_id'=>$this->user->id,'status'=>2])
            ->orderBy('house_id','desc')
            ->select('member_id,house_id,status')
            ->all();

        foreach ($MemberHouseList as $v){
           $HouseArr[] = ['house_id'=>$v->house_id,
                          'ancestor_name'=>$v->house->ancestor_name,
                          'house_name'=>$v->house->house_name];
        }

        return $this->renderPartial('welcome', [
           'HouseArr' => $HouseArr,
           'member' => $this->user,
        ]);

    }

    /**
     * 进入答题前的过滤
     * @return string
     * @author zhaowenxi
     */
    public function actionPerfect(){

//        return  $this->renderJson(['code'=>1,'message'=>'活动已结束，感谢您的参与！']);

        //统计已领红包多少个
        $totalPass = MinAutumnRedPack::find()->where(['status' => 2])->count();

        if($totalPass >= MinAutumnRedPack::RED_PACK_TOTAL)
            return $this->renderJsonFail('红包被抢光了！感谢您参与！');

        $request = \Yii::$app->request->post();

        $code = 0;
        $MreSite = '';

        if($request['surname']==''){
            $MreSite = '请填写业主姓名';
            $code    = 1;
        }
        if($request['telephone']==''){
            $MreSite = '请填写联系电话';
            $code    = 1;
        }
        if(!isset($request['house_id'])){
            $MreSite = '请先认证房产';
            $code    = 1;
        }

        //判断所属小区是否有参与红包活动
        $projectIds = ArrayHelper::getColumn($this->getHouseByProjectRegion(), 'house_id');
        if(!in_array($this->project->house_id, $projectIds)){
            $MreSite = "抱歉，小区未参与本次猜灯谜活动";
            $code    = 1;
        }

        //判断用户信息
        $userInfo  = Member::findOne(['phone' => $request['telephone'], 'id' => $this->user->id]);
        $houseInfo = MemberHouse::findOne([
            'member_id' => $this->user->id,
            'house_id'  => $request['house_id'],
            'status'    => MemberHouse::STATUS_ACTIVE
        ]);

        if(!$userInfo || !$houseInfo){
            $MreSite = "抱歉，没有找到业主信息！";
            $code    = 1;
        }

        //判断业主是否已参与
        $hadAnswered = $this->checkRedPack($request['house_id']);

        if(isset($hadAnswered) && $hadAnswered->status != 1){
            $MreSite = "您完成答题，再次感谢您的参与！";
            $code    = 1;
        }

        if(!$hadAnswered){
            //插入一条记录
            $model             = new MinAutumnRedPack();
            $model->house_id   = $request['house_id'];
            $model->member_id  = $this->user->id;
            $model->project_id = $this->project->house_id;
            $model->sure_name  = $request['surname'];
            $model->status     = MinAutumnRedPack::STATUS_WAIT;
            $model->created_at = time();

            if(!$model->save()){
                $MreSite = "服务器错误，请联系管理员";
                $code    = 1;
            }
        }

        if($code != 0) return $this->renderJsonFail($MreSite);

        $url = '/min-autumn/index?h=' . $request['house_id'];

        return  $this->renderJson(['code'=>0,'message'=>'跳转中，请稍等','url'=>$url]);

    }

    /**
     * 业主提交答题ajax
     * @return string
     * @throws \yii\db\Exception
     * @author zhaowenxi
     */
    public function actionQaSave()
    {
//        return  $this->renderJson(['code'=>1,'message'=>'活动已结束，感谢您的参与！']);

        $get = \Yii::$app->request->get();

        $line = 0;
        foreach ($get['SubjectID'] as $value){
            $line++;
            if($get['subject_text'.$value]==''){
                return $this->renderJsonFail('第'.$line.'题没有回答<br>');
            }
        }

        //判断做对多少题
        $answerTrue = MinAutumnQuestion::find()->select('id,answer_true')->where(['status' => 1])->all();

        $trueNum = 0;
        
        //记录业主提交的答案
        $userAnswer = [];
        
        foreach ($answerTrue as $v){
            if(isset($get['subject_text' . $v->id])){
                ((int)$get['subject_text' . $v->id] == $v->answer_true) && $trueNum++;
                $userAnswer[$v->id] = $get['subject_text' . $v->id];    
            }
        }

        //统计已领红包多少个
        $totalPass = MinAutumnRedPack::find()->where(['status' => 2])->count();

        if($totalPass >= MinAutumnRedPack::RED_PACK_TOTAL)
            return $this->renderJsonFail('红包被抢光了！感谢您参与！');

        //是否已领红包
        $find = $this->checkRedPack($get['house_id']);

        if(isset($find->wechat_mch_id) && $find->wechat_mch_id > 0)
            return $this->renderJsonFail('红包已经领过了！再次感谢您参与！');

        if(isset($find->status) && $find->status != MinAutumnRedPack::STATUS_WAIT)
            return $this->renderJsonFail('您已完成答题！再次感谢您参与！');

        //是否满足发送红包条件
        $passOrFail = true;

        ($trueNum < MinAutumnRedPack::QUESTION_PASS) && $passOrFail = false;

        $transaction = MinAutumnRedPack::getDb()->beginTransaction();

        $redMsg = "<br>本次未能答对" . MinAutumnRedPack::QUESTION_PASS . "条以上，红包不发放，请继续努力";

        try{

            //更新min_autumn_red_pack状态
            $find->updated_at    = time();
            $find->answer        = json_encode($userAnswer);
            $find->status        = MinAutumnRedPack::STATUS_VOID;

            if(!$find->save()) throw new \Exception("更新失败（错误提示：save1）");

            //答对3题以上，发送微信红包
            if($passOrFail){

                $redMsg = "<br>红包正在发送到您的微信，请注意查收！";

                $amount = 1;

                $redPackNumber = WechatRedPack::createNumber();

                $res = WechatSDK::sendRedPack($this->user->wechat_open_id, $redPackNumber, $amount,1,'祝您中秋快乐！','祝您中秋快乐！');

                if($res['return_code'] == 'SUCCESS'){

                    $wxRedPackModel                   = new WechatRedPack();
                    $wxRedPackModel->number           = $redPackNumber;
                    $wxRedPackModel->member_id        = $this->user->id;
                    $wxRedPackModel->amount           = $amount;
                    $wxRedPackModel->pm_order_id      = '1';    //必须为字符串
                    $wxRedPackModel->house_id         = $get['house_id'];
                    $wxRedPackModel->project_house_id = $this->project->house_id;
                    $wxRedPackModel->even_name        = 20180924;
                    $wxRedPackModel->even_key         = 1;
                    $wxRedPackModel->remark           = '中秋猜灯谜送红包';
                    $wxRedPackModel->created_at       = time();
                    $wxRedPackModel->completed_at     = time();
                    $wxRedPackModel->status           = WechatRedPack::STATUS_SEND;
                    $wxRedPackModel->result           = serialize($res);

                    if(!$wxRedPackModel->save()){
                        throw new \Exception("记录失败（错误提示：wxRed）");
                    }

                    $find->wechat_mch_id = $redPackNumber;
                    $find->amount        = $amount * 100;
                    $find->status        = MinAutumnRedPack::STATUS_PASS;
                }

                self::writeWechatRedLog($this->user->id, 1, $redPackNumber, $res);

            }else{

                $find->status = MinAutumnRedPack::STATUS_FAIL;
            }

            //更新min_autumn_red_pack状态
            $find->updated_at = time();
            if(!$find->save()) throw new \Exception("更新失败（错误提示：save2）");

            $transaction->commit();

            $msg = "总共" . MinAutumnRedPack::QUESTION_TOTAL . "道题目，答对" . $trueNum . "道。";
            $msg .= $redMsg;

            return $this->renderJson(['code' => 0,'message' => $msg]);

        }catch (\Exception $e){

            $transaction->rollBack();

            return  $this->renderJsonFail($e->getMessage());
        }
    }

    /**
     * 微信红包log日志
     * @param $memberId
     * @param $amount
     * @param $number
     * @param $result
     * @return bool
     * @author zhaowenxi
     */
    protected static function writeWechatRedLog($memberId, $amount, $number, $result)
    {
        return WechatRedPackLog::writeLog($memberId, $amount, $number, $result);
    }

    /**
     * 验证用户是否有认证房产和业主
     * @return bool
     * @author zhaowenxi
     */
    protected function checkUser(){

        $bool = true;

        (isset($this->user->id) && $this->user->id) || $bool = false;

        (isset($this->project->house_id) && $this->project->house_id) || $bool = false;

        return $bool;
    }

    /**
     * 查询用户答题记录
     * @param $houseId
     * @return MinAutumnRedPack|null
     * @author zhaowenxi
     */
    protected function checkRedPack($houseId){
        return MinAutumnRedPack::findOne(['member_id' => $this->user->id, 'house_id' => $houseId]);
    }

    /**
     *
     * @return array|\yii\db\ActiveRecord[]
     * @author zhaowenxi
     */
    protected function getHouseByProjectRegion(){
        return Project::find()
            ->where(['IN', 'project_region_id', $this->projectRegion])
            ->andWhere(['status' => 1])
            ->select('house_id')
            ->all();
    }
}