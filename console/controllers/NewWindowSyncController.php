<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/2 16:20
 * Description:
 */

namespace console\controllers;


use common\models\House;
use common\models\HouseBillOutline;
use common\models\HouseExt;
use common\models\PmOrder;
use common\models\PmOrderItem;
use common\models\Project;
use components\newWindow\NewWindow;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

class NewWindowSyncController extends Controller
{
    protected const MAX_FORKS = 30;

    public function actionLogin()
    {
        $res = (new NewWindow())->consoleLogin();
        var_export($res);
    }

    /**
     * @param null|int $forks
     * @param null|integer $projectId
     * @throws ErrorException
     */
    public function actionBillOutline($forks=null,$projectId = null)
    {
        $forks = $forks??self::MAX_FORKS;
        $projectData = Project::find()
            ->select('house_id')
            ->andFilterWhere(['house_id' => $projectId])
            ->all();
        $pids = [];
        $projectIds = ArrayHelper::getColumn($projectData,'house_id');
        /**
         * 统计进度用
         */
        $maxNumber = House::find()
            ->andWhere(['reskind' => [5, 9]])
            ->where(['project_house_id' => $projectIds])->count();
        $doing = 0;
        /**
         * 统计进度用
         */
        foreach ($projectData as $key => $projectHouseId) {
            House::getDb()->close();
            foreach (House::find()
                         ->asArray()
                         ->select('house_id')
                         ->andWhere(['reskind' => [5, 9]])
                         ->where(['project_house_id' => $projectHouseId['house_id']])
                         ->all() as $row) {
                House::getDb()->close();
                $pids[$row['house_id']] = pcntl_fork();
                if ($pids[$row['house_id']] == -1) {
                    throw  new ErrorException;
                } elseif ($pids[$row['house_id']]) {
                    if (sizeof($pids) >= $forks) {
                        $quitPID = pcntl_wait($status, WUNTRACED);
                        $doing++;
                        printf("concurrent:".sizeof($pids)." progress: [%-50s] %s%% \r", str_repeat('#',ceil($doing/2/$maxNumber*100)), round($doing/$maxNumber*100,5));
                        $k = array_search($quitPID, $pids);
                        if (isset($pids[$k])) unset($pids[$k]);
                    }
                } else {
                    $this->doBillOutline($row['house_id']);
                    exit(0);
                }
            }
        }
        foreach ($pids as $pid) {
            $doing++;
            printf("concurrent:".sizeof($pids)." progress: [%-50s] %s%% \r", str_repeat('#',ceil($doing/2/$maxNumber*100)), round($doing/$maxNumber*100,5));
            pcntl_waitpid($pid, $status);
        }
        echo "\nDone\n";
    }

    /**
     * 同步新视窗房产欠费信息 param：houseId[null],projectId[null]
     * @param null|int $houseId
     * @param null|int $projectId
     * @throws ErrorException
     */
    public function actionNewBillOutline($houseId=null, $projectId=null)
    {
        if(empty($houseId) && empty($projectId)){
//            HouseBillOutline::deleteAll();
            $this->stdout("empty--------\n");
        }

        $model = House::find()
            ->select('house_id')
            ->where(['deepest_node' => 1])
            ->andWhere(['reskind' => [5, 9]])
            ->andFilterWhere(['>', 'house_id', $houseId])
            ->andFilterWhere(['project_house_id' => $projectId])
            ->orderBy('house_id ASC');

        $tempNumber = 0;
        $runNumber = 0;
        foreach ($model->each() as $row) {
            /**
             * @var House $row
             */
            $tempNumber += 1;
            $runNumber += 1;

            $this->stdout("run：{$runNumber} \n");
            $this->stdout("-------------houseId：{$row->house_id} \n");
            $this->doBillOutline($row->house_id);

            if($tempNumber > 499){
                $tempNumber = 0;

                $this->stdout("----------sleep\n");
                sleep(2);
            }
        }

        $this->stdout("Done \n");
        exit(1);
    }

    /**
     * @param $house_id
     * @throws ErrorException
     */
    private function doBillOutline($house_id)
    {
        $model = HouseBillOutline::findOrCreate($house_id);
        $_res = (new NewWindow)->getBill($house_id);
        $res = [];
        foreach ($_res as $row) {
            $date = explode('-', $row['BillDate']);
            if (strtotime($date[0]) < time()) {
                $res[] = $row;
            }
        }
        $model->bill_count = count($res);
        if ($model->bill_count > 0) {
            $res = ArrayHelper::getColumn($res, 'BillAmount');
            $model->total_amount = array_sum($res);

            //测试数据
            $model->process_status = 0;
//            $model->aggregate_data = serialize($_res);

        } else {
            $model->total_amount = 0;
        }
        $model->save();
    }

