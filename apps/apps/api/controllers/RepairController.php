<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 9:42
 */

namespace apps\api\controllers;


use apps\api\models\Butler;
use apps\api\models\House;
use apps\api\models\Member;
use apps\api\models\MemberHouse;
use apps\api\models\Repair;
use apps\api\models\RepairCancel;
use common\models\QyWeixinNotifyLog;
use common\models\RepairCustomerEvaluation;
use common\models\RepairResponse;
use common\valueObject\TimeValue;
use components\newWindow\NewWindow;
use components\wechatSDK\QYWechatSDK;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class RepairController extends Controller
{
    public $modelClass = 'apps\api\models\Repair';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    /**
     * 只获取用户住宅
     * @author HQM 2018/11/21
     * @return string
     */
    public function actionCustomerInfo()
    {
        $member = Member::findOne(['id' => $this->userId]);
        if(!$member){
            return $this->renderJsonFail(40003);
        }

        $res['memberInfo'] = [
            'name' => $member->name,
            'phone' => $member->phone,
        ];
        $memberHouse = $memberHouse = MemberHouse::find()
            ->select("h.ancestor_name AS ancestorName,h.house_id AS houseId")
            ->where(['member_house.member_id' => $this->userId, 'member_house.status' => MemberHouse::STATUS_ACTIVE])
            ->andFilterWhere(['member_house.group' => MemberHouse::GROUP_HOUSE])
            ->leftJoin("`house` AS h ON `h`.`house_id` = `member_house`.`house_id`")
            ->orderBy("created_at DESC")->asArray()->all();

        $res['memberHouse'] = $memberHouse;
        return $this->renderJsonSuccess(200, $res);
    }

    public function actionList(){

        $res = [];

        $type = $this->get('type', 'w');

        $status = $this->get('status', Repair::STATUS_WAIT);

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = Repair::find()->select('')
            ->where(['flow_style_id' => $type, 'status' => explode(',', $status), 'member_id' => $this->userId])
            ->orderBy('updated_at DESC');

        $dataProvider->pagination = new Pagination([
            'validatePage' => false,
        ]);

        foreach ($dataProvider->getModels() as $model){

            $followDate = in_array($model->status, [2, 3, 7, 3000]) && isset($model->repairResponse->created_at)
                ? TimeValue::dateDiff($model->repairResponse->created_at)
                : '';

            $styleClass = $model->flow_style_id == 'w' ? $model->flow_style_id . $model->site : $model->flow_style_id;
            //受理人
            $receptionUserName = isset($model->reception_user_name) ? $model->reception_user_name : '暂无';
            //处理人
            $orderUserName = isset($model->order_user_name) ? $model->order_user_name : '暂无';
            $site = $model->flow_style_id == 'w' ? $model->siteText : $model->flowStyleText;

            //取消原因
            $cancelContent = '';
            if($model->status == Repair::STATUS_CANCEL){
                $cancelContent = $model->repairCancel->type < 3 ? $model->repairCancel->typeText : $model->repairCancel->content;
            }

            $res[] = [
                'id'            => $model->id,
                'site'          => $site,
                'content'       => mb_substr($model->content, 0, 5) . '...',
                'address'       => $model->house->showName,
                'postDate'      => TimeValue::dateDiff($model->created_at),
                'followDate'    => $followDate,
                'class'         => $styleClass,
                'receptionUserName' => $receptionUserName,
                'orderUserName' => $orderUserName,
                'flowStyleText' => $model->flowStyleText,
                'statusText'    => $model->statusText,
                'serviceId'     => isset($model->repairResponse->services_id)
                    ? mb_substr($model->repairResponse->services_id, 0, 12) . '...'
                    : $model->id,
                'star'          => isset($model->repairCustomerEvaluation->satisfactionText)
                    ? $model->repairCustomerEvaluation->satisfactionText
                    : '',
                'timeliness'    => isset($model->repairCustomerEvaluation->timelinessText)
                    ? $model->repairCustomerEvaluation->timelinessText
                    : '',
                'cancelContent' => $cancelContent,
            ];
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 报事/投诉详情
     * @author HQM 2018/11/28
     * @param $id
     * @throws \yii\base\ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDetail($id)
    {
        $res = [];

        $model = Repair::findOne(['id' => $id, ['member_id' => $this->userId]]);

        if($model){
            $evaluation = $model->repairCustomerEvaluation;

            $pics = [];
            if(!empty($model->pics)){
                $picsArr = explode(',', $model->pics);
                foreach ($picsArr as $v){
                    $pics[] = \Yii::getAlias($v);
                }
            }

            $servicesId = isset($model->repairResponse->services_id) ? $model->repairResponse->services_id : $model->id;
            $address = !empty($model->address) ? $model->address : $model->house->ancestor_name;

            $res = [
                'id'              => $model->id,
                'type'            => $model->siteText,
                'status'          => $model->status,
                'statusText'      => $model->statusText,
                'address'         => $address,
                'name'            => $model->name,
                'tel'             => $model->tel,
                'content'         => $model->content,
                'pics'            => $pics,
                'flowStyle'       => $model->flowStyleText,
                'flowStyleId'     => $model->flow_style_id,
                'postDate'        => date("Y-m-d H:i:s", $model->created_at),
                'updateDate'      => date("Y-m-d H:i:s", $model->updated_at),
                'newWindowNumber' => $servicesId,
                'star'            => isset($evaluation->satisfaction) ? $evaluation->satisfaction : 0,
                'timeliness'      => isset($evaluation->timeliness) ? $evaluation->timeliness : 0,
                'customerIdea'    => isset($evaluation->customer_idea) ? $evaluation->customer_idea : '',
            ];

            $res['spotCircs'] = '';
            $businessId = isset($model->repairResponse->business_id) ? $model->repairResponse->business_id : null;

            if (!in_array($model->status,[Repair::STATUS_WAIT, Repair::STATUS_CANCEL, Repair::STATUS_HOLD]) && !empty($businessId)){
                //同步新视窗的报事状态
                if($model->status != Repair::STATUS_EVALUATED){
                    $newWindowRes = (new NewWindow())->getRepairDetail($businessId);
                    $record = $newWindowRes['Response']['Data']['Record'];
                    $res['spotCircs'] = $record[0]['spotCircs'] ?? '';

                    if($record[0]['ServiceState'] > 0){
                        $model->status = $record[0]['ServiceState'];
                        $model->save();
                        $res['status'] = $record[0]['ServiceState'];
                        $res['statusText'] = $model->statusText;
                    }

                    $levelName = isset($record[0]['LevelName'])
                        ? $record[0]['LevelName'] : '';

                    $this->saveRepairResponse($model->id, $newWindowRes['Response']['Data']['NWRespCode'],
                        $newWindowRes['Response']['Data']['NWErrMsg'],
                        $newWindowRes['Response']['Data']['Record'],
                        $levelName,
                        $newWindowRes['Response']['Data']
                    );
                }
            }
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 统计报事/投诉各个状态数量
     * @param $style
     * @author zhaowenxi
     */
    public function actionCount($style){

        $res = ['wait' => 0, 'doing' => 0, 'finish' => 0, 'evaluation' => 0, 'cancel' => 0];

        $data = Repair::find()->select("id,status")
            ->where(['member_id' => $this->userId, 'flow_style_id' => $style])
            ->all();

        foreach ($data as $v){
            switch ($v->status){
                case Repair::STATUS_WAIT: //待处理
                    $res['wait'] += 1;
                    break;
                case Repair::STATUS_UNDERWAY: //处理中
                case Repair::STATUS_COMPLETE:
                case 5:
                case 6:
                case 8:
                case 9:
                    $res['doing'] += 1;
                    break;
                case Repair::STATUS_DONE: //待评价
                case 7:
                    $res['finish'] += 1;
                    break;
                case Repair::STATUS_EVALUATED: //已完成
                    $res['evaluation'] += 1;
                    break;
                case 4: //已取消
                case Repair::STATUS_CANCEL:
                    $res['cancel'] += 1;
                    break;
            }
        }

        return $this->renderJsonSuccess(200, $res);
    }

    /**
     * 创建报事
     * @author HQM 2018/11/20
     * @return string
     */
    public function actionCreate()
    {
        $data = $this->post('data');

        $house = House::findOne(['house_id' => $data['houseId']]);
        $repair = new Repair();

        $repair->member_id = $this->userId;
        $repair->type = 1;
        $repair->project_house_id = $house->project_house_id;
        $repair->pics = $data['pics'];
        $repair->name = trim($data['name']);
        $repair->tel = trim($data['phone']);
        $repair->flow_style_id = $data['styleId'];
        $repair->site = $data['site'];
        $repair->content = trim($data['content']);
        $repair->house_id = $data['houseId'];

        $repair->reception_user_name = 'cdj';

        if ($data['styleId'] != 'w') {
            $repair->site = 2;
        } elseif ($data['styleId'] == Repair::FLOW_STYLE_TYPE_W && $repair->site == Repair::SITE_TYPE_2) {

            $repair->address = $house->ancestor_name;
        }

        if ($repair->save()) {
            /*$this->notificationButler($repair);

            $requestUrl = 'http://testmgt.51homemoney.com/member-repair-notify/notify';

            $data = [
                'repairId' => $repair->id,
            ];

            HttpRequest::post($requestUrl, $data);*/

            return $this->renderJsonSuccess(200);

        }

        return $this->renderJsonFail( 41001, ['message' => $repair->getErrors()]);
    }

    /**
     * 取消报事
     * @author HQM 2018/11/20
     * @param $id
     * @return string
     */
    public function actionCancel($id)
    {
        $data = $this->post();
        $model = Repair::findOne(['id' => $id, ['member_id' => $this->userId, 'status' => Repair::STATUS_WAIT]]);
        if (!$model) {
            return $this->renderJsonFail(41004);
        }

        if ($data['type'] == 3 && empty($data['cancelText'])) {
            return $this->renderJsonFail(40010);
        }

        $repairCancel = RepairCancel::findOrCreate($model->id);
        $repairCancel->type = $data['type'];
        $repairCancel->content = $data['cancelText'];
        $model->status = Repair::STATUS_CANCEL;
        if ($repairCancel->save() && $model->save()) {
            return $this->renderJsonSuccess();
        }

        return $this->renderJsonFail(40010);
    }

    /**
     * 业主评价
     * @author HQM 2018/11/21
     * @return string
     * @throws \yii\base\ErrorException
     */
    public function actionEvaluate()
    {
        $postData = $this->post();
        $repairId = $postData['repairId'];
        if(!is_numeric($repairId)) return $this->renderJsonFail(41004);

        $model = Repair::findOne([
            'id' => $repairId,
            'member_id' => $this->userId,
            'status' => [Repair::STATUS_DONE, 7],
        ]);

        if (!$model) {
            return $this->renderJsonFail(41005);
        }

        $evaluation = RepairCustomerEvaluation::findOrCreate($repairId);
        $evaluation->satisfaction = $postData['star'];
        $evaluation->timeliness = $postData['timeliness'];
        $evaluation->customer_idea = $postData['content'];
        $model->status = Repair::STATUS_EVALUATED;

        $satisfaction = 3;

        switch($evaluation->satisfaction){
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

        $businessId = $model->repairResponse->business_id;
        $response = $this->newWindowCustomerEvaluation($businessId, $satisfaction, $postData['timeliness'], $postData['content']);

        if (is_array($response) && $evaluation->save() && $model->save()) {
            return $this->renderJsonSuccess();
        }

        return $this->renderJsonFail(41004);
    }

    /**
     * 业主评价，同步到新视窗，不管是否写入对方记录，返回成功则评价成功
     * @author HQM 2018/11/21
     * @param $ServiceID
     * @param $Satisfaction
     * @param $Timeliness
     * @param string $CustomerIdea
     * @return bool|mixed|string
     * @throws \yii\base\ErrorException
     */
    protected function newWindowCustomerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea = '')
    {
        $res = (new NewWindow())->customerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea);

        return isset($res['Response']['Data']['Record']) ? $res : $res['Response']['Data']['NWErrMsg'];
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
     * 发送通知管家
     * @param Repair $repair
     * @param string $msgtype
     * @param int $agentid
     * @return bool|array
     */
    protected function notificationButler($repair, $msgtype='text', $agentid=53)
    {
        $butlerModel = Butler::find()->select('wechat_user_id')->where(['group' => Butler::GROUP_1, 'status' => Butler::STATUS_ENABLE, 'project_house_id' => $repair->project_house_id])->asArray()->all();

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