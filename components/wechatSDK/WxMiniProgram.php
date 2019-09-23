<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/26
 * Time: 16:36
 */

namespace components\wechatSDK;


use common\models\MiniWxLoginLog;
use components\helper\File;
use yii\base\Exception;

class WxMiniProgram
{
    private $appid;
    private $appsecret;
    private $access_token;
    public $errCode = 40001;
    public $errMsg = "no access";

    const BASE_URL = "https://api.weixin.qq.com/";

    //登录凭证校验
    const SNS_JSCODE2SESTION = "sns/jscode2session?";
    const TOKEN_URL = 'cgi-bin/token?grant_type=client_credential&';
    const WXAQRCODE_URL = 'cgi-bin/wxaapp/createwxaqrcode?';

    public function __construct($options)
    {
        $this->appid = isset($options['appId']) ? $options['appId'] : '';
        $this->appsecret = isset($options['appSecret']) ? $options['appSecret'] : '';

    }

    /**
     * 登录凭证校验
     * @param $jsCode
     * @param string $grantType
     * @return bool|mixed
     * @author zhaowenxi
     */
    public function code2Session($jsCode, $grantType = "authorization_code"){

        $url = self::BASE_URL . self::SNS_JSCODE2SESTION .
            'appid=' . $this->appid .
            '&secret=' . $this->appsecret .
            '&js_code=' . $jsCode .
            '&grant_type=' . $grantType;

        $res = $this->http_get($url);

        $params = explode('&', substr($url, strpos($url, "?") + 1));

        //记录微信登录log
        MiniWxLoginLog::writesLog($params, $res, \Yii::$app->request->getUserIP());

        if ($res) {

            return json_decode($res, true);

        }

        return false;
    }

    /**
     * 获取access_token
     * @author HQM 2018/11/26
     * @param string $appid 如在类初始化时已提供，则可为空
     * @param string $appsecret 如在类初始化时已提供，则可为空
     * @param string $token 手动指定access_token，非必要情况不建议用
     * @return string|bool
     */
    public function getAccessToken($appid = '', $appsecret = '', $token = '')
    {
        if (!$appid || !$appsecret) {
            $appid = $this->appid;
            $appsecret = $this->appsecret;
        }
        if ($token) { //手动指定token，优先使用
            $this->access_token = $token;
            return $this->access_token;
        }

        $authName = 'mp_access_token' . $appid;
        $rs = $this->getCache($authName);
        if ($rs) {
            $this->access_token = $rs;
            return $rs;
        }

        $result = $this->http_get(self::BASE_URL . self::TOKEN_URL . 'appid=' . $appid . '&secret=' . $appsecret);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->access_token = $json['access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in']) - 100 : 3600;
            $this->setCache($authName, $this->access_token, $expire);

