<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/3 15:49
 * Description:
 */

namespace components\wechatSDK;


use components\wechatSDK\lib\WxPayNotify;


class WxPayCallBack{
    public static function Notify($func){
        return new PayNotify($func);
    }
}

class PayNotify extends WxPayNotify
{
    private $func=null;
    public function __construct($func)
    {
        $this->func = $func;
    }

    public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = [];

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        return call_user_func($this->func,$data);
    }
}