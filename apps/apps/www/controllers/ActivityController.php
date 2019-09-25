<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/1 16:40
 * Description:
 */

namespace apps\www\controllers;


use apps\mgt\models\FpzzLog;
use common\models\Activity;
use common\models\Butler;
use common\models\Member;
use common\models\SignUp;
use common\models\MemberHouse;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

use components\wechatSDK\QYWechatSDK;
use yii\helpers\ArrayHelper;


class ActivityController extends Controller
{
    public function actionIndex($id=0)
    {
        $ItemsLib = Activity::findOne(['id'=>$id]);

        //活动已结束
        if($ItemsLib->status == 1){
            return $this->renderPartial('activity-not', [
                'member' => $this->user,
                'Items'=>$ItemsLib
            ]);
        }

        //用户已参加活动
        $SignUpFind = SignUp::find()->where(['activity_id'=>$id,'member_id'=>$this->user->id])->one();
        if(isset($SignUpFind)){
            return $this->redirect('/activity/comment?id='.$SignUpFind->id);
        }

        $Arr1 = $this->optionsFun($ItemsLib->options1);
        $Arr2 = $this->optionsFun($ItemsLib->options2);

        $ItemsLib->click_numbers += 1;
        $ItemsLib->save();

        //用户未认证房产
        $MemberHouseItem =  MemberHouse::findOne(['member_id'=>$this->user->id]);
        if(!isset($MemberHouseItem)){
            $ItemsLib->auth_numbers += 1;
            $ItemsLib->save();
            return $this->redirect('/auth/?group=1&project_id='.$ItemsLib->project_id);
        }

        return $this->renderPartial('activity', [
            'member' => $this->user,
            'Items'=>$ItemsLib,
            'Arr1'=>$Arr1,
            'Arr2'=>$Arr2
        ]);
    }

