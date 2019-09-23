<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/8/5 11:56
 * Description:
 */

namespace components\innerAPI;


use common\exception\ConfigException;

trait InnerAPIServerTrait
{
    private $token;
    private $data;

    private function checkSignature()
    {
        //接口token配置
        if (empty($this->token)) {
            $APIInfo = isset(\Yii::$app->params['innerAPI']['wechat']) ? \Yii::$app->params['innerAPI']['wechat'] : false;
            if (!$APIInfo) {
                throw new ConfigException;
            }
            $this->token = $APIInfo['token'];
            if (empty($this->token))
                throw new ConfigException;
        }
        $_data = $this->data;
        $signature = $this->post('signature');
        if (empty($_data['__timestamp']) || empty($signature)) {
            return false;
        }
        ksort($_data);
        return time() - $_data['__timestamp'] < 16 && sha1(serialize($_data) . $this->token) === $signature;
    }
}