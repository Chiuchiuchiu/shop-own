<?php
/**
 * Created by PhpStorm.
 * User: feng
 * Date: 2019/8/14
 * Time: 10:42
 */
namespace apps\www\module\viewShop;

class shopApi
{
    const HOST = "https://shop.51homemoney.com/Api/";
    const shopByGroup = "ShopApi/getshopByGroup";
    const shopList = "ShopApi/getshop";
    const reGoodsList = "ShopApi/recommedGoods";

    public function __construct()
    {

    }
    public function getShopList($pid){

        return json_decode(self::curlRequest(self::HOST . self::shopList."?pid=".$pid), true);
    }

    /**
     * @author dtfeng
     * @Date: 2019/9/6
     * @Time: 19:13
     * @description 推荐商品
     * @param $pid
     * @return mixed
     */
    public function getReGoodsList($pid,$kw){
        return json_decode(self::curlRequest(self::HOST . self::reGoodsList."?pid=".$pid ."&kw=" .$kw), true);
    }

    /**
     * @author dtfeng
     * @Date: 2019/8/14
     * @Time: 11:28
     * @description 获取店铺列表
     * @param $pid
     * @return mixed
     */
    public function getShopListByGroup($pid){

        return json_decode(self::curlRequest(self::HOST . self::shopByGroup."?pid=".$pid), true);
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