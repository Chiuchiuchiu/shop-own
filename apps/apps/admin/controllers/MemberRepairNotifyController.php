<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/1/23
 * Time: 17:12
 */

namespace apps\admin\controllers;


use apps\butler\models\Butler;
use apps\butler\models\RepairResponse;
use common\models\NewRepairLog;
use common\models\Repair;
use components\newWindow\NewWindow;
use components\wechatSDK\QYWechatSDK;
use components\wechatSDK\WechatSDK;
use console\models\ReminderLog;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;

class MemberRepairNotifyController extends Controller
{
    protected $missPermission = ['member-repair-notify/notify', 'member-repair-notify/status-notify'];
    public $enableCsrfValidation = false;

    /**
     * @return bool
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNotify()
    {
        $postData = $this->post();
        $repairId = $postData['repairId'];

        //记录文件日志
        $this->fileLog($postData);

        $model = Repair::findOne(['id' => $repairId, 'status' => Repair::STATUS_WAIT]);

        if($model){
            //选择人员列表前，先提交事务到新视窗
            $res = $this->beforeCommitNewWindow($model);
            if(is_array($res)){
                $businessId = isset($res['Response']['Data']['Record'][0]['BusinessID']) ? $res['Response']['Data']['Record'][0]['BusinessID'] : 0;
                $servicesId = isset($res['Response']['Data']['Record'][0]['ServicesID']) ? $res['Response']['Data']['Record'][0]['ServicesID'] : 0;
                $flowID = isset($res['Response']['Data']['Record'][0]['FlowID']) ? $res['Response']['Data']['Record'][0]['FlowID'] : 0;

                RepairResponse::log($model->id, $res['Response']['Data']['NWRespCode'], $res['Response']['Data']['NWErrMsg'], $res['Response']['Data']['Record'], $servicesId, $businessId, $flowID);

                $model->status = $res['Response']['Data']['Record'][0]['ServiceState'];
                $model->reception_user_name = 'cdj';
                if($model->save()){
                    //企业微信通知管家
                    $this->notificationButler($model);

                    //wechat to member
                    $templateData = [
                        $model->flowStyleText,
                        $model->address,
                        $model->content,
                        $model->created_at,
                        $model->statusText,
                    ];
                    $this->sendWxTemplateToMember($model->member->wechat_open_id, $templateData);
                    return true;
                } else {

                    NewRepairLog::writesLog(['id' => $repairId], ['error' => $model->getErrors()]);

                    return false;
                }
            } else {

                NewRepairLog::writesLog(['id' => $repairId], ['error' => $res]);

                $templateData = [
                    $model->flowStyleText,
                    $model->address,
                    $res,
                    $model->created_at,
                    $model->statusText,
                ];
                //能通知财到家开发人员就行
                $this->sendWxTemplateToMember('o4sxcxLBzmvgpO_0BVo9gPbkQWxE', $templateData);

                return false;
            }
        }

        return false;
    }

    /**
     * 发送通知管家
     * @param Repair $repair
     * @param string $msgtype
     * @param int $agentid
     * @return bool|array
     */
    private function notificationButler(Repair $repair, string $msgtype='text',int $agentid=1000002)
    {
        $projectHouseId = $repair->project_house_id;
        $flowId = $repair->repairResponse->flow_id;

        //获取报事流程相关工程维修部人员姓名
        $engNameList = $this->getRepairBindUser($projectHouseId, $flowId);

        //获取报事流程相关管家人员姓名
        $butlerNameList = $this->getRepairBindUser($projectHouseId, $flowId, 1);

        $butlerModel = Butler::find()
            ->select('wechat_user_id')
            ->where(['group' => [1,7], 'status' => Butler::STATUS_ENABLE, 'project_house_id' => $projectHouseId])
            ->asArray()
            ->all();

        if($butlerModel){
            $users = ArrayHelper::getColumn($butlerModel, 'wechat_user_id');
            $users = implode($users,'|');

            $data = [
                'touser' => $users,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "报事报修：类型【{$repair->flowStyleText}】，\n系统已自动提交至新视窗，\n业主【{$repair->name}】，\n手机【{$repair->tel}】，\n地址【{$repair->address}】，\n抄送管家【{$butlerNameList}】，\n抄送工程【{$engNameList}】，\n报修内容【{$repair->content}】\n<a href=\"http://butler.51homemoney.com\">点击查看</a>",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            return $res;
        }

        return 0;
    }

    /**
     * 发送报事模板消息至微信用户
     * @param string $wechatOpenId
     * @param array $templateData
     */
    private function sendWxTemplateToMember($wechatOpenId, $templateData)
    {
        $postData = [
            'touser' => $wechatOpenId,
            'template_id' => '78Mp_ZYMgjs2_g_ozaIBQvgeqO8Cjp1kIrVB94zE4f8',
            'url' => '',
            'data' => [
                'first' => [
                    'value' => $templateData[0] . '进展',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => $templateData[1],
                ],
                'keyword2' => [
                    'value' => $templateData[2],
                ],
                'keyword3' => [
                    'value' => date('Y-m-d H:i:s', $templateData[3]),
                ],
                'keyword4' => [
                    'value' => $templateData[4],
                    'color' => '#173177',
                ],
                'keyword5' => [
                    'value' => '已抄送管家/工程',
                ],
                'remark' => [
                    'value' => '如有疑问，请联系项目管家',
                ],
            ]
        ];
        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);
        $wx_obj->sendTemplateMessage($postData);
    }

