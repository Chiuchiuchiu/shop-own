<?php
/**
 * https://open.swiftpass.cn
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 *
 * ================================================================
 */

namespace components\swiftpass;

use components\swiftpass\lib\RequestHandler;
use components\swiftpass\lib\ClientResponseHandler;
use components\swiftpass\lib\PayHttpClient;
use components\swiftpass\lib\Utils;

class SwiftPassOrigin
{
    /**
     * @var ClientResponseHandler $resHandler
     */
    public $resHandler = null;
    /**
     * @var RequestHandler $reqHandler
     */
    public $reqHandler = null;

    /**
     * @var PayHttpClient $pay
     */
    public $pay = null;
    public $cfg = null;

    public function __construct($config)
    {
        $this->Request($config);
    }

    public function Request($config)
    {
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = $config;

        $this->reqHandler->setGateUrl($this->cfg['url']);
        $this->reqHandler->setKey($this->cfg['key']);
    }

    public function index()
    {
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : 'submitOrderInfo';
        switch ($method) {
            case 'submitOrderInfo'://提交订单
                $this->submitOrderInfo();
                break;
            case 'queryOrder'://查询订单
                $this->queryOrder();
                break;
            case 'submitRefund'://提交退款
                $this->submitRefund();
                break;
            case 'queryRefund'://查询退款
                $this->queryRefund();
                break;
            case 'callback':
                $this->callback();
                break;
        }
    }

    /**
     * 提交订单信息
     * @param $subOpenId
     * @param $outTradeNo
     * @param $money
     * @param $notifyUrl
     * @param array $option
     * @return array|bool
     */
    public function submitOrderInfo($subOpenId, $outTradeNo, $money, $notifyUrl, $option = [])
    {
        $this->reqHandler->setSubOpenId($subOpenId);
        $this->reqHandler->setParameter('sub_appid', $this->cfg['sub_appid']);
        $this->reqHandler->setParameter('service', 'pay.weixin.jspay');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('version', $this->cfg['version']);
        $this->reqHandler->setParameter('is_raw', '1');
        $this->reqHandler->setParameter('out_trade_no', $outTradeNo);
        $this->reqHandler->setParameter('mch_create_ip', \Yii::$app->request->userIP);
        $this->reqHandler->setParameter('total_fee', $money * 100);
        //通知地址，必填项，接收平台通知的URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        $this->reqHandler->setParameter('notify_url', $notifyUrl);
        $this->reqHandler->setParameter('nonce_str', self::getNonceStr());//随机字符串，必填项，不长于 32 位

        $this->reqHandler->setBody(isset($option['body']) ? $option['body'] : '缴费');
        $this->reqHandler->setAttach(isset($option['attach']) ? $option['attach'] : '缴费');

        if (isset($option['limit_credit_pay'])) {
            $this->reqHandler->setParameter('limit_credit_pay', $option['limit_credit_pay']);
        }

        $this->reqHandler->createSign();//创建签名

        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {

                    $result = json_decode($this->resHandler->getParameter('pay_info'), true);

                    return $result;
                }
            }

        }

        return false;
    }


    /**
     * 查询订单
     */
    public function queryOrder()
    {
        $this->reqHandler->setReqParams($_POST, array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if (empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])) {
            echo json_encode(array('status' => 500,
                'msg' => '请输入商户订单号,平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version', $this->cfg['version']);
        $this->reqHandler->setParameter('service', 'unified.trade.query');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                $res = $this->resHandler->getAllParameters();
                Utils::dataRecodes('查询订单', $res);
                //支付成功会输出更多参数，详情请查看文档中的7.1.4返回结果
                echo json_encode(array('status' => 200, 'msg' => '查询订单成功，请查看result.txt文件！', 'data' => $res));
                exit();
            }
            echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')));
        } else {
            echo json_encode(array('status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()));
        }
    }


    /**
     * 提交退款
     */
    public function submitRefund()
    {
        $this->reqHandler->setReqParams($_POST, array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if (empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])) {
            echo json_encode(array('status' => 500,
                'msg' => '请输入商户订单号或平台订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version', $this->cfg['version']);
        $this->reqHandler->setParameter('service', 'unified.trade.refund');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('op_user_id', $this->cfg['mchId']);//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式
        var_dump($data);
        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                 'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                 'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                                 'refund_id'=>$this->resHandler->getParameter('refund_id'),
                                 'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                                 'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                                 'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee'));*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('提交退款', $res);
                    echo json_encode(array('status' => 200, 'msg' => '退款成功,请查看result.txt文件！', 'data' => $res));
                    exit();
                } else {
                    echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('err_code') . ' Error Message:' . $this->resHandler->getParameter('err_msg')));
                    exit();
                }
            }
            echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')));
        } else {
            echo json_encode(array('status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()));
        }
    }

    /**
     * 查询退款
     */
    public function queryRefund()
    {
        $this->reqHandler->setReqParams($_POST, array('method'));
        if (count($this->reqHandler->getAllParameters()) === 0) {
            echo json_encode(array('status' => 500,
                'msg' => '请输入商户订单号,平台订单号,商户退款单号,平台退款单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version', $this->cfg['version']);
        $this->reqHandler->setParameter('service', 'unified.trade.refundquery');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->cfg['mchId']);//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);//设置请求地址与请求参数
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                  'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                  'refund_count'=>$this->resHandler->getParameter('refund_count'));
                    for($i=0; $i<$res['refund_count']; $i++){
                        $res['out_refund_no_'.$i] = $this->resHandler->getParameter('out_refund_no_'.$i);
                        $res['refund_id_'.$i] = $this->resHandler->getParameter('refund_id_'.$i);
                        $res['refund_channel_'.$i] = $this->resHandler->getParameter('refund_channel_'.$i);
                        $res['refund_fee_'.$i] = $this->resHandler->getParameter('refund_fee_'.$i);
                        $res['coupon_refund_fee_'.$i] = $this->resHandler->getParameter('coupon_refund_fee_'.$i);
                        $res['refund_status_'.$i] = $this->resHandler->getParameter('refund_status_'.$i);
                    }*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('查询退款', $res);
                    echo json_encode(array('status' => 200, 'msg' => '查询成功,请查看result.txt文件！', 'data' => $res));
                    exit();
                } else {
                    echo json_encode(array('status' => 500, 'msg' => 'Error Code:' . $this->resHandler->getParameter('message')));
                    exit();
                }
            }
            echo json_encode(array('status' => 500, 'msg' => $this->resHandler->getContent()));
        } else {
            echo json_encode(array('status' => 500, 'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()));
        }
    }

    /**
     * 后台异步回调通知
     */
    public function callback()
    {
        $xml = file_get_contents('php://input');
        $this->resHandler->setContent($xml);
        $this->resHandler->setKey($this->cfg['key']);
        if ($this->resHandler->isTenpaySign()) {
            if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                $tradeno = $this->resHandler->getParameter('out_trade_no');
                // 此处可以在添加相关处理业务，校验通知参数中的商户订单号out_trade_no和金额total_fee是否和商户业务系统的单号和金额是否一致，一致后方可更新数据库表中的记录。
                //更改订单状态

                Utils::dataRecodes('接口回调收到通知参数', $this->resHandler->getAllParameters());
                ob_clean();
                echo 'success';
                file_put_contents('2.txt', 1);
                exit();
            } else {
                echo 'failure1';
                exit();
            }
        } else {
            echo 'failure2';
        }
    }

    public function callbackFunc($func)
    {

    }


    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}