<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/8
 * Time: 11:49
 */

namespace components\genvict;


class Genvict
{
    /**
     *  appid：20171108091116882
     *  appkey：zacdj20171108mkcdl
     *  parkingid:20170104000000000002
     *  测试接口地址：http://topenapi.parkingjet.com:8082/CommonOpenApi/default.ashx
     *  月卡：苏EG26J6
     *  临卡：冀AQ0B00
     *  临卡车辆：粤B12345
     *  月卡车辆：粤A00002
     * 广州奥园项目 Parking：07550001120170703000000000002
     */

//    const REQUEST_URL = 'http://topenapi.parkingjet.com:8082/CommonOpenApi/default.ashx?';
    const REQUEST_URL = 'http://openapi.parkingjet.com/CommonOpenApi/default.ashx?';
    const METHOD_PARKING_INFO = 'parkingjet.open.s2s.thirdpay.parking.info';    //查询车场详细信息
    const METHOD_PARKING_TEMP_CALC = 'parkingjet.open.s2s.parkingfee.temp.calcfee.plateno'; //获取临时卡算费信息
    const METHOD_PARKING_TEMP_PAY_ORDER_CREATE = 'parkingjet.open.s2s.parkingfee.temp.payorder.create'; //创建临卡订单信息
    const METHOD_PARKING_MONTH_CALC = 'parkingjet.open.s2s.parkingfee.month.calcfee.plateno';   //获取月卡算费信息
    const METHOD_PARKING_MONTH_PAY_ORDER_CREATE = 'parkingjet.open.s2s.parkingfee.month.payorder.create';   //创建月卡订单信息
    const METHOD_PARKING_ORDER_RESULT = 'parkingjet.open.s2s.parkingfee.orderresult.search';    //查询支付结果
    const METHOD_PARKING_PAY_RESULT_NOTIFY = 'parkingjet.open.s2s.parkingfee.payresult.notify'; //临卡支付完成通知

    public $appid = '20171108091116882';
    public $appkey = 'zacdj20171108mkcdl';
    public $version = '1.0';
    public $errMsg = '';
    public $errCode = '';
    public $value = [];
    public $errorTips = [
        '50-11-15' => '网络异常，请去收费岗缴费！',
        '50-11-09' => '此卡为月保卡，无需临卡算费',
        '50-11-05' => '无该车入场纪录，请找收费岗处理。',
    ];

    public function __construct($appid=null, $appkey=null, $version=null)
    {
        $this->appid = !empty($appid) ? $appid : $this->appid;
        $this->appkey = !empty($appkey) ? $appkey : $this->appkey;
        $this->version = !empty($version) ? $version : $this->version;
    }

    public function sign($methodName, $data)
    {
        $time = self::getTime();
        $params = [
            'appid=' . $this->appid,
            'methodname=' . $methodName,
            'timestamp=' . $time,
            'version=' . $this->version,
            'postdata=' . json_encode($data),
        ];
        asort($params);

        $proParams = implode('&', $params);
        $proParams = $proParams . $this->appkey;

        return [
            'sign' => md5($proParams),
            'time' => $time,
        ];
    }

    /**
     * 获取车场详细信息
     * @param array $postData
     * @return $this
     */
    public function getParkingInfo($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_INFO, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_INFO;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);
        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }

            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];
        }

        return $this;
    }

    /**
     * 获取临时卡算费信息
     * @param array $postData
     * @return $this
     */
    public function getParkingFeeTempCalcFeePlateno($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_TEMP_CALC, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_TEMP_CALC;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);

        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }
            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];

        }

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getErrors()
    {
        $tipsMsg = isset($this->errorTips[$this->errCode]) ? $this->errorTips[$this->errCode] : '服务异常，请去收费岗缴费！';

        return [
            'msg' => $this->errMsg,
            'code' => $this->errCode,
            'tipsMsg' => $tipsMsg,
        ];
    }

    /**
     * 创建临卡订单信息
     * @param array $postData
     * @return $this
     */
    public function createParkingTempPayOrder($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_TEMP_PAY_ORDER_CREATE, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_TEMP_PAY_ORDER_CREATE;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);
        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }

            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];
        }

        return $this;
    }

    /**
     * 获取月卡算费信息
     * @param array $postData
     * @return $this
     */
    public function getParkingMonthCalcFeePlateno($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_MONTH_CALC, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_MONTH_CALC;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);
        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }

            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];
        }

        return $this;
    }

    /**
     * 创建月卡订单信息
     * @param array $postData
     * @return $this
     */
    public function createParkingMonthPayOrder($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_MONTH_PAY_ORDER_CREATE, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_MONTH_PAY_ORDER_CREATE;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);
        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }

            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];
        }

        return $this;
    }

    /**
     * 查询支付结果
     * @param array $postData
     * @return $this
     */
    public function getOrderResult($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_ORDER_RESULT, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_ORDER_RESULT;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);
        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }

            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];
        }

        return $this;
    }

    /**
     * 支付完成通知
     * @param array $postData
     * @return $this
     */
    public function payResultNotify($postData)
    {
        $sing = $this->sign(self::METHOD_PARKING_PAY_RESULT_NOTIFY, $postData);

        $url = self::REQUEST_URL . 'methodname='. self::METHOD_PARKING_PAY_RESULT_NOTIFY;
        $url .= '&appid=' .$this->appid.'&timestamp='. $sing['time'];
        $url .= '&version='. $this->version .'&sign=' . $sing['sign'];

        $data = [
            'postdata' => json_encode($postData),
        ];

        $res = $this->http_post($url, $data);
        if($res){
            if($res['Status'] == 1){
                $this->value = $res;
            }

            $this->errMsg = $res['Message'];
            $this->errCode = $res['ErrorCode'];
        }

        return $this;
    }

    protected static function getTime()
    {
        return date('YmdHis', time());
    }

    private function http_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        $responseStatus = curl_getinfo($ch);
        curl_close($ch);

        if (intval($responseStatus["http_code"]) == 200) {
            return json_decode($response, 1);
        } else {
            return false;
        }
    }

}