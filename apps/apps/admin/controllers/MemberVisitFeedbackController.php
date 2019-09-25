<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/21
 * Time: 17:03
 */

namespace apps\admin\controllers;


use apps\butler\models\Butler;
use common\models\VisitHouseOwnerNotifyLog;
use components\redis\Redis;
use components\wechatSDK\QYWechatSDK;

class MemberVisitFeedbackController extends Controller
{
    private $prefix = 'butlerVisitRegionHouse_';
    protected $missPermission = ['member-visit-feedback/notify'];
    public $enableCsrfValidation = false;

    public function actionNotify()
    {
        $postData = $this->post();
        $houseId = $postData['house_id'];
        $butlerId = $postData['butlerId'];
        $visitHouseOwnerId = $postData['visitHouseOwnerId'];

        if($houseId && $butlerId){
            $prefix = $this->prefix . $butlerId;
            if(Redis::init()->exists($prefix) > 0){
                $zcard = Redis::init()->zcard($prefix) + 1;
                Redis::init()->zadd($prefix, $zcard, $houseId);
            }
        }

        $this->notificationButler($visitHouseOwnerId);

        exit(1);
    }

    /**
     * 企业微信通知管家
     * @param $visitHouseOwnerId
     * @param string $msgtype
     * @param int $agentid
     * @return bool|int|mixed|string
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    private function notificationButler($visitHouseOwnerId, string $msgtype='text',int $agentid=53)
    {
        $butlerModel = VisitHouseOwnerNotifyLog::findOne(['visit_house_owner_id' => $visitHouseOwnerId]);

        if($butlerModel){
            $butlerM = Butler::findOne(['id' => $butlerModel->butler_qywechat_ids]);
            $data = [
                'touser' => $butlerM->wechat_user_id.'|huangqimin',
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "走访业主评价，\n业主【{$butlerModel->visitHouseOwner->member->showName}】，\n手机【{$butlerModel->visitHouseOwner->phone}】，\n房产【{$butlerModel->visitHouseOwner->house->ancestor_name}】，\n综合满意度：【{$butlerModel->visitHouseOwner->satisfaction}星】，\n报事报修满意度：【{$butlerModel->visitHouseOwner->ra_satisfaction}星】，\n清洁绿化满意度：【{$butlerModel->visitHouseOwner->cg_satisfaction}星】，\n管家服务满意度：【{$butlerModel->visitHouseOwner->bs_satisfaction}星】，\n安全管理满意度：【{$butlerModel->visitHouseOwner->sm_satisfaction}星】，\n公共设施维护管理满意度：【{$butlerModel->visitHouseOwner->pu_satisfaction}星】，\n意见：【{$butlerModel->visitHouseOwner->content}】",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            $butlerModel->visitHouseOwner->project_house_id = $butlerM->project_house_id;
            $butlerModel->visitHouseOwner->save();

            if(!empty($res)){
                $butlerModel->delete();
            }

            return $res;
        }

        return 0;
    }

}