    public function actionSignUp($id=0)
    {



        $Items = Activity::findOne(['id'=>$id]);

        $Request = \Yii::$app->request->get();
        $Code =0;
        $MreSite = '';
        if($Request['surname']==''){
            $MreSite = $MreSite.'请填写真实姓名';
            $Code=1;
        }
        if($Request['telephone']==''){
            $MreSite = $MreSite.'请填写联系电话';
            $Code=1;
        }
        if($Code==0){
            $Items = Activity::findOne(['id'=>$id]);
            $MemberHouseFirst=  MemberHouse::find()->where(['member_id'=>$this->user->id,'status'=>2])->orderBy('house_id desc')->select('member_id,house_id,status')->one();
            $Count = SignUp::find()->where(['activity_id'=>$id,'telephone'=>$Request['telephone']])->count();
            if($Count==0){
            $lIB = new SignUp();
            $lIB->uid = date("YmdHis").mt_rand(1000000,9999999);
            $lIB->surname = $Request['surname'];
            $lIB->telephone = $Request['telephone'];
            $lIB->project_id = $Items->project_id;
            $lIB->house_id = $MemberHouseFirst->house->house_id;
            $lIB->ancestor_name = $MemberHouseFirst->house->ancestor_name;
            $lIB->activity_id = $Items->id;

            //菊姐说露天电影不用填site

            $lIB->site = $Request['site'];

            $lIB->member_id = $this->user->id;
            if($Items->options1<>''){
                $lIB->options1 = $Request['options1'];
            }
            if($Items->options2<>''){
                $lIB->options2 = $Request['options2'];
            }

            $lIB->status = 1;
            $lIB->created_at = time();
            $lIB->save();
            $this->ActivityMsg($lIB->id);
            $Url ='/activity/sign-up-ok?id='.$id;
            return  $this->renderJson(['code'=>0,'msg'=>'报名/登记成功，请稍等','url'=>$Url]);


            }else{
                return  $this->renderJson(['code'=>1,'msg'=>'您已经报名了，请不要重复报名']);
            }
        }else{
            return  $this->renderJson(['code'=>1,'msg'=>$MreSite]);
        }
    }
    public function actionSignUpOk($id=0)
    {
        $Items = Activity::findOne(['id'=>$id]);
        return $this->renderPartial('sign-up-ok', [
            'member' => $this->user,
            'Items'=>$Items
        ]);
    }
    public function actionComment($id=0)
    {
        $SignUp = SignUp::findOne(['id'=>$id]);
        if($SignUp->status==2){
            $Url ='/activity/comment-yes?id='.$id;
            return $this->redirect($Url);
            exit();
        }


        $Activity = Activity::findOne(['id'=>$SignUp->activity_id]);
        $TagList = explode(',',$Activity->comment_tag);
        return $this->renderPartial('comment', [
            'member' => $this->user,
            'TagList'=>$TagList,
            'Activity'=>$Activity,
            'SignUp'=>$SignUp
        ]);
    }
    public function actionCommentYes($id=0)
    {
        $SignUp = SignUp::findOne(['id'=>$id]);
        $Activity = Activity::findOne(['id'=>$SignUp->activity_id]);
        return $this->renderPartial('comment-yes', [
            'member' => $this->user,
            'Activity'=>$Activity,
            'SignUp'=>$SignUp
        ]);
    }
    private function ActivityMsg($id){
        $Items = SignUp::findOne(['id'=>$id]);
        $Activity = Activity::findOne(['id'=>$Items->activity_id]);
        $project_id = $Items->project_id;
        $butlerModel = Butler::find()->select('wechat_user_id')->where(['group' => [1], 'status' => Butler::STATUS_ENABLE, 'project_house_id' => $project_id])->asArray()->all();
        if($butlerModel){
            $users = ArrayHelper::getColumn($butlerModel, 'wechat_user_id');
            $users = implode($users,'|');
        }
        $ancestor_name = $Items->ancestor_name;
        $options1='';

        $options2='';
        /*
        if($Activity->options1<>''){
            $Action  = $this->optionsFun($Activity->options1);
            $options1 = $Action[0].": ".$Items->options1."\n";
        }
        if($Activity->options2<>''){
            $Action  = $this->optionsFun($Activity->options2);
            $options2 = $Action[0].": ".$Items->options2."\n";
        }*/

        $content = "活动名称:【".$Activity->title."】\n用户姓名:【".$Items->surname."】\n联系电话:【".$Items->telephone."】".$options1.$options2."\n房产信息:【".$Items->ancestor_name."】\n备注详细:【".$Items->site."】\n<a href=\"http://butler.51homemoney.com/activity/list?id=".$Activity->id."\">点击查看</a>";
        $data = [
            'touser' => $users,
            'msgtype' => 'text',
            'agentid' => '53', //中奥通讯录应用 ID
            'text' => [
                'content' => $content
            ],
            'safe' => 0,
        ];
        $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
        $res = $wechatQYSDK->sendMsg($data);

    }
    public function actionCommentSave($id=0,$starNumber=0,$sign_up_id=0,$Comment='')
    {
        $SignUp = SignUp::findOne(['id'=>$sign_up_id]);
        if($SignUp->status==1)
        {
            $SignUp->status=2;
            $SignUp->star_number=$starNumber;
            $SignUp->comment=$Comment;
            $SignUp->save();
            $Url ='/activity/comment-yes?id='.$sign_up_id;
            return  $this->renderJson(['code'=>0,'message'=>'感谢您的参与','url'=>$Url]);
        }else{
            return  $this->renderJson(['code'=>1,'message'=>'您已经评论过了，请不要重复']);
        }
    }
    public function optionsFun($str)
    {
        $Arrnumber = array();
        $Arr = explode(':',$str);
        if(count($Arr)==2){
            $ArrOne = explode('|',$Arr[1]);
            return array('title'=>$Arr[0],'List'=>$ArrOne);
        }else{
            return array(0);
        }
    }
}