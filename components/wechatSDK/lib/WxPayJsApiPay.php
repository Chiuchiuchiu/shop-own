<?php
namespace components\wechatSDK\lib;
/**
 * 
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class WxPayJsApiPay
{
    public function GetJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误".serialize($UnifiedOrderResult).'|prepay_id='.$UnifiedOrderResult['prepay_id']);
		}
		$jsapi = new WxPayJsApiPayData();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();

		//小程序APPID
		if(isset($UnifiedOrderResult['sub_appid'])){
		    $jsapi->SetAppid($UnifiedOrderResult['sub_appid']);
        }

		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		return $jsapi->GetValues();
	}

	/**
	 * 
	 * 拼接签名字符串
	 * @param array $urlObj
	 * 
	 * @return 返回已经拼接好的字符串
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
//
//	/**
//	 *
//	 * 获取地址js参数
//	 *
//	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
//	 */
//	public function GetEditAddressParameters()
//	{
//		$getData = $this->data;
//		$data = array();
//		$data["appid"] = WxPayConfig::APPID;
//		$data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//		$time = time();
//		$data["timestamp"] = "$time";
//		$data["noncestr"] = "1234568";
//		$data["accesstoken"] = $getData["access_token"];
//		ksort($data);
//		$params = $this->ToUrlParams($data);
//		$addrSign = sha1($params);
//
//		$afterData = array(
//			"addrSign" => $addrSign,
//			"signType" => "sha1",
//			"scope" => "jsapi_address",
//			"appId" => WxPayConfig::APPID,
//			"timeStamp" => $data["timestamp"],
//			"nonceStr" => $data["noncestr"]
//		);
//		$parameters = json_encode($afterData);
//		return $parameters;
//	}
	
//	/**
//	 *
//	 * 构造获取code的url连接
//	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
//	 *
//	 * @return 返回构造好的url
//	 */
//	private function __CreateOauthUrlForCode($redirectUrl)
//	{
//		$urlObj["appid"] = WxPayConfig::APPID;
//		$urlObj["redirect_uri"] = "$redirectUrl";
//		$urlObj["response_type"] = "code";
//		$urlObj["scope"] = "snsapi_base";
//		$urlObj["state"] = "STATE"."#wechat_redirect";
//		$bizString = $this->ToUrlParams($urlObj);
//		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
//	}
//
//	/**
//	 *
//	 * 构造获取open和access_toke的url地址
//	 * @param string $code，微信跳转带回的code
//	 *
//	 * @return 请求的url
//	 */
//	private function __CreateOauthUrlForOpenid($code)
//	{
//		$urlObj["appid"] = WxPayConfig::APPID;
//		$urlObj["secret"] = WxPayConfig::APPSECRET;
//		$urlObj["code"] = $code;
//		$urlObj["grant_type"] = "authorization_code";
//		$bizString = $this->ToUrlParams($urlObj);
//		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
//	}
}