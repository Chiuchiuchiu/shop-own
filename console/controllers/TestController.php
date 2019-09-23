<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/16 08:45
 * Description:
 */

namespace console\controllers;


use common\models\House;
use common\models\Member;
use common\models\MemberHouse;
use components\email\Email;
use components\helper\File;
use components\wechatSDK\WechatSDK;
use components\newWindow\NewWindow;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionDataTest()
    {
        $weChatApp = new WechatSDK(\Yii::$app->params['wechat']);
        $res = $weChatApp->getDatacube('user','summary','2016-02-02');
        var_dump($weChatApp->errMsg);
        var_dump($weChatApp->errCode);
        var_dump($res);
    }

    /**
     * 获取微信 accountToken
     */
    public function actionGetAccountToken()
    {
        $weChatApp = new WechatSDK(\Yii::$app->params['wechat']);
        $res = $weChatApp->checkAuth();
        var_dump($res);
    }

    /**
     * 测试保存PDF到本地以及其JPG格式 param:pdfUrl
     * @param $pdfUrl
     */
    public function actionSavePdfJpg($pdfUrl)
    {
        $this->stdout("PdfUrl：{$pdfUrl} \n");

        $pdfSavePath = File::savePdf($pdfUrl);

        $this->stdout("pdfSavePath：{$pdfSavePath} \n");
        exit(1);
    }

    public function actionSendEmail()
    {
        (new Email())->sendToAdmin('电子发票开具失败，新视窗返回错误码：dfdfdfdf');
    }

    /**
     * @param string $url
     */
    public function actionRequestNotify($url='')
    {
        $requestUrl = !empty($url) ? $url: 'http://www.51homemoney.com/tcis/fpzz-notify';
        $data = [
            'test' => 'name',
            'age' => 20,
            'sex' => 1,
            'project_house_id' => 1000,
        ];

        $this->http_post($requestUrl, $data);
    }

    /**
     * 检查认证用户真实性
     * @param $projectId
     * @throws \yii\base\ErrorException
     * @throws \yii\db\Exception
     */
    public function actionCheckAuthMember($projectId)
    {
        $countSql = 'SELECT COUNT(1) as count FROM (SELECT house_id FROM `member_house` WHERE `status` = 2 and `is_first` = 1 AND `group`=1) tb1 LEFT JOIN `house` h on tb1.house_id=h.`house_id` WHERE h.`project_house_id`= ' . $projectId;

        $selectSql = 'SELECT tb1.member_id FROM (SELECT house_id,member_id FROM `member_house` WHERE `status` = 2 and `is_first` = 1 AND `group`=1) tb1 LEFT JOIN `house` h on tb1.house_id=h.`house_id` WHERE h.`project_house_id`= ' . $projectId;;

//        echo $selectSql . PHP_EOL;
//        echo $countSql . PHP_EOL;

        $result = \Yii::$app->db->createCommand($selectSql)->queryColumn();

        $memberNumber = 0;
        $conNumber = 0;
        foreach($result as $key =>  $row){
            $member = Member::findOne(['id' => $row]);
            if($member) {
                echo 'kye:' . $key . '-------' . $member->phone . PHP_EOL;

                $newWindowsMember = (new NewWindow())->getCustomerInfo($projectId, $member->phone);
                if(isset($newWindowsMember['Response']['Data']['Record'][0]['CustomerName'])){
                    $memberNumber += 1;
                }

            } else {
                $conNumber +=1;
                echo 'kye:' . $key . '-------' . $row . PHP_EOL;
            }

        }

//        echo $memberNumber . PHP_EOL;
        $this->stdout("empty: {$conNumber}\n");
        $this->stdout("member: {$memberNumber}\n");
        exit();
        var_export($result);die;

        $authCount = \Yii::$app->db->createCommand($countSql)->queryOne();

        $this->stdout($authCount['count'] . PHP_EOL);
    }

    /**
     * @param int $memberId
     * @param string $url
     */
    public function actionNotifyWmpWechat(int $memberId, string $url)
    {
        $memberInfo = Member::findOne(['id' => $memberId]);
        $requestUrl = $url;

        $data = [
            'nickname' => $memberInfo->nickname,
            'publicName' => '幸福南奥',
        ];

        $this->http_post($requestUrl, $data);
    }

    public function actionShowHouseName($houseId)
    {
        $house = House::findOne(['house_id' => $houseId]);

        $this->stdout("HouseName：{$house->showName}");
    }

    /**
     * 检测车位管理费与房产 $houseId,$houseGroup,$memberId
     * @param $houseId
     * @param $houseGroup
     * @param $memberId
     * @throws \yii\base\ErrorException
     */
    public function actionCheckHousePayBill($houseId, $houseGroup, $memberId)
    {
        if($houseGroup == 2){
            $_list = (new NewWindow())->getBill($houseId);

            $pBillArray = [];
            $houseBillArray = [];
            $hBillDate = 0;
            $pBillDate = 0;

            if(count($_list) > 0){

                foreach($_list as $pRow){
                    $ShouldChargeDate = substr($pRow['ShouldChargeDate'], 0, 6);
                    $pAmount = bcsub($pRow['BillAmount'], $pRow['BillFines'], 2);
                    if($pAmount > 0){
                        $pBillArray[] = $ShouldChargeDate;
                    }
                }

                sort($pBillArray);
                $pBillDate = isset($pBillArray[0]) ? $pBillArray[0] : 0;

                $memberAllHouse = MemberHouse::find()
                    ->select('house_id')
                    ->where(['member_id' => $memberId, 'group' => MemberHouse::GROUP_HOUSE, 'status' => MemberHouse::STATUS_ACTIVE])
                    ->asArray()
                    ->all();
                if($memberAllHouse){

                    foreach($memberAllHouse as $hKey => $hV){
                        $billList = (new NewWindow())->getBill($hV['house_id']);

                        if(count($billList) > 0){
                            foreach($billList as $row){
                                $ShouldChargeDate = substr($row['ShouldChargeDate'], 0, 6);
                                $hBill = bcsub($row['BillAmount'], $row['BillFines'], 2);
                                if($hBill > 0){
                                    $houseBillArray[] = $ShouldChargeDate;
                                }
                            }

                            sort($houseBillArray);
                            if(isset($houseBillArray[0])){
                                $hBillDate = $houseBillArray[0];
                                break;
                            }
                        }
                    }

                    if(empty($hBillDate) && $pBillDate > 0){
                        $this->stdout('hbill is empty'. $pBillDate . PHP_EOL);die;
                    }

                    if(empty($hBillDate) && empty($pBillDate)){
                        $this->stdout('all is empty' . PHP_EOL);die;
                    }

                    if($pBillDate >= $hBillDate){
                        $this->stdout('pBillDate >= hBillDate'. $pBillDate . PHP_EOL);die;
                    }

                    $this->stdout('pBillDat：' . $pBillDate . PHP_EOL);
                    $this->stdout('hBillDate：' . $hBillDate . PHP_EOL);
                    $this->stdout('1' . PHP_EOL);die;
                }

                $this->stdout('pBillDat：' . $pBillDate . PHP_EOL);
                $this->stdout('hBillDate：' . $hBillDate . PHP_EOL);
                $this->stdout('2' . PHP_EOL);die;
            }

            $this->stdout('pBillDat：' . $pBillDate . PHP_EOL);
            $this->stdout('hBillDate：' . $hBillDate . PHP_EOL);
            $this->stdout('3' . PHP_EOL);die;
        }

        $this->stdout('4' . PHP_EOL);die;
    }

    public function actionRandomRed()
    {
//        $array = array_rand(3);
        $number = mt_rand(0, 20);
        $maxCash = 150; //100   平均每天可发 13 个
        $mediumCash = 5000; //10    平均每天发 417 个
        $minCash = 35000;    //1    平均每天可发 2917 个
        $maxDayNumber = 12;
        $minOut = 2;

        $sum = $maxCash + $mediumCash + $minCash;
        $maxAvg = $maxCash / $sum;
        $mediumAvg = $mediumCash / $sum;
        $minAvg = $minCash / $sum;
        $oneHundred = $maxCash / $maxDayNumber;
        $tenYuan = $mediumCash / $maxDayNumber;
        $oneYuan = $minCash / $maxDayNumber;

        $redis = \Yii::$app->redis;
        $redisMaxNumber = $redis->get('hundred');
        $maxSumNumber = $redis->get('maxSumNumber');


        $this->stdout("maxSumNumber：$maxSumNumber \n");
        $this->stdout("redisMaxNumber：$redisMaxNumber \n");
        $this->stdout("OneYuan：$oneYuan \n");
        $this->stdout("tenYuan：$tenYuan \n");
        $this->stdout("oneHundred：$oneHundred \n");
        $this->stdout("maxAvg：$maxAvg \n");
        $this->stdout("mediumAvg：$mediumAvg \n");
        $this->stdout("minAvg：$minAvg \n");
        $this->stdout("$number \n");
    }

    public function actionTestRedPack()
    {
        /*$redis = \Yii::$app->redis;
        $maxSumNumber = $redis->get('maxSumNumber');
        $redisHundred = $redis->get('oneHundredYuanNumber');
        $redisTenYuan = $redis->get('tenYuanNumber');
        $redisOneYuanNumber = $redis->get('oneYuanNumber');


        $this->stdout("maxSumNumber：$maxSumNumber \n");
        $this->stdout("redisHundredNumber：$redisHundred \n");
        $this->stdout("redisTenYuan：$redisTenYuan \n");
        $this->stdout("redisOneYuanNumber：$redisOneYuanNumber \n");
        $this->stdout("Done \n");*/


        $this->buildRedPackArray();
    }

    private function buildRedPackArray()
    {
        $redis = \Yii::$app->redis;

        $redArray = [];
        $maxNumber = $redis->get('oneHundredYuanNumber');
        $number = $redis->get('tenYuanNumber');
        $minNumber = $redis->get('oneYuanNumber');

        $oneHundredYuan = 100;
        $tenYuan = 10;
        $arrayLength = 20;

        if($maxNumber > 0){
            array_push($redArray, $oneHundredYuan);
        }

        if($number > 0){
            $redArray = array_pad($redArray, 3, $tenYuan);
        }

        $count = count($redArray);
        if(!$count){
            return false;
        }

        for($i=0; $i <= $arrayLength - $count; $i++){
            array_push($redArray, 1);
        }
        shuffle($redArray);

        $count = count($redArray);
        for($k=0; $k<$count; $k++){
            $this->stdout("{$redArray[$k]} \n");
        }

        $arrayValue = $redArray[array_rand($redArray)];
        $this->stdout("随机输出：$arrayValue \n");


        die;

        for($j=0; $j<1000; $j++){
            $arrayValue = $redArray[array_rand($redArray)];

            switch($arrayValue){
                case 1:
                    $redis->decr('oneYuanNumber');
                    break;
                case 10:
                    $redis->decr('tenYuanNumber');
                    break;
                case 100:
                    $redis->decr('oneHundredYuanNumber');
                    break;
            }

            if($arrayValue > 1){
                $this->stdout("第 $j 次：$arrayValue \n");
            }
        }

    }

    /**
     * POST 请求
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @return string content
     */
    private function http_post($url, $param, $post_file = false)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }
    public function actionDemoNew($house_id=0)
    {
        $_list = (new NewWindow())->getBill($house_id);
        print_r($_list);
       // $this->stdout($house_id."\n");
    }

}