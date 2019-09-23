<?php
/**
 * 民生
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2019/8/28
 * Time: 15:07
 */
namespace components\minSheng;

require_once ("php_java.php");

use yii\base\Exception;
use yii\log\FileTarget;

class MinSheng
{

    //微信公众号跳转支付请求地址
    const TEST_API_URL = 'https://wxpay.cmbc.com.cn/mobilePlatform/';   //测试环境host
    const API_URL = 'https://epay.cmbc.com.cn/appweb/';                 //正式环境host

    const JSAPI = "appserver/lcbpPay.do";                       //js-api支付
    const CANCEL = "appserver/cancelTrans.do";                  //退款
    const CHECKRESULT = 'appserver/paymentResultSelectNew.do';  //支付查询

    const PLATFORM_ID = 'A00002019090001146071'; //接入平台号
    const MERCHANT_ID = 'M03002019090001146268'; //民生商户号

    //支付类型
    const SELECT_TRADE_WXJSAPI = "H5_WXJSAPI";      //微信公众号跳转支付
    const SELECT_TRADE_WXMWEB = "H5_WXMWEB";        //微信 H5 支付
    const SELECT_TRADE_WXQRCODE = "API_WXQRCODE";   //扫码支付

    const NOTIFY_URL = "https://www.51homemoney.com/min-sheng/wx-notify";  //回调
    const CMBC_PUBLIC_CERT_PATH = "/cert/cmbc.cer";             //民生公钥
    const CUSTOMER_PUBLIC_CERT_PATH = "/cert/cust0001.cer";     //商户公钥
    const CUSTOMER_PRIVATE_CERT_PATH = "/cert/cust0001.sm2";    //商户私钥
    const CUSTOMER_PRIVATE_CERT_PASSWORD = "111111";

    const SIGN_ALG = "SM3withSM2";
    const ENCRYPT_ALG = "SM4/CBC/PKCS7Padding";