    /**
     * 获取报事流程相关人员列表：
     * 获取管家：$IsGetCurrStepUser 为空；获取工程：$IsGetCurrStepUser 为 1
     * @param $projectHouseId
     * @param $flowId
     * @param null $IsGetCurrStepUser
     * @return array
     * @throws \yii\base\ErrorException
     */
    private function getRepairBindUser($projectHouseId, $flowId, $IsGetCurrStepUser=null)
    {
        $userLists =[];
        $userNames = [];
        $res = (new NewWindow())->getRepairBindUser($projectHouseId, 0, $flowId, $IsGetCurrStepUser);
        if (isset($res['Response']['Data']['Record'])) {
            $userLists = $res['Response']['Data']['Record'];
            if(is_array($userLists)){
                foreach($userLists as $key => $value){
                    array_push($userNames, $value['UserName']);
                }
            }
        }

        if(!empty($userNames)){
            $userNames = implode(',', $userNames);
        } else {
            $userNames = '-';
        }

        return $userNames;
    }

    /**
     * 先提交到视窗，获取 FlowID，下一步获取人员列表
     * @param Repair $repairModel
     * @param int $ActionFlag
     * @return mixed|string
     * @throws \yii\base\ErrorException
     */
    protected function beforeCommitNewWindow(Repair $repairModel, $ActionFlag=0)
    {
        $pics = [];

        //获取CustomerID，有偿维修需要带上 zhaowenxi
        /*$houseInfo = (new NewWindow())->getHouseInfo($repairModel->house_id);

        if($repairModel->flow_style_id == 'w' && ($houseInfo['Response']['Data']['NWRespCode'] !== '0000' || empty($houseInfo['Response']['Data']['Record']))){
            return $houseInfo['Response']['Data']['NWErrMsg'] ?? "业主信息没有在新视窗与房产作关联";
        }*/

        $data = [
            'ActionFlag' => $ActionFlag,
            'ContactAddress' => $repairModel->house->ancestor_name,
            'CustomerRoomID' => $repairModel->house_id,
            'ContactPhone' => $repairModel->tel,
            'Content' => isset($repairModel->content) ? $repairModel->content : '',
            'ContactName' => $repairModel->name,
            'CustomerName' => $repairModel->name,
//            'CustomerID' => $houseInfo['Response']['Data']['Record'][0]['CustomerID'],
            'PrecinctID' => !empty($repairModel->project_house_id) ? $repairModel->project_house_id : $repairModel->house->project_house_id,
            'Site' => $repairModel->site,    //1|2
            'StyleID' => $repairModel->flow_style_id,
            'ServiceKind' => 0,
            'Sources' => Repair::SOURCES_WECHAT,
        ];

        if(isset($repairModel->pics)){
            $pic = explode(',', $repairModel->pics);
            foreach ($pic as $row) {
                $pics[] = [
                    'FileName' => pathinfo(\Yii::getAlias($row), PATHINFO_BASENAME),
                    'URL' => \Yii::getAlias($row),
                ];
            }
            $data['FileInfo'] = $pics;
        }

        $postData = [$data];
        $res = (new NewWindow())->postRepair($postData);

        //记录参数 zhaowenxi
//        NewRepairLog::writesLog($data, $res);

        return !empty($res['Response']['Data']['Record']) ? $res : $res['Response']['Data']['NWErrMsg'];
    }

}