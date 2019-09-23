<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/8/10 17:30
 * Description:
 */

namespace components\innerAPI;


use common\exception\ConfigException;

trait CmdAPIClientTrait
{
    private $apiUrl;
    private $apiPort;
    private $encryptKey;
    private $signatureKey;

    private function cmdAPI($cmd)
    {
        if (empty($this->apiUrl) || empty($this->encryptKey) || empty($this->signatureKey)) {
            $APIInfo = \Yii::$app->params['innerAPI']['cmd'];
            if (!$APIInfo) {
                throw new ConfigException;
            }
            $this->apiUrl = $APIInfo['url'];
            $this->apiPort = isset($APIInfo['port'])?$APIInfo['port']:6868;
            $this->encryptKey = $APIInfo['encryptKey'];
            $this->signatureKey = $APIInfo['signatureKey'];
            if (empty($this->apiUrl) || empty($this->encryptKey) || empty($this->signatureKey)) {
                throw new ConfigException;
            }
        }
        $data = [
            'cmd' => $cmd,
            'timestamp' => time(),
            'r' => sha1(rand(0, 99999999) . microtime())//加入一个随机数,加大暴力破解难度
        ];
        $data = json_encode($data);
        $signature = md5($data . $this->signatureKey);

        $params = [
            'c' => $this->encode($data),
            'signature' => $signature
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PORT, $this->apiPort);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res=='ok';
    }

    function encode($data)
    {
        return $data;
        $data = base64_encode($data);
        $len = strlen($data);
        $key_len = strlen($this->encryptKey);
        $res = [];
        $tmp = 5;
        for ($i = 0; $i < $len; $i++) {
            $t = substr($data, $i, 1);
            $res[] = $tmp = ord($t) ^ ord(substr($this->encryptKey, $i % $key_len, 1)) ^ $tmp;

        }
        return base64_encode(implode(',', $res));
    }
}