    /**
     * API统一下单
     * @param array $orderInfo
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @author zhaowenxi
     */
    public function submitOrderInfo($orderInfo = []){
//        $orderInfo = [
//            'platformId' => self::PLATFORM_ID,
//            'merchantNo' => self::MERCHANT_ID,
//            'selectTradeType' => self::SELECT_TRADE_WXJSAPI,
//            'amount' => '1',
//            'orderInfo' => "物业费test",
//            'merchantSeq' => self::PLATFORM_ID . date("Ymdhis", time()) . rand(0,100),
//            'mchSeqNo'  => "billtest" . date("Ymdhis", time()) . rand(0,100),
//            'transDate' => date('Ymd', time()),
//            'transTime' => date('Ymdhis', time()),
//            'notifyUrl' => self::NOTIFY_URL,
//            'subAppId' => \Yii::$app->params['wechat']['appId'],
//            'subOpenId' => 'o4sxcxLBzmvgpO_0BVo9gPbkQWxE'
//        ];

        if(!$orderInfo){
            return ['code' => 1];
        }

        $orderJson = json_encode($orderInfo, JSON_UNESCAPED_UNICODE);

        $customerPrivateCert = file_get_contents(dirname(__FILE__).self::CUSTOMER_PRIVATE_CERT_PATH);

        //签名
        $signRes = $this->makeP1sign(
            self::SIGN_ALG,
            base64_encode($orderJson),
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $signObj = json_decode(trim(preg_replace('/\s/', '', $signRes)));

        if($signObj->Code == '90000000'){
            $sign = $signObj->Base64SignatureData;
        }else{
            return ['code' => 2];
        }

        $mgJson = json_encode(['sign' => $sign, 'body' => $orderJson], JSON_UNESCAPED_UNICODE);

        $cmbcPublicCert = trim(str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----"],
            "",
            file_get_contents(dirname(__FILE__).self::CMBC_PUBLIC_CERT_PATH)
        ));

        //加密
        $mgRes = $this->makeEnvelope($mgJson, self::ENCRYPT_ALG, $cmbcPublicCert);

        $mgObj = json_decode(trim(preg_replace('/\s/', '', $mgRes)));

        if($mgObj->Code != '90000000'){
            return ['code' => 3];   //加密错误
        }

        //调用下单支付
        $businessJson = json_encode([
            'businessContext' => $mgObj->Base64EnvelopeMessage
        ]);

        $billRes = self::curlPost(self::API_URL . self::JSAPI, $businessJson);

        $this->queryLog("minshengQuery", $billRes, json_encode($orderInfo), '');

        $billObj = json_decode(trim(preg_replace('/\s/', '', $billRes)));

        if(!isset($billObj->gateReturnType) || $billObj->gateReturnType != 'S'){
            return ['code' => 4];   //API请求错误
        }

        //解密
        $openMg = $this->openEnvelope(
            $billObj->businessContext,
            self::ENCRYPT_ALG,
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', $openMg)));

        if($openMgObj->Code != '90000000'){
            return ['code' => 5];   //解密错误
        }

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', base64_decode($openMgObj->Base64SourceString))));

        $mgData = $openMgObj->body;

        $payObj = json_decode($mgData);

        $result = [];

        if($payObj){
            $payInfo = explode('|', $payObj->payInfo);


            foreach ($payInfo as $k => $v){

                $result[substr($v, 0, strpos($v, '='))] = substr(strstr($v, '='), 1);
            }
        }

        if(!$result || empty($result)){
            return ['code' => 6];   //返回支付数据有误
        }

//        //验签
//        $openSign = $this->openP1sign(
//            self::SIGN_ALG,
//            $openMgObj->body,
//            self::CMBC_PUBLIC_CERT_PATH,
//            $openMgObj->sign);
//
//        $openSignObj = json_decode(trim(preg_replace('/\s/', '', $openSign)));
//
//        if($openSignObj->Code != '90000000'){
//            return ['code' => 7];   //验签错误
//        }

        return ['code' => 0, 'data' => $result];
    }

    public function openNotify($context){

        $arr = json_decode($context);


        if(empty($arr)){
            $this->notifyLog($context, 1);
            return ['code' => 1, 'data' => $context];
        }

        $customerPrivateCert = file_get_contents(dirname(__FILE__).self::CUSTOMER_PRIVATE_CERT_PATH);

        //解密
        $openMg = $this->openEnvelope(
            $arr->context,
            self::ENCRYPT_ALG,
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', $openMg)));

        if($openMgObj->Code != '90000000'){
            $this->notifyLog($openMg, 2);
            return ['code' => 2, 'data' => $openMg];
        }

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', base64_decode($openMgObj->Base64SourceString))));

        $notifyResult = json_decode($openMgObj->body);

        if($notifyResult->tradeStatus == "S"){
            $this->notifyLog(json_encode($notifyResult), 0);
            return ['code' => 0, 'data' => $notifyResult];
        }

        $this->notifyLog(trim(preg_replace('/\s/', '', base64_decode($openMgObj->Base64SourceString))), 4);
        return ['code' => 4, 'data' => trim(preg_replace('/\s/', '', base64_decode($openMgObj->Base64SourceString)))];
    }

    public function refund($data){

        if(!$data){
            return ['code' => 1];
        }

        $orderJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $customerPrivateCert = file_get_contents(dirname(__FILE__).self::CUSTOMER_PRIVATE_CERT_PATH);

        //签名
        $signRes = $this->makeP1sign(
            self::SIGN_ALG,
            base64_encode($orderJson),
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $signObj = json_decode(trim(preg_replace('/\s/', '', $signRes)));

        if($signObj->Code == '90000000'){
            $sign = $signObj->Base64SignatureData;
        }else{
            return ['code' => 2];
        }

        $mgJson = json_encode(['sign' => $sign, 'body' => $orderJson], JSON_UNESCAPED_UNICODE);

        $cmbcPublicCert = trim(str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----"],
            "",
            file_get_contents(dirname(__FILE__).self::CMBC_PUBLIC_CERT_PATH)
        ));

        //加密
        $mgRes = $this->makeEnvelope($mgJson, self::ENCRYPT_ALG, $cmbcPublicCert);

        $mgObj = json_decode(trim(preg_replace('/\s/', '', $mgRes)));

        if($mgObj->Code != '90000000'){
            return ['code' => 3];   //加密错误
        }

        //调用退款
        $businessJson = json_encode([
            'businessContext' => $mgObj->Base64EnvelopeMessage
        ]);

        $billRes = self::curlPost(self::API_URL . self::CANCEL, $businessJson);

        $this->queryLog("minshengRefund", $billRes, json_encode($data), '');

        $billObj = json_decode(trim(preg_replace('/\s/', '', $billRes)));

        if(!isset($billObj->gateReturnType) || $billObj->gateReturnType != 'S'){
            return ['code' => 4, 'data' => isset($billObj->gateReturnMessage) ? $billObj->gateReturnMessage : ''];   //API请求错误
        }

        //解密
        $openMg = $this->openEnvelope(
            $billObj->businessContext,
            self::ENCRYPT_ALG,
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', $openMg)));

        if($openMgObj->Code != '90000000'){
            return ['code' => 5];   //解密错误
        }

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', base64_decode($openMgObj->Base64SourceString))));

        $mgData = json_decode($openMgObj->body);

        if(!$mgData){
            return ['code' => 6];   //数据有误
        }

        return ['code' => 0, 'data' => $mgData];
    }

    public function orderCheck($data){

        if(!$data){
            return ['code' => 1];
        }

        $orderJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        $customerPrivateCert = file_get_contents(dirname(__FILE__).self::CUSTOMER_PRIVATE_CERT_PATH);

        //签名
        $signRes = $this->makeP1sign(
            self::SIGN_ALG,
            base64_encode($orderJson),
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $signObj = json_decode(trim(preg_replace('/\s/', '', $signRes)));

        if($signObj->Code == '90000000'){
            $sign = $signObj->Base64SignatureData;
        }else{
            return ['code' => 2];
        }

        $mgJson = json_encode(['sign' => $sign, 'body' => $orderJson], JSON_UNESCAPED_UNICODE);

        $cmbcPublicCert = trim(str_replace(
            ["-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----"],
            "",
            file_get_contents(dirname(__FILE__).self::CMBC_PUBLIC_CERT_PATH)
        ));

        //加密
        $mgRes = $this->makeEnvelope($mgJson, self::ENCRYPT_ALG, $cmbcPublicCert);

        $mgObj = json_decode(trim(preg_replace('/\s/', '', $mgRes)));

        if($mgObj->Code != '90000000'){
            return ['code' => 3];   //加密错误
        }

        //调用查询
        $businessJson = json_encode([
            'businessContext' => $mgObj->Base64EnvelopeMessage
        ]);

        $billRes = self::curlPost(self::API_URL . self::CHECKRESULT, $businessJson);

        $this->queryLog("minshengCheck", $billRes, json_encode($data), '');

        $billObj = json_decode(trim(preg_replace('/\s/', '', $billRes)));

        if(!isset($billObj->gateReturnType) || $billObj->gateReturnType != 'S'){
            return ['code' => 4];   //API请求错误
        }

        //解密
        $openMg = $this->openEnvelope(
            $billObj->businessContext,
            self::ENCRYPT_ALG,
            $customerPrivateCert,
            self::CUSTOMER_PRIVATE_CERT_PASSWORD
        );

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', $openMg)));

        if($openMgObj->Code != '90000000'){
            return ['code' => 5];   //解密错误
        }

        $openMgObj = json_decode(trim(preg_replace('/\s/', '', base64_decode($openMgObj->Base64SourceString))));

        $mgData = json_decode($openMgObj->body);

        $this->queryLog("minshengCheck", "number = " . $data['merchantSeq'], $openMgObj->body, '');

        if(!$mgData){
            return ['code' => 6];   //数据有误
        }

        return ['code' => 0, 'data' => $mgData];
    }

    /**
     * 生成数字信封方法，将返回加密串放到API公共报文头
     * @param $base64SourceData
     * @param $signAlg
     * @param $base64CertData
     * @return \Exception|mixed
     * @author zhaowenxi
     */
    private function makeEnvelope($base64SourceData, $signAlg, $base64CertData){

        try {
            $ret = lajp_call("cfca.sadk.api.EnvelopeKit::envelopeMessage", $base64SourceData,$signAlg, $base64CertData);

            return $ret;
        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    private function makeP1sign($signAlg,$base64SourceData,$base64P12Data,$p12Password){
        try {
            $ret = lajp_call("cfca.sadk.api.SignatureKit::P1SignMessage",$signAlg,$base64SourceData,$base64P12Data,$p12Password);

            return $ret;
        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    private function openEnvelope($base64EnvelopeData, $signAlg, $base64P12Data, $p12Password){
        try {
            $ret = lajp_call("cfca.sadk.api.EnvelopeKit::openEvelopedMessage",  $base64EnvelopeData,$signAlg, $base64P12Data, $p12Password);

            return $ret;
        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    private function openP1sign($signAlg, $base64SourceData, $base64X509CertData, $base64P1SignatureData){
        try {
            $ret = lajp_call("cfca.sadk.api.SignatureKit::P1VerifyMessage", $signAlg,$base64SourceData, $base64X509CertData,$base64P1SignatureData);

            return $ret;
        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    /**
     * 发送post请求
     * @param string $url 链接
     * @param string $data 数据
     * @param string $headers 请求头
     * @return bool|mixed
     */
    public static function curlPost($url, $data = null)
    {
        $curl = curl_init();

        $data && curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);

        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * @param $fileName
     * @param $log
     * @param $orderInfo
     * @param $contextJson
     * @throws \yii\base\InvalidConfigException
     * @author zhaowenxi
     */
    private function queryLog($fileName, $log, $orderInfo, $contextJson)
    {
        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . "/logs/{$fileName}.log";
        $fileLog->messages[] = [[$log, $orderInfo, $contextJson], 8, 'application', microtime(true)];
        $fileLog->export();
    }

    /**
     * @param $log
     * @param $code
     * @param string $result
     * @throws \yii\base\InvalidConfigException
     * @author zhaowenxi
     */
    private function notifyLog($log, $code)
    {
        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/minshengNotify.log';
        $fileLog->messages[] = [[$log, $code], 8, 'application', microtime(true)];
        $fileLog->export();
    }
}