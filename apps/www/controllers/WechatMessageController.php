<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/9/19
 * Time: 09:26
 */

namespace apps\www\controllers;


use common\models\PmOrder;
use common\models\PmOrderFpzz;
use common\models\WechatInvoiceCard;
use components\wechatSDK\WechatSDK;
use yii\log\FileTarget;

class WechatMessageController extends Controller
{
    protected $missPermission = ['wechat-message/index'];
    public $enableCsrfValidation = false;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $wechatParams = \Yii::$app->params['wechat'];
        $wechatSdk = new WechatSDK($wechatParams);
        $eventArray = $wechatSdk->getRev()->getRevEvent();

        if($eventArray['event'] == 'user_authorize_invoice'){
            $wechatSdk->text()->reply();
            $xmlData = $wechatSdk->getRev()->getRevData();

            //记录微信数据
            $this->log($xmlData);

            if(isset($xmlData['SuccOrderId'])){
                $findOrSave = WechatInvoiceCard::findNumber($xmlData['SuccOrderId']);
                if($findOrSave){
                    $this->WechatInvoiceCard($xmlData['SuccOrderId']);
                }
            }

            die;
        }
    }

    /**
     * 将电子发票加入用户微信卡包
     * @param $orderNumber
     * @author HQM 2018-09-20
     */
    private function WechatInvoiceCard($orderNumber)
    {
        $wechatParam = \Yii::$app->params['wechat'];
        $wechatSDK = new WechatSDK($wechatParam);

        $pmOrderInfo = PmOrder::findOne(['number' => $orderNumber]);
        $pmOrderInvoicePdf = PmOrderFpzz::findOne(['pm_order_id' => $pmOrderInfo->id]);

        //税额
        $taxAmount = $pmOrderInvoicePdf->pmOrderNewwindowPdf->tax_amount;
        $taxAmount = $taxAmount * 100;
        //不含税金额
        $notTaxAmount = $pmOrderInvoicePdf->pmOrderNewwindowPdf->not_tax_amount;
        $notTaxAmount = $notTaxAmount * 100;
        //校验码
        $fpjym = $pmOrderInvoicePdf->pmOrderNewwindowPdf->fpjym;

        if($taxAmount < 1){
            return false;
        }
        if($notTaxAmount < 1){
            return false;
        }
        if(empty($fpjym)){
            return false;
        }

        $cardData = [
            'invoice_info' => [
                'base_info' => [
                    'logo_url' => 'https://mmbiz.qpic.cn/mmbiz_png/KnvF24vtSiaNTbMH0I2oGaMLToL7l6FCia4SaWkPWaXOUArSH2CqeD4Qxnia0wVdE6znb2Qtx3qD9AOHmhQx0kYhg/0?wx_fmt=png',
                    'title' => '广东中奥物业',
                ],
                'payee' => '广东中奥物业管理有限公司广州分公司',
                'type' => '增值税电子普通发票',
            ]
        ];
        $cardInfo = $wechatSDK->invoiceCreateCard($cardData);
        if($cardInfo){
            //PDF 存放路径
            $pdfPath = $pmOrderInvoicePdf->pmOrderNewwindowPdf->save_path;
            $path = str_replace('@cdnUrl', '@root/attached/public', $pdfPath);
            $pdfFile = \Yii::getAlias($path);
            if(file_exists($pdfFile)){
                $fildData = [
                    'pdf' => new \CURLFile($pdfFile),
                ];
                $setPdf = $wechatSDK->invoiceSetPdf($fildData);
                if($setPdf){
                    $fee = $pmOrderInvoicePdf->total_amount * 100;
                    $title = $pmOrderInvoicePdf->user_name;
                    $billingTime = $pmOrderInvoicePdf->pmOrderNewwindowPdf->created_at;
                    $billingNo = $pmOrderInvoicePdf->pmOrderNewwindowPdf->bill_code;
                    $billingCode = $pmOrderInvoicePdf->pmOrderNewwindowPdf->bill_num;
                    $insertData = [
                        'order_id' => $orderNumber,
                        'card_id' => $cardInfo['card_id'],
                        'appid' => $wechatParam['appId'],
                        'card_ext' => [
                            'nonce_str' => $wechatSDK->generateNonceStr(),
                            'user_card' => [
                                'invoice_user_data' => [
                                    'fee' => $fee,
                                    'title' => $title,
                                    'billing_time' => $billingTime,
                                    'billing_no' => $billingNo,
                                    'billing_code' => $billingCode,
                                    'fee_without_tax' => $notTaxAmount,
                                    'tax' => $taxAmount,
                                    's_pdf_media_id' => $setPdf['s_media_id'],
                                    'check_code' => $fpjym,
                                ]
                            ]
                        ]
                    ];

                    $insert = $wechatSDK->invoiceInsert($insertData);
                    $status = 3;
                    if($insert){
                        $status = 2;
                    }

                    WechatInvoiceCard::updateAll(['pm_order_number' => $orderNumber], ['card_id' => $cardInfo['card_id'], 'status' => $status]);
                }
            }

        }
    }

    /**
     * 记录请求参数
     * @param $msgLog
     * @throws \yii\base\InvalidConfigException
     */
    private function log($msgLog)
    {
        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/wechat.log';
        $fileLog->messages[] =  [$msgLog, 8, 'application', microtime(true)];;
        $fileLog->export();
    }

}