            return $this->access_token;
        }

        return false;
    }

    /**
     * 获取小程序二维码(方形)，适用于数量较少的业务场景
     * @author HQM 2018/11/26
     * @param $data
     * @return bool|mixed
     * @throws Exception
     */
    public function createWxQrCode($data)
    {
        $accessToken = $this->getAccessToken();
        if (!$this->access_token && !$accessToken) return false;

        $result = $this->http_post(self::BASE_URL . self::WXAQRCODE_URL . 'access_token=' . $this->access_token, self::json_encode($data));

        if ($result) {
            $json = json_decode($result, true);
            if (!$json) {
                $name = File::makeName();
                $path = 'public/' . date('Wy');
                $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']) . $path;
                if (!file_exists($savePath)){
                    @mkdir($savePath);
                }
                if (!is_dir($savePath)) {
                    throw new Exception('can not make dir');
                }

                file_put_contents($savePath.'/'.$name, $result);
                $savePath = '@cdnUrl/'.$path . '/' . $name;

                return $savePath;
            } else {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
        }

        return false;
    }

    /**
     * 获取小程序码(圆)，用于数量较少的业务场景
     * @author HQM 2018/11/26
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function getWXACode($data)
    {
        $accessToken = $this->getAccessToken();
        if (!$this->access_token && !$accessToken) return false;

        $url = 'wxa/getwxacode?';
        $result = $this->http_post(self::BASE_URL . $url . 'access_token=' . $this->access_token, self::json_encode($data));

        if ($result) {
            $json = json_decode($result, true);
            if (!$json) {
                $name = File::makeName();
                $path = date('Wy');
                $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']) . 'public/' .  $path;
                if (!file_exists($savePath)){
                    @mkdir($savePath);
                }
                if (!is_dir($savePath)) {
                    throw new Exception('can not make dir');
                }

                file_put_contents($savePath.'/'.$name, $result);
                $savePath = '@cdnUrl/'.$path . '/' . $name;

                return $savePath;
            } else {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
        }

        return false;
    }

    /**
     * 获取小程序码（圆），用于数量较多的业务场景
     * @author HQM 2018/11/26
     * @param $data
     * @return bool|string
     * @throws Exception
     */
    public function getWXACodeUnlimit($data)
    {
        $accessToken = $this->getAccessToken();
        if (!$this->access_token && !$accessToken) return false;

        $url = 'wxa/getwxacodeunlimit?';
        $result = $this->http_post(self::BASE_URL . $url . 'access_token=' . $this->access_token, self::json_encode($data));

        if ($result) {
            $json = json_decode($result, true);
            if (!$json) {
                $name = File::makeName();
                $path = 'public/' . date('Wy');
                $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']) . $path;
                if (!file_exists($savePath)){
                    @mkdir($savePath);
                }
                if (!is_dir($savePath)) {
                    throw new Exception('can not make dir');
                }

                file_put_contents($savePath.'/'.$name, $result);
                $savePath = '@cdnUrl/'.$path . '/' . $name;

                return $savePath;
            } else {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
        }

        return false;
    }

    /**
     * 发送模板消息
     * @author HQM 2018/12/11
     * @param array $data
     * @return bool|mixed
     */
    public function sendTemplateMessage(array $data)
    {
        $accessToken = $this->getAccessToken();
        if (!$this->access_token && !$accessToken) return false;

        $url = 'cgi-bin/message/wxopen/template/send?';
        $result = $this->http_post(self::BASE_URL . $url . 'access_token=' . $this->access_token, self::json_encode($data));

        if($result){
            $json = json_decode($result, true);
            if($json['errcode'] == 0){
                return $json;
            }
        }

        return false;
    }

    /**
     * 下发小程序和公众号统一服务消息
     * //<https://developers.weixin.qq.com/miniprogram/dev/api/sendUniformMessage.html>
     * @author HQM 2018/12/11
     * @param array $data
     * @return bool|mixed
     */
    public function sendUniformMessage(array $data)
    {
        $accessToken = $this->getAccessToken();
        if (!$this->access_token && !$accessToken) return false;

        $url = 'cgi-bin/message/wxopen/template/uniform_send?';
        $result = $this->http_post(self::BASE_URL . $url . 'access_token=' . $this->access_token, self::json_encode($data));

        if($result){
            $json = json_decode($result, true);
            if($json['errcode'] == 0){
                return $json;
            }
        }

        return false;
    }

    /**
     * 设置缓存，按需重载
     * @author HQM 2018/11/26
     * @param string $cacheName
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cacheName, $value, $expired)
    {
        return \Yii::$app->cache->set($cacheName, $value, $expired);
    }

    /**
     * 获取缓存，按需重载
     * @author HQM 2018/11/26
     * @param string $cacheName
     * @return mixed
     */
    protected function getCache($cacheName)
    {
        return \Yii::$app->cache->get($cacheName);
    }

    /**
     * 微信api不支持中文转义的json结构
     * @author HQM 2018/11/26
     * @param array $arr
     * @return string
     */
    public static function json_encode($arr)
    {
        if (count($arr) == 0) return "[]";
        $parts = array();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length)) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = self::json_encode($value); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . self::json_encode($value); /* :RECURSION: */
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (!is_string($value) && is_numeric($value) && $value < 2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    /**
     * curl_get
     * @param $url
     * @return bool|mixed
     * @author zhaowenxi
     */
    private function http_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * curl_post
     * @param $url
     * @return bool|mixed
     * @author zhaowenxi
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
}