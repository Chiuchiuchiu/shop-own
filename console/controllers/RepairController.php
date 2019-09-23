<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/11
 * Time: 14:23
 */

namespace console\controllers;


use common\models\House;
use common\models\Repair;
use common\models\RepairResponse;
use components\helper\HttpRequest;
use components\newWindow\NewWindow;
use yii\base\ErrorException;
use yii\console\Controller;

class RepairController extends Controller
{
    /**
     * 批量处理未提交至新视察的报事记录 Param:$repairId(null),$sleep(default=5)
     * @param null $repairId
     * @param int $sleep
     */
    public function actionProcessingWaitState($repairId=null, $sleep=5)
    {
        $requestUrl = 'http://testmgt.51homemoney.com/member-repair-notify/notify';
        if(empty($repairId)){
            $repair = Repair::find()->where(['status' => Repair::STATUS_WAIT])->all();
            foreach ($repair as $row){
                /**
                 * @var Repair $row
                 */
                $data = [
                    'repairId' => $row->id,
                ];

                HttpRequest::post($requestUrl, $data);
                $this->stdout("RepairId：{$row->id} \n");

                sleep($sleep);
            }

        } else {
            $data = [
                'repairId' => $repairId,
            ];

            HttpRequest::post($requestUrl, $data);

            $this->stdout("RepairId：{$repairId} \n");
        }

        die(1);
    }

    /**
     * @param integer $projectHouseId
     */
    public function actionProjectTreeStructure($projectHouseId)
    {
        $res = (new NewWindow())->projectTreeStructure($projectHouseId);

        var_dump($res);
    }

    /**
     * 定时提交到新视窗
     * @param int $repaid
     * @param string $time
     * @param int $limit
     */
    public function actionAutoCommitTrans(int $repaid=0,string $time='0', int $limit=100)
    {
        $nowTime = date('Y-m-d', time());
        $nowTime = strtotime($nowTime);
        $time = !empty($time) ? strtotime($time) : $nowTime;

        $result = Repair::find()
            ->where(['status' => Repair::STATUS_WAIT])
            ->andWhere(['>', 'created_at', $time])
            ->andFilterWhere(['id' => $repaid])
            ->limit($limit)
            ->all();

        if($result){
            foreach($result as $row){
                /**
                 * @var $row Repair
                 */
                $res = $this->newWindowRepair($row);
                if(is_array($res)) {
                    $businessId = isset($res['Response']['Data']['Record'][0]['BusinessID']) ? $res['Response']['Data']['Record'][0]['BusinessID'] : 0;
                    $servicesId = isset($res['Response']['Data']['Record'][0]['ServicesID']) ? $res['Response']['Data']['Record'][0]['ServicesID'] : 0;
                    $flowID = isset($res['Response']['Data']['Record'][0]['FlowID']) ? $res['Response']['Data']['Record'][0]['FlowID'] : 0;

                    RepairResponse::log($row->id, $res['Response']['Data']['NWRespCode'], $res['Response']['Data']['NWErrMsg'], $res['Response']['Data']['Record'], $servicesId, $businessId, $flowID);

                    $row->status = 1;
                    $row->reception_user_name = 'cdj';
                    if ($row->save()) {
                        $this->stdout("Success \n");
                    }
                } else {
                    $this->stdout("{$res}\n");
                }
            }
            $this->stdout("Done******\n");
        }

        $this->stdout("End******\n");die();
    }

    /**
     * @param integer $projectId
     * @param string $Keyword
     * @param int $KeywordType
     */
    public function actionGetCustomerInfo($projectId, $Keyword, $KeywordType = 1)
    {
        $res = (new NewWindow())->getCustomerInfo($projectId, $Keyword, $KeywordType);
        var_dump($res);
    }

    /**
     * 获取业主所有房产
     * @param integer $projectId
     * @param string $Keyword
     * @param int $KeywordType
     * @param int $multiHouse
     */
    public function actionGetCustomerHouseInfo($projectId, $Keyword, $KeywordType=0, $multiHouse=1)
    {
        $res = (new NewWindow())->getCustomerHouseInfo($projectId, $Keyword, $KeywordType, $multiHouse);
        var_export($res);
    }

