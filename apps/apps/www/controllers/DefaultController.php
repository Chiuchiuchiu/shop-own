<?php

namespace apps\www\controllers;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

use apps\api\models\House;
use apps\api\models\MemberHouse;
use common\models\Article;
use common\models\AuthHouseNotificationMember;
use common\models\Banner;
use common\models\Member;
use common\models\Meter;
use common\models\PropertyAnnouncement;
use common\models\QuestionProject;
use common\models\QuestionItem;
use common\models\SysSwitch;
use dosamigos\qrcode\QrCode;
use common\models\Ad;
use common\models\ThirdpartyViewHistory;
use common\models\Project;


class DefaultController extends Controller
{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $this->layout         = 'NewIndex';
        $propertyAnnouncement = PropertyAnnouncement::find()
            ->select('title,id')
            ->where(['status' => PropertyAnnouncement::STATUS_ACTIVE])
            ->andWhere(['project_house_id' => isset($this->project->house_id) ? $this->project->house_id : 0])
            ->limit(5)
            ->orderBy('id DESC')
            ->asArray()->all();

        //begin 活动缴费红包活动（2017-12-24 ~ 2018-02-21）
        $authActivities = \Yii::$app->params['christmas_activities'];
        if (time() <= $authActivities['endTime']) {
            $memberAuthRedPack = AuthHouseNotificationMember::findOne(['member_id' => $this->user->id, 'status' => 0]);

        } else {
            $memberAuthRedPack = false;

        }
        //end
        $BannerList             = Banner::find()->select(['id', 'title', 'pic', 'url'])
            ->filterWhere(['LIKE', 'projects', $this->project->house_id])->orderBy('sort DESC')->all();
        $newDataProvider        = new ActiveDataProvider();
        $newDataProvider->query = Article::find()
            ->where([
                'project_id' => 0,
                //                'show_type'=>[2,1],
                'status'     => Article::STATUS_ACTIVE
            ])->orderBy('post_at DESC');

        //幻灯

        $newsProvider1 = Article::find()->where(['project_id' => 0, 'status' => Article::STATUS_ACTIVE])->orderBy('post_at DESC')->limit(10)->all();
        $newsProvider2 = Article::find()->where(['project_id' => 0, 'category_id' => 426, 'status' => Article::STATUS_ACTIVE])->orderBy('post_at DESC')->limit(10)->all();
        $newsProvider3 = Article::find()->where(['project_id' => 0, 'category_id' => 563, 'status' => Article::STATUS_ACTIVE])->orderBy('post_at DESC')->limit(10)->all();
        $projectId     = isset($this->project->house_id) ? $this->project->house_id : null;
        $navList       = [];

        if (SysSwitch::lxjProject('lxjProject', $projectId)) {
            $aUrl = '<li>
                  <a class="location-lxj" href="javascript: void(0);" data-h="' . Yii::$app->params['lxj_href'] . $this->user->phone . '"><i class="icon"  style="background-color: #FD781D;">
                  <img src="/static/images/ico/menjin.png"  width="28"  height="28"></i>门禁</a></li>';
            array_push($navList, $aUrl);
        }
        if (SysSwitch::inVal('projectParking', $projectId) && SysSwitch::inVal('projectShowParking', $projectId)) {
            $aUrl = '<li>
                  <a class="parking-url"><i class="icon "><img src="/static/images/ico/ico13.png"  width="40"  height="40"></i>停车费</a>
                  </li>';
            array_push($navList, $aUrl);
        }

        $MeterCount = Meter::find()->where(['project_id' => $projectId])->count();


        if(sizeof($navList) < 2 && $MeterCount > 0){
            $aUrl = '<li>
                     <a href="/meter/index">
                         <i class="icon " style="background-color:#6281FB;">
                             <img src="/static/images/ico/shuidian.png" width="28" height="28">
                         </i>
                         <span>水电抄表</span>
                     </a>
                 </li>';
            array_push($navList, $aUrl);
        }
        if(sizeof($navList) < 2){
            $aUrl = '<li>
                     <a href="/life-service/telephone">
                         <i class="icon " style="background-color:#43ADE7;">
                             <img src="/static/images/ico/bianming.png" width="28" height="28">
                         </i>
                         <span>便民电话</span>
                     </a>
                 </li>';
            array_push($navList, $aUrl);
        }

        if(sizeof($navList) < 2){
            $aUrl = '<li>
                     <a href="/article/list/">
                         <i class="icon " style="background-color:#FE6868;">
                             <img src="/static/images/ico/shequdongtai.png" width="28" height="28">
                         </i>
                         <span>社区动态</span>
                     </a>
                 </li>';
            array_push($navList, $aUrl);
        }


        //---------AD ------------
        $ad_list = Ad::find()
            ->where(['type'=>2])
            ->andWhere(['<', 'start_time', time()])
            ->andWhere(['>', 'end_time', time()])
            ->andWhere(['like', 'projects', ",".$projectId .","])
            ->orderBy('sort ASC')
            ->select("id,title,pic,url,diy_json")
            ->all();
     //   var_dump(sizeof($ad_list));die;

        //-------------判断是否有文件调查资格---------feng--------0415--------
        // var_dump($this->project->house_id);die;
        $isShowQuestion = $this->isShowQueFun();
        //-------------end-------------
        $memHouse = MemberHouse::findOne(['member_id' => $this->user->id]);
        $houseID  = 0;
        if ($memHouse != null) {
            $houseID = $memHouse['house_id'];
        }
        $_project = Project::find()->where(['house_id'=>$projectId])->all();
        $_url_key = $_project[0]['url_key'];

