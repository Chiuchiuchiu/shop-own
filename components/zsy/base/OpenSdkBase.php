<?php

/**
 * Created by Mixiu.
 * Date: 2015/12/2
 * Time: 13:25
 */
namespace components\zsy\base;

class OpenSdkBase
{

    public $appId;
    public $keySecret;
    public $sourceNo;
    public $protocol;
    public $apiHost;
    public $merchantNo;
    protected $_accessToken = null;


    protected $_params = array();

    /**
     * ACCESS_TOKEN 操作授权
     */
    const ACCESS_TOKEN_SCOPE = 'ACCESSTOKEN';

    /**
     * 获取ACCESS_TOKEN的Url
     */
    const ACCESS_TOKEN_URL = '/auth/service_access_token';

    const WIDGET_PAGE_URL = '/service/widget_page';

    /**
     * 收银台地址Url
     */
    const CASH_DESK_URL = '/service/cash_desk';


    /**
     * 创建会员Url
     */
    const CREATE_USER_URL = '/service/createUser';

    /**
     * 更新会员Url
     */
    const UPDATE_USER_URL = '/service/updateUser';

    /**
     * 商家二级结算--普通结算Url
     */
    const CHILD_MERCHANT_COMMON_SETTLE_URL = '/service/childMerchantCommonSettle';

    /**
     * 商家二级结算--订单结算
     */
    const CHILD_MERCHANT_ORDER_SETTLE_URL = '/service/childMerchantOrderSettle';


    public function __construct($appId, $keySecret, $sourceNo)
    {
        $this->appId = $appId;
        $this->keySecret = $keySecret;
        $this->sourceNo = $sourceNo;
    }



    /**
     * 通过授权获取AccessToken
     * @throws Exception
     */
    protected function getAccessTokenByUrl($mainParams)
    {

        $params = array_merge(array(
            'appId' => $this->appId,
            'scope' => 'ACCESSTOKEN',
            'sourceNo' => $this->sourceNo
        ),$mainParams);

        $response = $this->get($this->accessTokenUrl(), $params);

//        Log::write("accesstoken获取返回参数：" . $response);

        return $response;

    }

    public function getAccessToken($mainParams = array())
    {
        if (!$this->_accessToken) {
            $cacheToken = \Yii::$app->cache->get('ZSYAccessToken');
            if($cacheToken){
                $response = $cacheToken;
            }else{
                $response = json_decode($this->getAccessTokenByUrl($mainParams), true);
            //  实际开发过程中把access_token进行缓存处理，这样就不需要每次都请求
            }
            if (isset($response['code']) && $response['code'] == 1) {
                \Yii::$app->cache->set('ZSYAccessToken',$response,60);
                $this->_accessToken = $response['data']['access_token'];
            } else {
                var_dump($response);
                throw new \Exception(isset($response['message']) ? $response['message'] : $response);
            }
        }

        return $this->_accessToken;
    }

    /**
     * 获取accesstoken的Url
     * @return string
     */
    public function accessTokenUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::ACCESS_TOKEN_URL;
    }

    public function widgetPageUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::WIDGET_PAGE_URL;
    }

    public function createUserUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::CREATE_USER_URL;
    }

    public function updateUserUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::UPDATE_USER_URL;
    }

    public function cashDeskUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::CASH_DESK_URL;
    }

    public function childMerchantCommonSettleUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::CHILD_MERCHANT_COMMON_SETTLE_URL;
    }
    public function childMerchantOrderSettleUrl()
    {
        return $this->protocol . '://' . $this->apiHost . self::CHILD_MERCHANT_ORDER_SETTLE_URL;
    }
    public function generalGetUrl()
    {
        return $this->protocol . '://' . $this->apiHost . '/service/general_get';
    }
    public function h5AssetsUrl()
    {
        return $this->protocol . '://' . $this->apiHost . '/service/h5/access';
    }

    /**
     * 对参数进行签名
     * @param $params
     * @return string
     */
    public function sign($params)
    {
        $string = $this->formatParams($params) . $this->keySecret;
        return md5($string);

    }


    /**
     * 参数格式化并且排序
     * @param $params
     * @return string
     */
    public function formatParams($params)
    {
        $buff = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $buff .= $k . '=' . $v;
        }
        return $buff;
    }


    /**
     * GET 请求
     * @param $url
     * @param array $params
     * @return mixed
     */
    public function get($url, $params = array())
    {

        return $this->request($url, 'GET', $params);
    }

    /**
     * POST请求
     * @param $url
     * @param array $params
     * @return mixed
     */
    public function post($url, $params = array())
    {
        return $this->request($url, 'POST', $params);
    }

    /**
     * 发起一个HTTP请求
     * @param $url
     * @param $method
     * @param $params
     * @return mixed
     */
    public function request($url, $method, $params)
    {
        $params['appId'] = $this->appId;
        $params['sourceNo'] = $this->sourceNo;
        $params['merchantNo'] = $this->merchantNo;
        if(!isset($params['timestamp'])) {
            list($t1, $t2) = explode(' ', microtime());
            $params['timestamp'] = (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
        }
        if(!isset($params['signature'])) {
            $sign = $this->sign($params);
            $params['signature'] = $sign;
        }
        switch ($method) {
            case 'GET':
                $url = $url . '?' . http_build_query($params);
                return $this->http($url, 'GET');
            default :
                $body = http_build_query($params);
                return $this->http($url, $method, $body);
        }
    }

    /**
     * HTTP请求 基于CURL
     * @param $url
     * @param $method
     * @param null $postfields
     * @param array $headers
     * @return mixed
     */
    public function http($url, $method, $postfields = null, $headers = array())
    {
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        if (version_compare(phpversion(), '5.4.0', '<')) {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
        } else {
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 2);
        }
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        if ("POST" === $method) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);

            }
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }
}