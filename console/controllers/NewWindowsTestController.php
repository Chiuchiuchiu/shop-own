<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/8/16
 * Time: 10:55
 */

namespace console\controllers;


use common\models\PmOrder;
use common\models\PmOrderItem;
use components\newWindow\DecodeTest;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class NewWindowsTestController extends Controller
{
    /**
     * 订单明细请求新视窗是否可以开具发票 param:$pmOrderId
     * @param $pmOrderId
     */
    public function actionNewwindowQueryInv($pmOrderId)
    {
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId]);

        if(isset($pmOrder->items)){
            $chargeDetailIdList = [];
            foreach ($pmOrder->items as $row){
                /**
                 * @var PmOrderItem $row
                 */
                if(!empty($row->charge_detail_id_list)){
                    $chargeDetailIdList[] = $row->charge_detail_id_list;
                } else {
                    $unserizleData = unserialize($row->bill_content);
                    if(isset($unserizleData['ChargeDetailIDList'])){
                        $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                    }
                }
            }

            if(!empty($chargeDetailIdList)){
                $chargeDetailIdList = implode(',', $chargeDetailIdList);

                $newWindows = (new DecodeTest())->queryInvoice($chargeDetailIdList, 0);

                var_export($newWindows);

                if($newWindows['Response']['Data']['NWRespCode'] != '0000'){
                    $invStatus = true;
                }

                $record = $newWindows['Response']['Data']['Record'];
                if(!empty($record)){
                    $invStatus = true;
                }
            }

            if(!$invStatus){
                $this->stdout('等待开票！'.PHP_EOL);
            }
        }

        $this->stdout("Done \n");
        exit(0);
    }

    public function actionConsoleLogin()
    {
        $res = (new DecodeTest())->consoleLogin();

        var_export($res);
    }

    public function actionGetTempBill($houseId){

        $houseId = 84249;

        $res = (new DecodeTest())->getTempBill($houseId);

        var_export($res);
    }

    /**
     * @param integer $houseId
     * @param null $MobilePhone
     * @param null $ProjectHouseID
     */
    public function actionGetHouse($houseId, $MobilePhone=null, $ProjectHouseID = null)
    {
        $res = (new DecodeTest())->getHouse($houseId, $MobilePhone, $ProjectHouseID);

        var_export($res);
    }

    /**
     * @param integer $projectId
     * @param null $houseId
     */
    public function actionSaveHouse($projectId, $houseId = null)
    {
        if ($houseId === null) $houseId = $projectId;
        $this->stdout("running: $houseId \n");
        $res = (new DecodeTest())->houseStructure($houseId);


        var_export($res);
    }

    /**
     * @param integer $houseId
     * @param null|string $CustomerName
     * @param int $isGeneral
     */
    public function actionGetBill($houseId, $CustomerName = null, $isGeneral = 0)
    {
        $res = (new DecodeTest())->getBill($houseId, $CustomerName, $isGeneral);

        var_export($res);

    }

    /**
     * @param $pmOrderId
     * @param array $pmOrderItemId
     * @param array $statusArray
     */
    public function actionPayBill($pmOrderId, array $pmOrderItemId = [],array $statusArray = [2005, 2006])
    {
        $res = [];
        $order = PmOrder::findOne(['id' => $pmOrderId]);

        if(empty($pmOrderItemId[0])){
            $pmOrderItemId = PmOrderItem::find()->select('id')->where(['status' => $statusArray, 'pm_order_id' => $pmOrderId])->asArray()->all();

            $pmOrderItemId = ArrayHelper::getColumn($pmOrderItemId, 'id');
        }

        foreach ($pmOrderItemId as $key => $value){
            $items = PmOrderItem::findOne(['id' => $value, 'pm_order_id' => $pmOrderId]);
            $res[] = (new DecodeTest())->payBill(
                $pmOrderId . '-' . $value,
                $order->house->project_house_id,
                $items->contract_no,
                $items->amount,
                time()
            );
        }

        var_export($res);
    }

    /**
     * @param integer $houseId
     */
    public function actionHouseStructure($houseId)
    {
        $res = (new DecodeTest())->houseStructure($houseId);

        var_export($res);
    }

    /**
     * @param integer $projectHouseId
     */
    public function actionProjectTreeStructure($projectHouseId)
    {
        $res = (new DecodeTest())->projectTreeStructure($projectHouseId);

        var_export($res);
    }

    /**
     * @param string $name
     */
    public function actionProject($name)
    {
        $res = (new DecodeTest())->project($name);

        var_export($res);
    }

    /**
     * @param integer $PrecinctID
     * @param string $Keyword
     * @param int $CurrentPage
     * @param int $PageSize
     */
    public function actionGetProjectOwnerOrTenants($PrecinctID, $Keyword, $CurrentPage = 0, $PageSize = 10)
    {
        $res = (new DecodeTest())->getProjectOwnerOrTenants($PrecinctID, $Keyword, $CurrentPage, $PageSize);

        var_export($res);
    }

}