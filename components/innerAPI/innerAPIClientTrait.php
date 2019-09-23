<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/8/5 11:56
 * Description:
 */

namespace components\innerAPI;


use common\exception\ConfigException;

trait InnerAPIClientTrait
{
    private $innerAPI;
    private $token;

    private function callAPI($uri, $params = [], $format = 'json')
    {
        if (empty($this->innerAPI) || empty($this->token)) {
            $APIInfo = isset(\Yii::$app->params['innerAPI']['wechat']) ? \Yii::$app->params['innerAPI']['wechat'] : false;
            if (!$APIInfo) {
                throw new ConfigException;
            }
            $this->innerAPI = $APIInfo['url'];
            $this->token = $APIInfo['token'];
            if (empty($this->innerAPI) || empty($this->token)) {
                throw new ConfigException;
            }
        }
        $url = rtrim($this->innerAPI, '/') . '/' . $uri;
        $params['__timestamp'] = time();
        foreach ($params as &$v) {
            $v .= '';//强制转成成字符串进行加密计算
        }
        $params = [
            'data' => $params
        ];
        $params['signature'] = $this->signature($params['data']);
        \Yii::trace("callAPI:" . $url . "\n data:" . http_build_query($params));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 15000);
        $res = curl_exec($ch);
        curl_close($ch);
        \Yii::trace("call API result:" . $res);
        if ($format == 'json')
            return json_decode($res, true);
        return $res;
    }

    private function signature($params)
    {
        ksort($params);
        return sha1(serialize($params) . $this->token);
    }
}