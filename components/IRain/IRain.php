<?php
/**
 * de:艾润道闸接口
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/4/9
 * Time: 11:19
 */

namespace components\IRain;


use yii\log\FileTarget;

class IRain
{
    const PAY_TYPE_WECHAT = 1;  //微信
    const PAY_TYPE_ALIPAY = 2;  //支付宝

    public $appId;
    public $appSecret;
//    public $host = 'http://test.parkingwang.com:8280/'; //测试环境
    public $host = 'http://api.parkingwang.com:8280/'; //正式环境
    public $errorTips = [
        'NO_PARK_RECORD' => '无停车记录',
        'EXIT_CAR_NOT_EXIST' => '出口未识别到车牌',
        'GET_BILL_FAILED' => '获取账单失败',
    ];

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * @return array
     */
    public function sign()
    {
        //算法为将appid,appsecret,timestamp 按顺序拼接做md5所得
        $time = $this->getTime();
        $string = $this->appId . $this->appSecret . $time;
        $sign = md5($string);

        return [
            'sign' => $sign,
            'timestamp' => $time,
        ];
    }

    /**
     * 公共参数
     * @return array
     */
    public function getDefaultData()
    {
        $sign = $this->sign();

        $data = [
            'appid' => $this->appId,
            'sign' => $sign['sign'],
            'timestamp' => $sign['timestamp'],
        ];

        return $data;
    }

    /**
     * 请求查询临卡费用
     * @param $data
     * @return bool|mixed
     */
    public function getParkingFeeTempCalcFee($data)
    {
        $defaultData = $this->getDefaultData();
        $postData = array_merge($defaultData, $data);

        $url = $this->host . 'bill/Query';
        $res = $this->http_post($url, $postData);

        return $this->getValue($res);
    }

    /**
     * 创建临卡费用订单
     * @param $data
     * @return bool|mixed
     */
    public function createParkingTempPayOrder($data)
    {
        $defaultData = $this->getDefaultData();
        $postData = array_merge($defaultData, $data);

        $url = $this->host . 'pay/Issued';
        $res = $this->http_post($url, $postData);

        return $this->getValue($res);
    }

    public function getValue(&$res)
    {
        if(!$res){
            return false;
        }

        if(isset($res['data']) && is_array($res['data'])){
            return $res;
        }

        if(isset($this->errorTips[$res['code']])){
            $res['tipsMsg'] = $this->errorTips[$res['code']];
        } else {
            $res['tipsMsg'] = '网络异常，请去收费岗缴费！';
        }

        return $res;
    }

    public function getTime()
    {
        return time();
    }

    /**
     * @param $url
     * @param $data
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     */
    private function http_post($url, $data)
    {
        $data = http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $responseStatus = curl_getinfo($ch);
        curl_close($ch);

        $this->log($data, $url);

        if (intval($responseStatus["http_code"]) == 200) {
            return json_decode($response, 1);
        } else {
            return false;
        }
    }

    /**
     * 记录请求参数
     * @param $msg
     * @param string $requestUrl
     * @throws \yii\base\InvalidConfigException
     */
    protected function log($msg, $requestUrl='')
    {
//        $msg['url'] = $requestUrl;
//        $msgLog = serialize($msg);
        $msgLog = $msg;

        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/irain.log';
        $fileLog->messages[] =  [$msgLog, 8, 'application', microtime(true)];;
        $fileLog->export();
    }

}