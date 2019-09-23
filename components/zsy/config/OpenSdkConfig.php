<?php

/**
 * Created by Mixiu.
 * Date: 2015/12/2
 * Time: 16:06
 */
class OpenSdkConfig
{

    /**
     * APP应用的APPid
     * @var string
     */
    const APPID = '43289bf111b74bfa818f38331ad2aeab';

    /**
     * APP应用的appSecret
     * @var string
     */
    const KEYSECRET = 'a11fa506cb234d57a03284d1ec65dc5b';

    /**
     * 应用接入来源
     * @var string
     */
    const SOURCENO = '001';

    /**
     * 请求类型
     * http/https
     * @var string
     */

    const PROTOCOL = 'http';

    /**
     * host
     */
    const API_HOST = '223.252.223.155:8183';

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

}