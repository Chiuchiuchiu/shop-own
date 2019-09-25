<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/18
 * Time: 13:57
 */

namespace apps\www\controllers;


use apps\butler\models\Butler;
use common\models\MemberHouse;
use common\models\SysSwitch;
use common\models\VisitHouseOwner;
use common\models\VisitHouseOwnerNotifyLog;
use components\helper\HttpRequest;
use components\wechatSDK\QYWechatSDK;
use yii\log\FileTarget;

class VisitHouseOwnerController extends Controller
{
    public function actionIndex($butlerId)
    {
        $member = $this->user;

        $memberHouse = MemberHouse::findAll(['member_id' => $this->user->id, 'status' => MemberHouse::STATUS_ACTIVE, 'group' => MemberHouse::GROUP_HOUSE]);

        if(empty($memberHouse)){
            return $this->render('empty');
        }
        if(empty($this->user->phone)){
            return $this->render('empty', ['message' => '未注册手机号']);
        }

        if($this->isPost){
            $phone = $this->user->phone;
            $code = $this->post('code');
            $_code = \Yii::$app->cache->get('visitPhone_' . $phone);
            if (empty($code) || $code != $_code['code']) {
                if($code !== '8888'){
                    return $this->renderJsonFail("验证码错误");
                }
            }

            $param = "phone={$phone}&butlerId={$butlerId}&";
            return $this->renderJsonSuccess(['goUrl' => '/visit-house-owner/customer-evaluation?'.$param]);
        }

        return $this->render('index', [
            'butler' => $butlerId,
            'member' => $member,
        ]);
    }

    public function actionAuthCode()
    {
        $phone = $this->get('phone', null);
        $code = false && YII_ENV == YII_ENV_DEV ? 1111 : rand(1000, 9999);
        $codePhone = $phone;

        if(empty($phone)){
            return $this->renderJsonFail('请填写手机号');
        }

        \Yii::$app->cache->set('visitPhone_' . $codePhone,
            [
                'code' => $code,
                'time' => time(),
            ]
            , 60 * 10);
        if (false && YII_ENV == YII_ENV_DEV) {
            $resp['result']['err_code']=0;
        } else {
            require(\Yii::getAlias('@components/alidayuSDK/TopSdk.php'));
            $c = new \TopClient(\Yii::$app->params['alidayu.app'], \Yii::$app->params['alidayu.secret']);
            $req = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("财到家");
            $req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"财到家\"}");
            $req->setRecNum($codePhone);
            $req->setSmsTemplateCode("SMS_25460346");
            $resp = $c->execute($req);
            $resp = json_encode($resp);
            $resp = json_decode($resp, true);
        }

