<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/9/4
 * Time: 15:49
 */
namespace components\weChat;

use components\wechatSDK\WechatSDK;

class WechatTemplate
{
    /**
     * 电子发票模板消息
     * @param string $memberOpenId
     * @param string $projectKeyUrl
     * @param string $statusText
     * @param string $amount
     * @return mixed
     */
    public function electronicInvoice($memberOpenId, $projectKeyUrl, $statusText, $amount)
    {
        $url = 'http://'.$projectKeyUrl.'.'.\Yii::$app->params['domain.p'];
        $url .= '/tcis/lists?';

        $postData = [
            'touser' => $memberOpenId,
            'template_id' => 'RhrZ9NcEOOisF_vnyw7fq5FE4uljcl_k5Hdcj68x0kU',
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => '您好，您的电子发票申请状态进度',
                    'color' => '#173177',
                ],
                'keyword1' => [
                    'value' => date('Y-m-d H:i:s'),
                ],
                'keyword2' => [
                    'value' => $statusText,
                ],
                'keyword3' => [
                    'value' => '-',
                ],
                'keyword4' => [
                    'value' => $amount,
                ],
                'keyword5' => [
                    'value' => $amount,
                ],
                'remark' => [
                    'value' => '如有疑问，请联系项目管家',
                ],
            ]
        ];
        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);
        $res = $wx_obj->sendTemplateMessage($postData);
        return $res;
    }
}