        $projectName = $this->project->house_name;
        return $this->render('newIndex', [
            'newDataProvider'      => $newDataProvider,
            'newsProvider1'        => $newsProvider1,
            'newsProvider2'        => $newsProvider2,
            'newsProvider3'        => $newsProvider3,
            'BannerList'           => $BannerList,
            'propertyAnnouncement' => $propertyAnnouncement,
            'projectInfo'          => $this->project,
            'aUrl'                 => $navList,
            'MeterCount'           => $MeterCount,
            'memberAuthRedPack'    => $memberAuthRedPack,
            'isShowQuestion'       => $isShowQuestion,        // 是否显示文件调查
            'houseID'              => $houseID,
            'ad_list'              => $ad_list,
            'pic_host'             => 'https://shop.51homemoney.com',
            'currentPage'          => 'home',
            'pk'                   => $_url_key,
            'pid'                  => $projectId,
            'cdj_header_tip'       => $this->CDJ_TIP,
            'projectName'          => $projectName,
        ]);
    }

    //-----------feng----0415-----------

    private function isShowQueFun()
    {
        $res = false;
        if (!$this->project) {
            $_uid         = $this->user->id;
            $_memberHouse = MemberHouse::findOne(['member_id' => $_uid]);
            if (!$_memberHouse) {
                return $res;
            }
            $_houseId = $_memberHouse->house_id;
            $_House   = House::findOne(['house_id' => $_houseId]);
            if (!$_House) {
                return $res;
            }
            $_project_id = $_House->project_house_id;
        } else {
            $_project_id = $this->project->house_id;
        }
        $_QuestionItem = QuestionItem::findOne(['status' => 1, 'project_id' => $_project_id]);
        //  var_dump($_QuestionItem);die;
        if (!$_QuestionItem) {
            return $res;
        }
        $_Question = QuestionProject::findOne(['id' => $_QuestionItem->question_id]);

        if ($_Question) {
            $s_time = strtotime($_Question->start_date . " 00:00:00");
            $e_time = strtotime($_Question->end_date . " 00:00:00");

            $_curtime = time();

            if ($s_time <= $_curtime && $e_time > $_curtime) {
                $res = true;
            }
        }


        return $res;
    }

    //----------------end---------------

    public function actionMessage($msg = null)
    {
        return $this->render('message', get_defined_vars());
    }

    public function actionUpload()
    {
        $base64_string = $this->post('base64_string');
        $base64_string = base64_decode($base64_string);
        $name          = md5(uniqid() . time() . rand(0, 9999999));
        $name          = strtoupper(base_convert(substr($name, rand(0, 24), 8), 16, 32)) . '.jpg';
        $path          = '/' . date('Wy');
        $savePath      = \Yii::getAlias(\Yii::$app->params['attached.path']) . 'public' . $path;
        if (!file_exists($savePath)) {
            @mkdir($savePath);
        }
        if (!is_dir($savePath)) {
            throw new Exception('can not make dir');
        }
        file_put_contents($savePath . '/' . $name, $base64_string);
        $savePath = '@cdnUrl/' . $path . '/' . $name;
        return $this->renderJsonSuccess(['url' => Yii::getAlias($savePath), 'saveUrl' => $savePath]);
    }

    public function actionAuthCode($phone)
    {
        $member = Member::findOne(['phone' => $phone]);

        if($member) return $this->renderJsonFail("手机号已被注册");

        $code = false && YII_ENV == YII_ENV_DEV ? 1111 : rand(1000, 9999);

        Yii::$app->cache->set('authCode' . $phone,
            [
                'code' => $code,
                'time' => time(),
            ]
            , 60 * 10);
        if (false && YII_ENV == YII_ENV_DEV) {
            $resp['result']['err_code'] = 0;
        } else {
            require(Yii::getAlias('@components/alidayuSDK/TopSdk.php'));
            $c   = new \TopClient(Yii::$app->params['alidayu.app'], Yii::$app->params['alidayu.secret']);
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("财到家");
            $req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"财到家\"}");
            $req->setRecNum($phone);
            $req->setSmsTemplateCode("SMS_25460346");
            $resp = $c->execute($req);
            $resp = json_encode($resp);
            $resp = json_decode($resp, true);
        }
        if (isset($resp['result']['err_code']) && $resp['result']['err_code'] == 0)
            return $this->renderJsonSuccess([]);
        else {
            return $this->renderJsonFail($resp['msg']);
        }
    }

    public function actionAgreement($type = 'user')
    {
        $this->layout = 'empty';
        return $this->render('agreement');
    }

    public function actionQrcode($text)
    {
        QrCode::png(urldecode($text));
        die();
    }

    public function actionError()
    {
        return $this->render('error');
    }

    public function actionAjaxThirdParthViewHistory(){

        $_type      = $this->post('type');
        $_modelv    = $this->post('modelv');
        $_houseId   = $this->post('houseId');
        $_clickFrom = $this->post('clickPlace');
        $_pic       = $this->post('pic');

        $HOUSE = House::findOne(['house_id' => $_houseId]);

        if ($HOUSE) {
            $_projectId = $HOUSE['project_house_id'];
        } else {
            $_projectId = $this->project->house_id ?? 0;
        }

        $model              = new ThirdpartyViewHistory();
        $model->member_id   = $this->user->id;
        $model->house_id    = $_houseId;
        $model->project_id  = $_projectId;
        $model->type        = $_type;
        $model->model       = $_modelv;
        $model->click_place = $_clickFrom;
        $model->pic         = $_pic;
        $model->status      = ThirdpartyViewHistory::STATUS_FAVORITES;
        $model->created_at  = time();

        $res = $model->save();
        return $this->renderJsonSuccess(200, $res);
    }

}
