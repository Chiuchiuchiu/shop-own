<?php

/**
 * Created by Mixiu.
 * Date: 2015/12/2
 * Time: 13:25
 */

namespace components\zsy;

use components\zsy\base\OpenSdkBase;

class ZSYSDK extends OpenSdkBase
{
    public function __construct($appId=null, $keySecret=null, $sourceNo=null)
    {
        if(is_null($appId)) $appId = \Yii::$app->params['zsy']['appId'];
        if(is_null($keySecret)) $keySecret = \Yii::$app->params['zsy']['keySecret'];
        if(is_null($sourceNo)) $sourceNo = \Yii::$app->params['zsy']['sourceNo'];
        $this->protocol = \Yii::$app->params['zsy']['protocol'];
        $this->apiHost = \Yii::$app->params['zsy']['apiHost'];
        $this->merchantNo = \Yii::$app->params['zsy']['merchantNo'];


        parent::__construct($appId, $keySecret, $sourceNo);
    }

    private function array_merge($mainParams = array(), $extParams = array()) {
        $mainParams['accessToken'] = $this->getAccessToken($mainParams);
        return array_merge($mainParams, $extParams);
    }
    /**
     * widget_page 统一请求方法
     * @param array $params
     * @return mixed
     */
    protected function _widgetPage($params = array())
    {

        return json_decode($this->post($this->widgetPageUrl(), $params),true);
    }

    /**
     * 获取帮卡页面URL
     * @param array $mainParams
     * @param array $extParams
     * @return mixed
     */
    public function bindCar($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, $extParams);
        return $this->_widgetPage($params);
    }
    public function deposit($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, $extParams);
        $params['urlKey'] = 'recharge_page';
        return $this->_widgetPage($params);
    }

    public function withdraw($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, $extParams);
        $params['urlKey'] = 'withdraw_page';
        return $this->_widgetPage($params);
    }

    public function transDetail($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, $extParams);
        $params['urlKey'] = 'trans_detail';
        return $this->_widgetPage($params);
    }

    public function myAssets($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, $extParams);
        $params['urlKey'] = 'my_assets';
        return $this->_widgetPage($params);
    }


    /**
     * 获取收银台页面URL
     * @param array $mainParams
     * @param array $extParams
     * @return mixed
     */
    public function cashDesk($mainParams = array(), $extParams = array())
    {

        $params = $this->array_merge($mainParams, $extParams);
        return $this->post($this->cashDeskUrl(), $params);
    }


    /**
     * 创建会员
     * @param array $mainParams
     * @param array $extParams
     * @return mixed
     */
    public function createUser($mainParams = array(), $extParams = array())
    {

        $params = $this->array_merge($mainParams, $extParams);
        return $this->post($this->createUserUrl(), $params);

    }

    /**
     * 更新会员
     * @param array $mainParams
     * @param array $extParams
     * @return mixed
     */
    public function updateUser($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, $extParams);
        return $this->post($this->updateUserUrl(), $params);
    }


    /**
     * 商家二级结算--普通结算
     * @param array $mainParams
     * @param array $extParams
     * @return mixed
     */
    public function childMerchantCommonSettle($mainParams = array(), $extParams = array())
    {

        $params = $this->array_merge($mainParams, $extParams);

        return $this->post($this->childMerchantCommonSettleUrl(), $params);
    }

    /**
     * 商家二级结算--订单结算
     * @param array $mainParams
     * @param array $extParams
     * @return mixed
     */
    public function childMerchantOrderSettle($mainParams = array(), $extParams = array())
    {

        $params = $this->array_merge($mainParams, $extParams);
        return $this->post($this->childMerchantOrderSettleUrl(), $params);
    }
    /**
     * 获得总资产
     * @param integer $outCustomerId
     * @return mixed
     */
    public function generalGet($outCustomerId)
    {
        $mainParams['outCustomerId']=$outCustomerId;
        $mainParams['api_method']='pp.server.IOutwardService.queryAssetAndIncome';
        $params = $this->array_merge($mainParams, []);
        return json_decode($this->post($this->generalGetUrl(), $params),true);
    }

    public function h5Assets($mainParams = array(), $extParams = array())
    {
        $params = $this->array_merge($mainParams, []);
        return $this->post($this->h5AssetsUrl(), $params);

    }
}