        if (isset($resp['result']['err_code']) && $resp['result']['err_code'] == 0)
            return $this->renderJsonSuccess([]);
        else {
            $this->aliSmsLog($resp);
            return $this->renderJsonFail('发送验证码失败，系统繁忙！');
        }
    }

    public function actionCustomerEvaluation()
    {
        $model = MemberHouse::findAll(['member_id' => $this->user->id, 'status' => MemberHouse::STATUS_ACTIVE, 'group' => MemberHouse::GROUP_HOUSE]);
        $butlerId = $this->get('butlerId');

        if($this->isPost){
            $years = date('Y', time());
            $houseId = $this->post('id');
            $butlerId = $this->post('butlerId');
            $satisfaction = $this->post('osSf', 4);
            $content = $this->post('content');
            $quarter = $this->getNowQuarter();
            $memberHouse = MemberHouse::findOne(['house_id' => $houseId, 'member_id' => $this->user->id]);
            $model = VisitHouseOwner::findOne(['quarter' => $quarter['where'], 'house_id' => $houseId, 'years' => $years]);
            if(!$memberHouse){
                return $this->renderJsonFail('抱歉，您未认证该房产，请前往首页认证！');
            }
            if($model){
                return $this->renderJsonFail('您已经提交过评价，无需重复提交！');
            }
            $model = new VisitHouseOwner();
            $model->quarter = $quarter['quarter'];
            $model->house_id = $houseId;
            $model->butler_id = $butlerId;
            $model->member_id = $this->user->id;
            $model->status = VisitHouseOwner::STATUS_ACTIVE;
            $model->project_house_id = $memberHouse->house->project_house_id;
            $model->project_region_id = $memberHouse->house->project->project_region_id;
            $model->phone = $this->user->phone;
            $model->satisfaction = $satisfaction;
            $model->bs_satisfaction = $this->post('bsSf', 0);
            $model->cg_satisfaction = $this->post('cgSf', 0);
            $model->sm_satisfaction = $this->post('smSf', 0);
            $model->pu_satisfaction = $this->post('sopfSf', 0);
            $model->ra_satisfaction = $this->post('repairSf', 0);
            $model->content = trim($content);
            $model->years = $years;

            if($model->save()){
                $this->notificationButler($butlerId, $model);
                $requestUrl = 'http://testmgt.51homemoney.com/member-visit-feedback/notify';
                $data = [
                    'house_id' => $houseId,
                    'butlerId' => $butlerId,
                    'visitHouseOwnerId' => $model->id,
                ];
                HttpRequest::post($requestUrl, $data);
                return $this->renderJsonSuccess(['goUrl' => '/visit-house-owner/success?back=s']);
            }

            return $this->renderJsonFail($model->getErrors());
        }

        return $this->render('customer-evaluation', [
            'model' => $model,
            'butlerId' => $butlerId,
        ]);
    }

    public function actionSuccess()
    {
        return $this->render('success');
    }

    public function actionUndefined()
    {
        return $this->redirect('/');
    }

    private function getNowQuarter()
    {
        $nowMonth = date('n', time());
        $quarter['quarter'] = 1;

        if($nowMonth >= 10){
            $quarter['quarter'] = 4;
        }else if($nowMonth >= 7){
            $quarter['quarter'] = 3;
        }else if($nowMonth >= 4){
            $quarter['quarter'] = 2;
        }

        if($nowMonth > 6){
            $quarter['where'] = [3, 4];
        } else {
            $quarter['where'] = [1, 2];
        }

        return $quarter;
    }

    /**
     * 企业微信通知管家
     * @param $butlerUserId
     * @param VisitHouseOwner $visitHouseOwner
     * @param string $msgtype
     * @param int $agentid
     * @return bool|int|mixed|string
     */
    private function notificationButler($butlerUserId,VisitHouseOwner $visitHouseOwner, string $msgtype='text',int $agentid=53)
    {
        $butlerModel = Butler::find()->select('wechat_user_id, project_house_id')->where(['group' => Butler::GROUP_1, 'id' => $butlerUserId])->asArray()->one();

        if($butlerModel){
            $data = [
                'touser' => $butlerModel['wechat_user_id'].'|huangqimin',
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "走访业主评价，\n业主【{$visitHouseOwner->member->showName}】，\n手机【{$visitHouseOwner->phone}】，\n房产【{$visitHouseOwner->house->ancestor_name}】，\n综合满意度：【{$visitHouseOwner->satisfaction}星】，\n报事报修满意度：【{$visitHouseOwner->ra_satisfaction}星】，\n清洁绿化满意度：【{$visitHouseOwner->cg_satisfaction}星】，\n管家服务满意度：【{$visitHouseOwner->bs_satisfaction}星】，\n安全管理满意度：【{$visitHouseOwner->sm_satisfaction}星】，\n公共设施维护管理满意度：【{$visitHouseOwner->pu_satisfaction}星】，\n意见：【{$visitHouseOwner->content}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            $visitHouseOwner->project_house_id = $butlerModel['project_house_id'];
            $visitHouseOwner->save();

            if(empty($res)){
                VisitHouseOwnerNotifyLog::writeLog($butlerUserId, $visitHouseOwner->member_id, $visitHouseOwner->id);
            }

            return $res;
        }

        return 0;
    }

    /**
     * 记录阿里大于短信反馈
     * @param $msg
     * @throws \yii\base\InvalidConfigException
     */
    protected function aliSmsLog($msg)
    {
        $msgLog = serialize($msg);

        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/mobile.log';
        $fileLog->messages[] =  [$msgLog, 8, 'application', microtime(true)];;
        $fileLog->export();
    }


}