    /**
     * 重新核销账单：pmOrderId，statusArray
     * @author HQM
     * @param $pmOrderId
     * @param array $statusArray
     * @throws ErrorException
     */
    public function actionPayBill($pmOrderId, array $statusArray = [2005, 2006])
    {
        $res = [];
        $order = PmOrder::findOne(['id' => $pmOrderId]);

        $pmOrderItemId = PmOrderItem::find()->select('id')->where(['status' => $statusArray, 'pm_order_id' => $pmOrderId])->asArray()->all();
        $pmOrderItemId = ArrayHelper::getColumn($pmOrderItemId, 'id');

        foreach ($pmOrderItemId as $key => $value){
            $items = PmOrderItem::findOne(['id' => $value, 'pm_order_id' => $pmOrderId]);
            $payBill = (new NewWindow())->payBill(
                $pmOrderId . '-' . $value,
                $order->house->project_house_id,
                $items->contract_no,
                $items->amount,
                $order->payed_at,
                $order->bill_type
            );

            $res[] = $payBill;

            if ($payBill) {
                $items->status = $payBill[0]['ReturnCode'];
                $items->bankBillNo = $payBill[0]['BankBillNo'];
//                $items->completed_at = time();
                $items->second_updated_at = time(); //二次核销
                $items->save();
            }

            $this->stdout("PmOrderItem：{$value}\n");
        }

        var_dump($res);

    }

    /**
     * @param integer $houseId
     */
    public function actionHouse($houseId)
    {
        $res = (new NewWindow())->getHouse($houseId);
        var_dump($res);
    }

    /**
     * @param integer $houseId
     */
    public function actionHouseStructure($houseId)
    {
        $res = (new NewWindow())->houseStructure($houseId);
        var_dump($res);
    }

    /**
     * @param integer $projectId
     * @param null $houseId
     * @return int
     */
    public function actionSaveHouse($projectId, $houseId = null)
    {
        if ($houseId === null) $houseId = $projectId;
        $this->stdout("running: $houseId \n");
        $res = (new NewWindow())->houseStructure($houseId);
        if (isset($res['Response']['Data']['NWRespCode'])) {
            if ($res['Response']['Data']['NWRespCode'] == '0000') {
                foreach ($res['Response']['Data']['Record'] as $row) {
                    $this->saveHouse($row, $houseId, $projectId);
                    if (isset($res['Response']['Data']['lsChild']) && is_array($res['Response']['Data']['lsChild'])) {
                        foreach ($res['Response']['Data']['lsChild'] as $v)
                            $this->saveHouse($v, $row['HouseID'], $projectId);
                    }

                    $this->stdout('Save Success' . "[projectId]：{$projectId} [house_id]：{$houseId}\n");
                }
                return 1;
            } elseif ($res['Response']['Data']['NWRespCode'] == 'Cannot find column [ParentID].') {
                return 1;
            }
        }

        $this->stdout("Save Error [projectId]:{$projectId} [house_id]:{$houseId}" . "\n");
        $this->stdout("Error_Code：{$res['Response']['Data']['NWRespCode']}=>>Error_Message：{$res['Response']['Data']['NWErrMsg']}\n");
//        throw new ErrorException("no data");
    }

    /**
     * @param string $name
     */
    public function actionProject($name)
    {
        $res = (new NewWindow())->project($name);
        var_dump($res);
    }

