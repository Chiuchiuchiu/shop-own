<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/5/6
 * Time: 9:26
 */

namespace apps\www\controllers;


use common\models\Butler;
use apps\mgt\models\FpzzLog;
use apps\www\models\RepairCancel;
use common\models\MemberHouse;
use common\models\QyWeixinNotifyLog;
use common\models\Repair;
use common\models\RepairCustomerEvaluation;
use common\models\RepairResponse;
use components\helper\HttpRequest;
use components\newWindow\NewWindow;
use components\wechatSDK\QYWechatSDK;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class NewRepairController extends Controller
{
    public function actionIndex($flowStyleID = 'w', $site = 0)
    {
        $project = isset($this->project->house_id) ? $this->project->house_id : null;

        $hasHouse = false;
        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->with('house')->all();

        foreach ($model as $row) {
            /* @var $row MemberHouse */
            if ($row->house->project->house_id == $this->project->house_id) {
                $hasHouse = $row->house_id;
            }
        }

        $repair = new Repair();
        if ($this->isPost && $this->isAjax) {
//            return $this->renderJsonFail('系统服务升级，暂停报事报修！');
            $repair->load($this->post());
            $repair->member_id = $this->user->id;
            $repair->type = 1;
            $repair->project_house_id = isset($this->project->house_id) ? $this->project->house_id : 0;
            $goUrl = '/new-repair/list?status=0';

            if ($repair->flow_style_id != 'w') {
                $repair->site = 2;
                $goUrl .= '&flowStyleID=8';
            }

            //投诉、报修都记录房产
            $repair->address = $repair->house->ancestor_name;

            $repair->reception_user_name = 'cdj';

            if ($repair->save()) {
                $this->notificationButler($repair);

                $requestUrl = 'http://testmgt.51homemoney.com/member-repair-notify/notify';
                $data = [
                    'repairId' => $repair->id,
                ];
                HttpRequest::post($requestUrl, $data);

                return $this->renderJsonSuccess(['goUrl' => $goUrl]);
            } else {
                return $this->renderJsonFail('error', -1, ['errorMsg' => $repair->getFirstErrors()]);
            }
        }

        $memberHouse = MemberHouse::findOne(['member_id' => $this->user->id, 'status' => MemberHouse::STATUS_ACTIVE, 'house_id' => $hasHouse]);
        return $this->render('index', get_defined_vars());
    }

    public function actionList($status = 0, $flowStyleID='w')
    {
        $hasHouse = false;
        $model = MemberHouse::find()
            ->where([
                'member_id' => $this->user->id,
                'status' => [MemberHouse::STATUS_ACTIVE],
            ])
            ->with('house')->all();



        if ($this->project != null) {
            foreach ($model as $row) {
                /* @var $row MemberHouse */
                if ($row->house->project->house_id == $this->project->house_id) {
                    $hasHouse = $row->house_id;
                }
            }

            $projectName = $this->project->house_name;
        }


        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Repair::find()
            ->where(['member_id' => $this->user->id])
            ->andFilterWhere(['status' => explode(',', $status)])
            ->andFilterWhere(['flow_style_id' => $flowStyleID])
            ->orderBy('updated_at DESC');

        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('list', [
                'dataProvider' => $dataProvider,
                'status' => $status,
                'hasHouse' => $hasHouse,
                'projectName' => $projectName,
                'flowStyleID' => $flowStyleID,
            ]);
        }
    }

    public function actionView($id = 0)
    {
        $model = Repair::findOne(['id' => $id, ['member_id' => $this->user->id]]);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        $dataArray = [];

        if (!in_array($model->status,[Repair::STATUS_WAIT, Repair::STATUS_CANCEL, Repair::STATUS_HOLD]) && $model->repairResponse->business_id > 0) {
            if ($model->status != Repair::STATUS_EVALUATED) {
                $res = (new NewWindow())->getRepairDetail($model->repairResponse->business_id);

                if (isset($res['Response']['Data']['Record'][0]['ServiceState'])) {

                    // 如果新视窗完成了，就完成了
                    /*if ($res['Response']['Data']['Record'][0]['ServiceState'] == 3 && $model->status != Repair::STATUS_EVALUATED) {
                        $model->status = $res['Response']['Data']['Record'][0]['ServiceState'];
                        $model->save();
                    }*/

                    if($res['Response']['Data']['Record'][0]['ServiceState'] > 0){
                        $model->status = $res['Response']['Data']['Record'][0]['ServiceState'];
                        $model->save();
                    }

                }

                if (isset($res['Response']['Data']['Record']))
                    $dataArray = $res['Response']['Data']['Record'];

                $levelName = isset($dataArray[0]['LevelName']) ? $dataArray[0]['LevelName'] : '';

                //saveRepairResponse($repairId, $code, $errorMsg, $responseData, $levelName = '', $result = [])
                $this->saveRepairResponse($model->id, $res['Response']['Data']['NWRespCode'], $res['Response']['Data']['NWErrMsg'], $res['Response']['Data']['Record'], $levelName, $res['Response']['Data']);

            } elseif ($model->status == Repair::STATUS_EVALUATED){
                $res = isset($model->repairResponse->response_data) ? unserialize($model->repairResponse->response_data) : [];
                if(empty($res)){
                    $newWindowResult = (new NewWindow())->getRepairDetail($model->repairResponse->business_id);
                    /*$this->saveRepairResponse($model->id, $newWindowResult['Response']['Data']['NWRespCode'], $newWindowResult['Response']['Data']['NWErrMsg'], $newWindowResult['Response']['Data']['Record'], $newWindowResult['Response']['Data']['Record'][0]['LevelName']);*/
                    $dataArray = $newWindowResult['Response']['Data']['Record'];
                }
            }
        }

        return $this->render('view', ['model' => $model, 'res' => $dataArray]);
    }

    public function actionCancel($id = 0)
    {
        $model = Repair::findOne(['id' => $id, ['member_id' => $this->user->id]]);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        $res = [];

        if ($this->isPost) {
            $postData = $this->post('RepairCancel');
            $repairId = $postData['id'];
            $repair = Repair::findOne(['id' => $repairId, 'status' => Repair::STATUS_WAIT, 'member_id' => $this->user->id]);
            if (!$repair) {
                return $this->renderJsonFail($repairId);
            }
            $repairCancel = RepairCancel::findOrCreate($repairId);
            if ($repairCancel->load($this->post())) {
                if ($repairCancel->type == 3 && empty($repairCancel->content)) {
                    return $this->renderJsonFail('请填写内容说明');
                }
                $model->status = Repair::STATUS_CANCEL;
                if ($repairCancel->save() && $model->save()) {
                    return $this->renderJsonSuccess([]);
                }
            }

            return $this->renderJsonFail($repairCancel->type);
        }

        return $this->render('cancel', ['model' => $model, 'res' => $res]);
    }

    public function actionCustomerEvaluation($id = 0)
    {
        $model = Repair::findOne(['status' => [Repair::STATUS_DONE, 7], 'id' => $id]);

        if ($this->isPost) {
            $postData = $this->post('RepairCustomerEvaluation');
            $repairId = $postData['id'];

            $model = Repair::findOne([
                'id' => $repairId,
                'member_id' => $this->user->id,
                'status' => [Repair::STATUS_DONE, 7],
            ]);

            if (!$model) {
                return $this->renderJsonFail('error');
            }

            $RepairCustomerEvaluation = RepairCustomerEvaluation::findOrCreate($repairId);
            $RepairCustomerEvaluation->load($this->post());
            $model->status = Repair::STATUS_EVALUATED;

            $satisfaction = 3;

            switch($RepairCustomerEvaluation->satisfaction){
                case 1:
                    $satisfaction = 5;
                    break;
                case 2:
                    $satisfaction = 4;
                    break;
                case 4:
                    $satisfaction = 2;
                    break;
                case 5:
                    $satisfaction = 1;
                    break;
            }

            $syncNewWindowRepairResult = $this->newWindowCustomerEvaluation($model->repairResponse->business_id, $satisfaction, $RepairCustomerEvaluation->timeliness, $RepairCustomerEvaluation->customer_idea);

            if (is_array($syncNewWindowRepairResult) && $RepairCustomerEvaluation->save() && $model->save()) {
                return $this->renderJsonSuccess('');
            }

            return $this->renderJsonFail('提交失败');
        }

        return $this->render('customer-evaluation', ['model' => $model]);
    }

    /**
     * 获取报事报修详情（新视察），反馈回来的内容写入到 repairResponse 表
     * @param $repairId
     * @param $code
     * @param $errorMsg
     * @param $responseData
     * @param string $levelName
     * @param array $result
     * @return bool
     */
    protected function saveRepairResponse($repairId, $code, $errorMsg, $responseData, $levelName = '', $result = [])
    {
        $model = RepairResponse::findOrCreate($repairId);

        $model->code = $code;
        $model->error_msg = $errorMsg;
        $model->response_data = serialize($responseData);
        $model->level_name = $levelName;
        $model->service_state = isset($result['Record'][0]['ServiceState']) ? $result['Record'][0]['ServiceState'] : 0;

        return $model->save();
    }

    /**
     * 提交报事事务到新视窗
     * @param Repair $repairModel
     * @param int $ActionFlag 新视窗相对应字段状态  0: 直接提交下一步（状态为处理中） 1:暂存（状态为待提交）
     * @return bool
     */
    protected function newWindowRepair(Repair $repairModel, $ActionFlag = 1)
    {

        $pics = [];
        $pic = explode(',', $repairModel->pics);

        /*if (sizeof($pic)) {
            foreach ($pic as $row) {
                $pics[] = [
                    'FileName' => pathinfo(\Yii::getAlias($row), PATHINFO_BASENAME),
                    'URL' => 'http://butler.cdj.loc.com.tunnel.qydev.com' . \Yii::getAlias($row),
                ];
            }
        }*/

        $data = [
            'ActionFlag' => $ActionFlag,
            'ContactAddress' => $repairModel->house->ancestor_name,
            'ContactPhone' => $repairModel->tel,
            'Content' => isset($repairModel->content) ? $repairModel->content : '',
            'ContactName' => $repairModel->name,
            'CustomerName' => $repairModel->name,
            'PrecinctID' => $repairModel->project_house_id,
            'Site' => $repairModel->site,    //1|2
            'StyleID' => $repairModel->flow_style_id,
            'ServiceKind' => 0,
        ];

        $postData = [$data];

        $res = (new NewWindow())->postRepair($postData);

        return isset($res['Response']['Data']['Record']) ? $res : $res['Response']['Data']['NWErrMsg'];
    }

    /**
     * 业主评价，同步到新视窗，不管是否写入对方记录，返回成功则评价成功
     * @param $ServiceID
     * @param $Satisfaction
     * @param $Timeliness
     * @param string $CustomerIdea
     * @return bool|array
     */
    protected function newWindowCustomerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea = '')
    {
        $res = (new NewWindow())->customerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea);

        return isset($res['Response']['Data']['Record']) ? $res : $res['Response']['Data']['NWErrMsg'];
    }

    /**
     * 发送通知管家
     * @param Repair $repair
     * @param string $msgtype
     * @param int $agentid
     * @return bool|array
     */
    protected function notificationButler($repair, string $msgtype='text',int $agentid=53)
    {
        $butlerModel = Butler::find()->select('wechat_user_id')
            ->where(['status' => Butler::STATUS_ENABLE, 'project_house_id' => $repair->project_house_id])
            ->andWhere(['IN', 'group', [Butler::GROUP_1, Butler::GROUP_E, Butler::GROUP_OF]])->asArray()->all();

        if($butlerModel){
            $users = ArrayHelper::getColumn($butlerModel, 'wechat_user_id');
            $users = implode($users,'|');

            $data = [
                'touser' => $users,
                'msgtype' => $msgtype,
                'agentid' => $agentid, //中奥通讯录应用 ID
                'text' => [
                    'content' => "报事报修：类型【{$repair->flowStyleText}】，\n业主【{$repair->name}】，\n手机【{$repair->tel}】，\n地址【{$repair->address}】，\n报修内容【{$repair->content}】\n<a href=\"http://butler.51homemoney.com\">点击查看</a>",
                ],
                'safe' => 0,
            ];

            $wechatQYSDK = new QYWechatSDK(\Yii::$app->params['wechatQY']);
            $res = $wechatQYSDK->sendMsg($data);

            $this->writeQyWeixinNotifyLog($data, $res);

            return $res;
        }

        return 0;
    }

    /**
     * @param array|string $sendData
     * @param array|string $result
     */
    protected function writeQyWeixinNotifyLog($sendData, $result)
    {
        $model = new QyWeixinNotifyLog();
        $model->send_data = serialize($sendData);
        $model->result = serialize($result);
        $model->ip = \Yii::$app->request->userIP;

        $model->save();
    }

}