    /**
     * 获取业主信息 houseId
     * @param $houseId
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     * @author zhaowenxi
     */
    public function actionGetHouseInfo($houseId)
    {
        $res = (new NewWindow())->getHouseInfo($houseId);
        var_dump($res);
    }

    public function actionUploadFile($file, $reportPhoto=null)
    {
        $fp = fopen($file, 'rb');
        $fileBinary = fread($fp, filesize($file));
        fclose($fp);

        $res = (new NewWindow())->uploadFile($fileBinary, $reportPhoto);
        var_dump($res);
    }

    /**
     * @param integer $id
     * @param integer $ReceptionUserID 受理人ID
     * @param integer $OrderUserID  接单人ID
     * @param string $OrderUserName 接单人名称
     * @param int $ActionFlag
     * @throws ErrorException
     */
    public function actionTestRepair($id, $ReceptionUserID, $OrderUserID=null, $OrderUserName=null, $ActionFlag = 1)
    {
        $repairModel = Repair::findOne(['id' => $id]);

        if(!$repairModel){
            throw new ErrorException($repairModel->errors);
        }

        $pics = [];
        $pic = explode(',', $repairModel->pics);
        foreach ($pic as $row){
            $pics[] = [
                'FileName' => pathinfo(\Yii::getAlias($row), PATHINFO_BASENAME),
                'URL' => 'http://butler.cdj.loc.com.tunnel.qydev.com' . \Yii::getAlias($row),
            ];
        }

//        var_export($pics);die;

        $number = mt_rand(100, 1000);

        $data = [
            'ActionFlag' => $ActionFlag,
            'ContactAddress' => $repairModel->house->ancestor_name,
            'ContactPhone' => $repairModel->tel,
            'Content' => isset($repairModel->content) ? $repairModel->content : '',
            'ContactName' => $repairModel->name . $number,
            'CustomerName' => $repairModel->name . $number,
            'ReceptionUserID' => $ReceptionUserID,
            'PrecinctID' => $repairModel->house->project_house_id,
            'Site' => $repairModel->site,    //1|2
            'StyleID' => $repairModel->flow_style_id,
            'FileInfo' => $pics,
            'ServiceKind' => 0,
        ];

        if($repairModel->flow_style_id == 'w'){
            $data['OrderUserID'] = $OrderUserID ? $OrderUserID : '';
            $data['OrderUserName'] = $OrderUserName ? $OrderUserName : '';
        }


        $postData = [$data];

//        var_export($postData);die;

        $res = (new NewWindow())->postRepair($postData);

        $businessId = isset($res['Response']['Data']['Record'][0]['BusinessID']) ? $res['Response']['Data']['Record'][0]['BusinessID'] : 0;
        $servicesId = isset($res['Response']['Data']['Record'][0]['ServicesID']) ? $res['Response']['Data']['Record'][0]['ServicesID'] : 0;

        RepairResponse::log($id, $res['Response']['Data']['NWRespCode'], $res['Response']['Data']['NWErrMsg'], $res['Response']['Data']['Record'], $servicesId, $businessId);

        var_export($res);
    }

    /**
     * @param integer $repairId
     * @param int $ActionFlag
     */
    public function actionBusinessIdRepair($repairId, $ActionFlag = 0)
    {
        $repairModel = Repair::findOne(['id' => $repairId]);

        $this->newWindowRepair($repairModel, $ActionFlag);

    }

