<?php
/**
 * Created by PhpStorm.
 * User: feng
 * Date: 2019/9/3
 * Time: 14:10
 */

namespace apps\admin\module;

class shopApi
{
    const HOST = "https://shop.51homemoney.com/Api/";
    // const HOST = "http://cdjshop.com/Api/";
    const AdList = "AdApi/getAdTemplate";
    const Ad = "AdApi/getAdTemplateById";

    public function __construct()
    {

    }

    /**
     * @author dtfeng
     * @Date: 2019/9/3
     * @Time: 11:28
     * @description 获取模块列表
     * @return mixed
     */
    public function getAdList(){

        return json_decode(self::curlRequest(self::HOST . self::AdList), true);
    }

    public function getAdById($id){

        return json_decode(self::curlRequest(self::HOST . self::Ad ."?id=" .$id), true);
    }


    // https请求

    /**
     * @author dtfeng
     * @Date: 2019/8/14
     * @Time: 11:27
     * @description GET请求
     * @param $url
     * @param null $data
     * @return mixed
     */
    public static function curlRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * 发送post请求
     * @param string $url 链接
     * @param string $data 数据
     * @return bool|mixed
     */
    public static function curlPost($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}