    /**
     * 查询电子发票状态：pmOrderNumber
     * @author HQM 2018-10-15
     * @param $pmOrderNumber
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionQueryInvoice($pmOrderNumber)
    {
        $pmOrder = PmOrder::findOne(['number' => $pmOrderNumber]);

        if(isset($pmOrder->items)) {
            $chargeDetailIdList = [];
            foreach ($pmOrder->items as $row) {
                /**
                 * @var PmOrderItem $row
                 */
                if (!empty($row->charge_detail_id_list)) {
                    $chargeDetailIdList[] = $row->charge_detail_id_list;
                } else {
                    $unserizleData = unserialize($row->bill_content);
                    if (isset($unserizleData['ChargeDetailIDList'])) {
                        $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                    }
                }
            }

            if (empty($chargeDetailIdList)) {
                $this->stderr('商品明细为空');
                exit(0);
            }

            $chargeDetailIdList = implode(',', $chargeDetailIdList);
            $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList);

            var_dump($newWindows);
        }

        exit(0);
    }

    private function saveHouse($row, $parentId, $projectId, $FindChild = true)
    {
        if(empty($row['HouseName'])){
            return false;
        }

        $house = House::findOrCreate($row['HouseID']);
        $this->stdout("findOrCreate:houseId:" . $house->house_id . "\n");
        if (empty($house->parent_id))
            $house->parent_id = $row['Level'] == 1 ? 0 : $parentId;
        $house->project_house_id = $projectId;
        $house->house_name = $row['HouseName'];
        $house->ancestor_name = $row['AncestorName'];
        $house->reskind = $row['Reskind'];
        $house->room_status = $row['RoomStatus'] . "";
        $house->room_status_name = $row['RoomStatusName'];
        $house->belong_floor = trim($row['BelongFloor']);
        $house->level = $row['Level'];
        $house->deepest_node = $row['DeepestNode'];
        $house->show_status = 0;

        if (!$house->save()) {
            echo $this->stdout("save Error");
            throw new ErrorException(serialize($house->getErrors()));
        } else {
            if ($FindChild && !$house->deepest_node && $house->house_id != $parentId) {
                $house_id = $house->house_id;
                unset($house);
                $this->actionSaveHouse($projectId, $house_id);
            }
        }
    }

    /**
     * @param null|int $forks
     * @param null|integer $projectId
     * @return int
     * @throws ErrorException
     */
    public function actionHouseExt($forks=null,$projectId = null)
    {
        $forks = $forks??self::MAX_FORKS;
        $projectData = Project::find()
            ->select('house_id')
            ->andFilterWhere(['house_id' => $projectId])
            ->all();
        $pids = [];
        /**
         * 统计进度用
         */
        $projectIds = ArrayHelper::getColumn($projectData,'house_id');
        $maxNumber = House::find()
            ->where(['project_house_id' => $projectIds])->count();
        $doing = 0;
        /**
         * 统计进度用
         */
        foreach ($projectData as $key => $projectHouseId) {
            House::getDb()->close();
            foreach (House::find()
                         ->asArray()
                         ->select('house_id')
                         ->where(['project_house_id' => $projectHouseId['house_id']])
                         ->all() as $row) {
                HouseExt::getDb()->close();
                /**
                 * @var $row House
                 */
                $pids[$row['house_id']] = pcntl_fork();
                if ($pids[$row['house_id']] == -1) {
                    throw  new ErrorException;
                } elseif ($pids[$row['house_id']]) {
                    //主进程将在进程数量达到上限时候阻塞进程
                    if (sizeof($pids) >= $forks) {
                        $quitPID = pcntl_wait($status, WUNTRACED);
                        $doing++;
                        printf("concurrent:".sizeof($pids)." progress: [%-50s] %s%% \r", str_repeat('#',ceil($doing/2/$maxNumber*100)), round($doing/$maxNumber*100,5));
                        $k = array_search($quitPID, $pids);
                        if (isset($pids[$k])) unset($pids[$k]);
                    }
                } else {
                    $newWindowRes = (new NewWindow())->getHouse($row['house_id']);
                    if ($newWindowRes) {
                        $this->saveHouseExt($newWindowRes[0]);
                        $this->stdout('Save Success' . $row['house_id']. "\n");
                    }else{
                        $this->stdout("GET ERROR ".$row['house_id']."\n");
                    }
                    exit(0);
                }
            }
        }
        foreach ($pids as $pid) {
            $doing++;
            printf("concurrent:".sizeof($pids)." progress: [%-50s] %s%% \r", str_repeat('#',ceil($doing/2/$maxNumber*100)), round($doing/$maxNumber*100,5));
            pcntl_waitpid($pid, $status);
        }
        echo "\n";
        echo "Done";
        return 0;
    }

    private function saveHouseExt($row)
    {
        $houseExt = HouseExt::findOrCreate($row['HouseID']);
        $birthDay = 0;
        if ($row['BirthDay'] != '/Date(-62135596800000+0800)/') {
            $rules = '/-?\d{6,}/';
            preg_match($rules, $row['BirthDay'], $matches);

            $numberLength = strlen($matches[0]);
            $birthDay = substr($matches[0], 0, $numberLength - 3) + (24 * 3600);
        }

        $houseExt->customer_id = $row['CustomerID'];
        $houseExt->birth_day = $birthDay;
        $houseExt->charge_area = $row['ChargeArea'];
        $houseExt->id_number = $row['IDNumber'];
        $houseExt->hurry_phone = $row['HurryPhone'];
        $houseExt->link_man = $row['LinkMan'];
        $houseExt->customer_name = trim($row['CustomerName']);
        $houseExt->mobile_phone = $row['MobilePhone'];
        $houseExt->updated_at = time();

        if (!$houseExt->save()) {
            echo $this->stdout("save Error");
            throw new ErrorException(serialize($houseExt->getErrors()));
        }
    }
}