    /**
     * 提交报事事务到新视窗
     * @param Repair $repairModel
     * @param int $ActionFlag 新视窗相对应字段状态  0: 直接提交下一步（状态为处理中） 1:暂存（状态为待提交）
     * @return mixed|string
     */
    protected function newWindowRepair(Repair $repairModel, $ActionFlag = 1)
    {
        $pics = [];

        $data = [
            'ActionFlag' => $ActionFlag,
            'ContactAddress' => $repairModel->house->ancestor_name,
            'ContactPhone' => $repairModel->tel,
            'Content' => isset($repairModel->content) ? $repairModel->content : '',
            'ContactName' => $repairModel->name,
            'CustomerName' => $repairModel->name,
            'PrecinctID' => !empty($repairModel->project_house_id) ? $repairModel->project_house_id : $repairModel->house->project_house_id,
            'Site' => $repairModel->site,    //1|2
            'StyleID' => $repairModel->flow_style_id,
            'ServiceKind' => 0,
        ];

        /*if(isset($repairModel->pics)){
            $pic = explode(',', $repairModel->pics);
            foreach ($pic as $row) {
                $pics[] = [
                    'FileName' => pathinfo(\Yii::getAlias($row), PATHINFO_BASENAME),
                    'URL' => \Yii::getAlias($row),
                ];
            }
            $data['FileInfo'] = $pics;
        }*/

        $postData = [$data];
        $res = (new NewWindow())->postRepair($postData);

        return isset($res['Response']['Data']['Record']) ? $res : $res['Response']['Data']['NWErrMsg'];
    }

    /**
     * @param integer $projectId
     * @param string $CustomerName
     * @param string $KeyWord
     * @param string $FlowStyleID
     * @param int $PageIndex
     * @param int $PageSize
     */
    public function actionGetTransactionList($projectId, $CustomerName, $KeyWord = '', $FlowStyleID = 'w', $PageIndex = 0, $PageSize = 10)
    {
        $res = (new NewWindow())->getTransactionList($projectId, $CustomerName, $KeyWord, $FlowStyleID, $PageIndex, $PageSize);
        var_dump($res);
    }

    /**
     * @param integer $PrecinctID
     * @param string $Keyword
     * @param int $CurrentPage
     * @param int $PageSize
     */
    public function actionGetProjectOwnerOrTenants($PrecinctID, $Keyword, $CurrentPage = 0, $PageSize = 10)
    {
        $res = (new NewWindow())->getProjectOwnerOrTenants($PrecinctID, $Keyword, $CurrentPage, $PageSize);
        var_export($res);
    }

    /**
     * @param integer $houseId
     * @param null $CustomerName
     * @param int $isGeneral
     */
    public function actionGetBill($houseId, $isGeneral = 0, $CustomerName=null)
    {
        $_res = (new NewWindow)->getBill($houseId, $isGeneral, $CustomerName);
        var_export($_res);
    }

    public function actionHouse($houseId, $MobilePhone = null, $ProjectHouseID = null)
    {
        $res = (new NewWindow())->getHouse($houseId, $MobilePhone, $ProjectHouseID);
        var_export($res);
    }

    /**
     * @param string $StyleID
     */
    public function actionSelectionOfEmergency($StyleID = 'w')
    {
        $res = (new NewWindow())->selectionOfEmergency($StyleID);
        var_dump($res);
    }

    /**
     * @param string $ParamTypeID
     */
    public function actionSelectSourceAndSingleSlipCause($ParamTypeID = '3015')
    {
        $res = (new NewWindow())->selectSourceAndSingleSlipCause($ParamTypeID);
        var_dump($res);
    }

    /**
     * @param string $Keyword
     * @param $ParentID
     * @param string $StyleID
     */
    public function actionNewspaperClassify($StyleID = 'w', $Keyword = null, $ParentID = null)
    {
        $res = (new NewWindow())->getNewspaperClassify($StyleID, $Keyword, $ParentID);
        var_dump($res);
    }

    /**
     * @param integer $projectId
     * @param integer $houseId
     * @param int $FlowID
     * @param int $CurrStepID
     * @param null $IsGetCurrStepUser
     */
    public function actionRepairBindUser($projectId, $houseId, $FlowID, $IsGetCurrStepUser = null, $CurrStepID = 1)
    {
        $res = (new NewWindow())->getRepairBindUser($projectId, $houseId, $FlowID, $IsGetCurrStepUser, $CurrStepID);
        var_dump($res);
    }

    /**
     * @param int $serverId
     */
    public function actionRepairDetail($serverId)
    {
        $res = (new NewWindow())->getRepairDetail($serverId);
        var_export($res);
    }

    /**
     * @param integer $projectId
     * @param string $FlowStyleID
     */
    public function actionGetStepList($projectId, $FlowStyleID=null)
    {
        $res = (new NewWindow())->getStepList($projectId, $FlowStyleID);
        var_dump($res);
    }

    /**
     * @param integer $ServiceID
     */
    public function actionGetNowStepInfo($ServiceID)
    {
        $res = (new NewWindow())->getNowStepInfo($ServiceID);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionOrders($serviceId)
    {
        $res = (new NewWindow())->orders($serviceId);
        var_dump($res);
    }

    /**
     * @param integer $ServiceID
     * @param int $Satisfaction
     * @param int $Timeliness
     * @param string $CustomerIdea
     */
    public function actionCustomerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea='')
    {
        $res = (new NewWindow())->customerEvaluation($ServiceID, $Satisfaction, $Timeliness, $CustomerIdea);
        var_export($res);
    }

    public function actionGetEvaluationInfo($ServiceId)
    {
        $res = (new NewWindow())->getEvaluationInfo($ServiceId);
        var_export($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionGetRepairStep($serviceId)
    {
        $res = (new NewWindow())->getRepairStep($serviceId);
        var_export($res);
    }

    /**
     * @param integer $ServiceID
     * @param integer $ToUserID
     */
    public function actionSingleTurn($ServiceID, $ToUserID)
    {
        $res = (new NewWindow())->singleTurn($ServiceID, $ToUserID);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionBackOrder($serviceId)
    {
        $res = (new NewWindow())->backOrder($serviceId);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     * @param int $UpgradeType
     */
    public function actionPostRepairUpdate($serviceId, $UpgradeType)
    {
        $res = (new NewWindow())->postRepairUpdate($serviceId, $UpgradeType);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionCancel($serviceId)
    {
        $res = (new NewWindow())->cancel($serviceId);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionSearchHistoryDoStep($serviceId)
    {
        $res = (new NewWindow())->searchHistoryDoStep($serviceId);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionRepairUnqualifiedUpdate($serviceId)
    {
        $res = (new NewWindow())->repairUnqualifiedUpdate($serviceId);
        var_dump($res);
    }

    /**
     * @param integer $serviceId
     */
    public function actionFollowUp($serviceId)
    {
        $res = (new NewWindow())->followUp($serviceId);
        var_dump($res);
    }

    /**
     * @param integer $BusinessID
     * @param integer $ID
     * @param int $FollowType
     * @param $ProcessInfo
     * @param $UnFinishReason
     * @param $FinishTime
     * @param $FollowTime
     */
    public function actionFollowAndModify($BusinessID, $ID, $FollowType, $ProcessInfo, $UnFinishReason, $FinishTime, $FollowTime)
    {
        $res = (new NewWindow())->followAndModify($BusinessID, $ID, $FollowType, $ProcessInfo, $UnFinishReason, $FinishTime, $FollowTime);
        var_dump($res);
    }

    /**
     * 同步用户提交的报事报修处理状态
     * @param null $repairId
     * @param int $timeBetween 0默认 1本月初到当日，2上月
     * @param null $projectId
     * @throws ErrorException
     * @author zhaowenxi
     */
    public function actionSyncNewWindowRepairInfo($repairId=null, $timeBetween=0, $projectId = null)
    {
        $data = Repair::find()->where(['NOT IN', 'status' ,[Repair::STATUS_CANCEL, Repair::STATUS_EVALUATED, 4, Repair::STATUS_HOLD]]);

        $dateWhere = $projectWhere = $repairWhere = [];

        switch ($timeBetween){
            //查找本月第一天零点到当前时间
            case 1: $dateWhere = ['BETWEEN', 'created_at', strtotime(date("Y-m-01")), time()];break;
            //上一月
            case 2:  $dateWhere = ['BETWEEN', 'created_at',
                strtotime(date("Y-m-01", strtotime('-1 month'))),
                strtotime(date('Y-m-t', strtotime('-1 month')))];
            break;
        }

        $projectId && $projectWhere = ['project_house_id' => $projectId];
        $repairId && $repairWhere = ['id' => $repairId];

        $data->andFilterWhere($dateWhere)->andFilterWhere($projectWhere)->andFilterWhere($repairWhere);

        $total = 0;
        foreach ($data->all() as $row) {
            /**
             * @var $row Repair
             */
            $repairDetail = (new NewWindow())->getRepairDetail($row->repairResponse->business_id);

            if($repairDetail['Response']['Data']['NWRespCode'] == '0000'){
                $newRes     = $repairDetail['Response']['Data']['Record'][0];
                $businessId = isset($newRes['BusinessID']) ? $newRes[0]['BusinessID'] : 0;
                $servicesId = isset($newRes['ServicesID']) ? $newRes[0]['ServicesID'] : 0;
                $levelName  = isset($newRes['LevelName'])  ? $newRes[0]['LevelName']  : '';

                $repairResponseRes = $this->saveRepairResponse($row->id, $servicesId,$repairDetail['Response']['Data']['NWRespCode'], $repairDetail['Response']['Data']['NWErrMsg'], $repairDetail['Response']['Data']['Record'], $businessId, $levelName);

                $this->stdout(serialize($repairResponseRes) . "\n");

                if (isset($repairDetail['Response']['Data']['Record'])) {

                    $saveRepair = $this->saveRepair($row->id, $repairDetail['Response']['Data']['Record'][0]['ServiceState']);

                    $this->stdout($saveRepair . "\n");

                } else {

                    $this->stdout($repairDetail['Response']['Data']['NWErrMsg'] . "\n");
                }
            }

            $total++;

            sleep(0.5);
        }

        $this->stdout('async end, total:' . $total . "\n");
    }

    public function actionGetLoginKey()
    {
        $res = (new NewWindow())->getLoginKey();
        var_dump($res);
    }

    /**
     * 获取报事报修详情（新视察），反馈回来的内容写入到 repairResponse 表
     * @param $repairId
     * @param $servicesId
     * @param $code
     * @param $errorMsg
     * @param $responseData
     * @param int $businessId
     * @param string $levelName
     * @return bool
     */
    protected function saveRepairResponse($repairId, $servicesId, $code, $errorMsg, $responseData, $businessId=0, $levelName='')
    {
        $model = RepairResponse::findOrCreate($repairId);

        $model->services_id = $servicesId;
        $model->code = $code;
        $model->error_msg = $errorMsg;
        $model->response_data = serialize($responseData);
        $model->business_id = $businessId;
        $model->level_name = $levelName;

        $res = $model->save();

        $this->stdout("保存状态：" . $res . "\n");

        return $model->getErrors();

    }

    /**
     * 新视窗查询报事报修详情，把对应的事物处理状态更新到 repair 表
     * @param $repairId
     * @param $serviceState
     * @return bool
     */
    protected function saveRepair($repairId, $serviceState)
    {
        $model = Repair::findOne(['id' => $repairId]);
        $model->status = $serviceState;
        return $model->save();
    }

    /**
     * 补回投诉地址
     * @param int $start 开始时间
     * @param int $end  结束时间
     * @param null $repairId  报事id
     * @author zhaowenxi
     */
    public function actionUpdateComplaintAddress($start =0, $end = 0, $repairId = null){

        if(!$start){
            $start = time() - (86400 * 7);
            $end = time();
        }

        $data = Repair::find()->where(['flow_style_id' => Repair::FLOW_STYLE_TYPE_8])
            ->andFilterWhere(['between', 'created_at', $start, $end])
            ->andFilterWhere(['id' => $repairId])
            ->all();

        if($data){

            foreach ($data AS $row){
                /**
                 * @var $row Repair
                 */
                $row->address = House::findOne($row->house_id)->ancestor_name ?? '-';

                $res = $row->save();

                $this->stdout("房号id：{$row->house_id}，保存状态：" . $res . "\n");
            }
        }
    }
}