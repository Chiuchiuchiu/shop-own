<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/18
 * Time: 10:58
 */

namespace console\controllers;


use apps\mgt\models\FpzzLog;
use common\models\PmOrder;
use common\models\PmOrderFpzz;
use common\models\PmOrderItem;
use common\models\PmOrderNewwindowPdf;
use components\email\Email;
use components\helper\File;
use components\newWindow\NewWindow;
use components\Tcis\Tcis;
use components\weChat\WechatTemplate;
use yii\base\ErrorException;
use yii\console\Controller;
use yii\log\FileTarget;

class TcisController extends Controller
{
    /**
     * 订单明细请求新视窗是否可以开具发票 param:$pmOrderId
     * @author HQM 2018/11/23
     * @param $pmOrderId
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNewwindowQueryInv($pmOrderId)
    {
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId]);
        $chargeDetailIdList = [];

        if($pmOrder){
            foreach ($pmOrder->items as $row){
                /**
                 * @var PmOrderItem $row
                 */
                if(!empty($row->charge_detail_id_list)){
                    $chargeDetailIdList[] = $row->charge_detail_id_list;
                } else {
                    $unserizleData = unserialize($row->bill_content);
                    if(isset($unserizleData['ChargeDetailIDList'])){
                        $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                    }
                }
            }


        }

        if(count($chargeDetailIdList) > 0){
            $chargeDetailIdList = implode(',', $chargeDetailIdList);
            $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList, 0);

            var_dump($newWindows);die;
        }

        $this->stdout("Done \n");
        exit(1);
    }

    /**
     * @throws ErrorException
     */
    public function actionActsign()
    {
        $appid = \Yii::$app->params['tcis']['appid'];
        $prikey = \Yii::$app->params['tcis']['prikey'];
        $res = (new Tcis())->getActsign($appid, $prikey);

        var_dump($res);
    }


    /**
     * 异步请求新视窗发票查询接口，3 ~ 5秒/次 pmOrderId[null],status[4],requestNum[4]
     * @author HQM 2019/01/02
     * @param null $pmOrderId
     * @param int $status
     * @param int $requestNum
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionAsyncNewwindowGet($pmOrderId=null, $status=4, $requestNum=7)
    {
        $pmOrderFpzzModel = PmOrderFpzz::find()
            ->where(['status' => $status, 'type' => PmOrderFpzz::TYPE_E])
            ->andWhere(['<', 'request_number', $requestNum])
            ->andFilterWhere(['pm_order_id' => $pmOrderId])
            ->orderBy('id ASC');

        foreach($pmOrderFpzzModel->each(100) as $oRow){
            /**
             * @var PmOrderFpzz $oRow
             */
            $requestNumber = $oRow->request_number + 1;
            $pmOrder = PmOrder::findOne(['id' => $oRow->pm_order_id, 'status' => PmOrder::STATUS_PAYED]);
            if(isset($pmOrder->items)){
                $chargeDetailIdList = [];
                $memberOpenId = $pmOrder->member->wechat_open_id;
                $projectKeyUrl = $pmOrder->project->url_key;
                $pmOrderTotalAmount = $pmOrder->total_amount;
                $statusText = '开票成功';

                foreach ($pmOrder->items as $row){
                    /**
                     * @var PmOrderItem $row
                     */
                    if(!empty($row->charge_detail_id_list)){
                        $chargeDetailIdList[] = $row->charge_detail_id_list;
                    } else {
                        $unserizleData = unserialize($row->bill_content);
                        if(isset($unserizleData['ChargeDetailIDList'])){
                            $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                        }
                    }
                }

                if(!empty($chargeDetailIdList)){
                    $chargeDetailIdList = implode(',', $chargeDetailIdList);
                    $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList, 0);

                    if($newWindows['Response']['Data']['NWRespCode'] != '0000'){

                        PmOrderFpzz::updateAll(['request_number' => $requestNumber], ['id' => $oRow->id]);
//                        //update pm_order_fpzz status fail
//                        if($requestNumber > 3){
//                            PmOrderFpzz::updateAll(['status' => PmOrderFpzz::STATUS_WAIT_REVIEW, 'request_number' => $requestNumber], ['id' => $oRow->id]);
//                        } else {
//                            PmOrderFpzz::updateAll(['request_number' => $requestNumber], ['id' => $oRow->id]);
//                        }

                        $statusText = '开票失败：局端异常。待技术核实原因';
                        $subS = '定时获取新视窗电子发票，返回错误：' . $newWindows['Response']['Data']['NWRespCode'];
                        $html = '订单ID：' . $pmOrder->id;

                        $this->writeFpzzLog($pmOrder->id, $chargeDetailIdList, $newWindows);

                        (new Email())->sendToAdmin($subS, '315780351@qq.com', $html);
                    }

                    $record = $newWindows['Response']['Data']['Record'];
                    $insertData = [];
                    $createdTime = time();
                    if(!empty($record)){
                        foreach($record as $recordRow){
                            //save pdf file
                            if(empty($recordRow['BillPDFUrl'])){
                                $record = [];
                                break;
                            }

                            $pdfSavePath = File::savePdf($recordRow['BillPDFUrl']);
                            //税额
                            $taxAmount = isset($recordRow['TaxAmount']) ? $recordRow['TaxAmount'] : 0;
                            //不含税金额
                            $notTaxAmount = isset($recordRow['NotTaxAmount']) ? $recordRow['NotTaxAmount'] : 0;
                            //发票校验码
                            $fpjym = isset($recordRow['FpJym']) ? $recordRow['FpJym'] : '';
                            $insertData[] = [
                                $oRow->id, $taxAmount, $notTaxAmount, $fpjym, $recordRow['BillNum'], $recordRow['BillCode'], $recordRow['BillPDFUrl'], $pdfSavePath, $createdTime
                            ];
                        }
                    }

                    if(!empty($insertData)){
                        PmOrderNewwindowPdf::getDb()->createCommand()->batchInsert(PmOrderNewwindowPdf::tableName(), [
                            'pm_order_fpzz_id',
                            'tax_amount',
                            'not_tax_amount',
                            'fpjym',
                            'bill_num',
                            'bill_code',
                            'bill_pdf_url',
                            'save_path',
                            'created_at'
                        ], $insertData)->execute();

                        $insertData = null;

                        PmOrderFpzz::updateAll(['status' => PmOrderFpzz::STATUS_SUCCESS], ['id' => $oRow->id]);

                        //微信模板
                        (new WechatTemplate())->electronicInvoice($memberOpenId, $projectKeyUrl, $statusText, $pmOrderTotalAmount);
                    }

                    if(!empty($record)){
                        foreach($record as $recordRow){
                            //send email to member
                            if(empty($recordRow['BillPDFUrl'])){
                                break;
                            }
                            $this->sendEmailToMember($oRow->email, $recordRow['BillPDFUrl'], $recordRow['BillCode'], $recordRow['BillNum'], $oRow->user_name);
                        }
                    } else {
                        if($requestNumber > 7){
                            PmOrderFpzz::updateAll(['status' => PmOrderFpzz::STATUS_WAIT_REVIEW, 'request_number' => $requestNumber], ['id' => $oRow->id]);

                            //微信模板
                            $statusText = '获取电子发票PDF文件失败。待技术核实原因';
                            (new WechatTemplate())->electronicInvoice($memberOpenId, $projectKeyUrl, $statusText, $pmOrderTotalAmount);
                        } else {
                            PmOrderFpzz::updateAll(['request_number' => $requestNumber], ['id' => $oRow->id]);
                        }
                    }

                    //写入文件日志
                    $this->queryInvoiceLog($newWindows, $oRow->pm_order_id);
                }
            }


            sleep(1);
        }

        $this->stdout('Done' . PHP_EOL);

        exit(0);
    }


    /**
     * 异步请求新视窗发票查询接口
     * @author feng 2019/07/11
     * @param null $pmOrderId 订单ID
     * @param int $status (0:开票失败; 1:拒绝; 2:开票中；3：已发送邮箱；4：提交成功；5：前台补录；10：纸质发票已开)
     * @param int $requestNum 总查询次数
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionAsyncNewWinForInvoices($pmOrderId=null, $status=4, $requestNum=50)
    {
        $time1 = time() - 1 * 60;
        $time2 = time() - 6 * 60;
        $time3 = time() - 12 * 60;
        $num1 = 3;
        $num2 = 13;
        $max = $requestNum;
        $pmOrderFpzzModel = PmOrderFpzz::find()
            ->where(['status' => $status, 'type' => PmOrderFpzz::TYPE_E])
            ->andWhere(['<', 'request_number', $max])
            ->andWhere(['and', ['or', 'updated_at<='.$time1 .' and request_number <= ' .$num1, 'updated_at<='.$time2 .' and request_number <= '.$num2, 'updated_at<='.$time3 .' and request_number < '.$max]])
            ->andFilterWhere(['pm_order_id' => $pmOrderId])
            ->orderBy('request_number ASC,id ASC');
        $batchSize = 50;
        $list = $pmOrderFpzzModel->each($batchSize);

        foreach($list as $oRow){
            $requestNumber = $oRow->request_number + 1;
            $pmOrder = PmOrder::findOne(['id' => $oRow->pm_order_id, 'status' => PmOrder::STATUS_PAYED]);
            if(isset($pmOrder->items)){
                $chargeDetailIdList = [];
                $memberOpenId = $pmOrder->member->wechat_open_id;
                $memberEmail = $oRow->email;
                $projectKeyUrl = $pmOrder->project->url_key;
                $pmOrderTotalAmount = $pmOrder->total_amount;

                foreach ($pmOrder->items as $row){
                    if(!empty($row->charge_detail_id_list)){
                        $chargeDetailIdList[] = $row->charge_detail_id_list;
                    } else {
                        $unserizleData = unserialize($row->bill_content);
                        if(isset($unserizleData['ChargeDetailIDList'])){
                            $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                        }
                    }
                }

                if(!empty($chargeDetailIdList)){
                    $chargeDetailIdList = implode(',', $chargeDetailIdList);
                    $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList, 0);

                    if($newWindows['Response']['Data']['NWRespCode'] == '0000'){
                        // 新视窗返回正常
                        $record = $newWindows['Response']['Data']['Record'];
                        $insertData = null;
                        $createdTime = time();
                        $updateTime = time();
                        $_recordRow = null;
                        $paperInvoice = false;
                        if(empty($record)){
                            continue;
                        }

                        foreach($record as $recordRow){
                            $_recordRow = $recordRow;
                            $_BillNum = $_recordRow['BillNum'];
                            $_BillCode = $_recordRow['BillCode'];
                            if(empty($_BillNum)){
                                break;
                            }

                            if(empty($_BillCode) && strlen($_BillNum)>8){
                                $paperInvoice = true;
                                break;
                            }

                            if(empty($_recordRow['BillPDFUrl'])){
                                break;
                            }

                            $pdfSavePath = File::savePdf($_recordRow['BillPDFUrl']);
                            //税额
                            $taxAmount = isset($_recordRow['TaxAmount']) ? $_recordRow['TaxAmount'] : 0;
                            //不含税金额
                            $notTaxAmount = isset($_recordRow['NotTaxAmount']) ? $_recordRow['NotTaxAmount'] : 0;
                            //发票校验码
                            $fpjym = isset($_recordRow['FpJym']) ? $_recordRow['FpJym'] : '';
                            $insertData[] = [
                                $oRow->id,
                                $taxAmount,
                                $notTaxAmount,
                                $fpjym,
                                $_recordRow['BillNum'],
                                $_recordRow['BillCode'],
                                $_recordRow['BillPDFUrl'],
                                $pdfSavePath,
                                $createdTime
                            ];
                        }

                        // 订阅号消息通知
                        if(!empty($memberOpenId) && !empty($insertData)){
                            PmOrderNewwindowPdf::getDb()->createCommand()->batchInsert(PmOrderNewwindowPdf::tableName(), [
                                'pm_order_fpzz_id',
                                'tax_amount',
                                'not_tax_amount',
                                'fpjym',
                                'bill_num',
                                'bill_code',
                                'bill_pdf_url',
                                'save_path',
                                'created_at'
                            ], $insertData)->execute();

                            $statusText = '开票成功';
                            $ser = new WechatTemplate();
                            $ser->electronicInvoice($memberOpenId, $projectKeyUrl, $statusText, $pmOrderTotalAmount);
                        }

                        // 邮件通知
                        if(!empty($memberEmail) && !empty($insertData)){
                            $this->sendEmailToMember($memberEmail, $_recordRow['BillPDFUrl'], $_recordRow['BillCode'], $_recordRow['BillNum'], $oRow->user_name);
                        }

                        $data = Array(
                            'request_number' => $requestNumber,
                            'updated_at'=> $updateTime
                        );
                        $where = Array(
                            'id' => $oRow->id
                        );
                        if(!empty($insertData)){
                            // 电子开票成功
                            $data['status'] = PmOrderFpzz::STATUS_SUCCESS;
                            PmOrderFpzz::updateAll($data, $where);
                        }else if($paperInvoice == true){
                            // 前台补录
                            $data['status'] = PmOrderFpzz::STATUS_PM;
                            PmOrderFpzz::updateAll($data, $where);
                        }else if($requestNumber == $max ){
                            // 已经达到最大访问新视窗次数，记为失败
                            $data['status'] = PmOrderFpzz::STATUS_WAIT_REVIEW;
                            PmOrderFpzz::updateAll($data, $where);
                        }
                        else{
                            // 当前还没成功
                            PmOrderFpzz::updateAll($data, $where);
//                            if($requestNumber > 7){
//                                // 开票失败
//                                // Debug TODO
//                                $data['status'] = PmOrderFpzz::STATUS_WAIT_REVIEW;
//                                PmOrderFpzz::updateAll($data, $where);
//                                $statusText = '获取电子发票PDF文件失败。待技术核实原因';
//                                $ser = new WechatTemplate();
//                                $ser->electronicInvoice($memberOpenId, $projectKeyUrl, $statusText, $pmOrderTotalAmount);
//                            }else{
//                                // 当前还没成功
//                                PmOrderFpzz::updateAll($data, $where);
//                            }
                        }
                        //写入文件日志
                        $this->queryInvoiceLog($newWindows, $oRow->pm_order_id);
                    }else{
                        // 4000 新视窗返回异常
                        $data = Array(
                            'request_number' => $requestNumber,
                            'updated_at'=> time()
                        );
                        $where = Array(
                            'id' => $oRow->id
                        );
                        PmOrderFpzz::updateAll($data, $where);
                        $subS = '定时获取新视窗电子发票，返回错误：' . $newWindows['Response']['Data']['NWRespCode'];
                        $html = '订单ID：' . $pmOrder->id;
                        $this->writeFpzzLog($pmOrder->id, $chargeDetailIdList, $newWindows);
                        $ser = new Email();
                        $ser->sendToAdmin($subS, '315780351@qq.com', $html);
                        $ser->sendToAdmin($subS, '398746422@qq.com', $html);
                    }
                }
            }
            sleep(1);
        }

        $this->stdout('Done' . PHP_EOL);

        exit(0);
    }



    /**
     * 请求新视窗开电子发票 pmOrderId
     * @author HQM 2018/11/23
     * @param $pmOrderId
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNewwindowOpen($pmOrderId)
    {
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId, 'status' => PmOrder::STATUS_PAYED]);
        if(isset($pmOrder->items)){
            $chargeDetailIdList = [];
            foreach ($pmOrder->items as $row){
                /**
                 * @var PmOrderItem $row
                 */
                if(!empty($row->charge_detail_id_list)){
                    $chargeDetailIdList[] = $row->charge_detail_id_list;
                } else {
                    $unserizleData = unserialize($row->bill_content);
                    if(isset($unserizleData['ChargeDetailIDList'])){
                        $chargeDetailIdList[] = $unserizleData['ChargeDetailIDList'];
                    }
                }
            }

            if(!empty($chargeDetailIdList)){
                $chargeDetailIdList = implode(',', $chargeDetailIdList);
                $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList, 1);

                var_dump($newWindows);

                $this->writeFpzzLog($pmOrderId, $chargeDetailIdList, $newWindows);

                if($newWindows['Response']['Data']['NWRespCode'] != '0000'){

                    $setSub = '请求新视窗开具发票失败，返回错误码：' . $newWindows['Response']['Data']['NWRespCode'];
                    $NWErrMsg = $newWindows['Response']['Data']['NWErrMsg'];
                    $NWErrMsg .= '--------订单ID：' . $pmOrder->id;

                    //发送发票模板消息通知用户
                    (new Email())->sendToAdmin($setSub, '315780351@qq.com', $NWErrMsg);
                    exit(0);
                }
            }
        }
    }

    /**
     * 将 PDF 重新发送邮箱：id
     * @author HQM 2018/09/03
     * @param $id
     */
    public function actionResetEmail($id)
    {
        $pmOrderFpzzModel = PmOrderFpzz::findOne(['status' => PmOrderFpzz::STATUS_SUCCESS, 'id' => $id]);
        $pdfModel = PmOrderNewwindowPdf::findOne(['pm_order_fpzz_id' => $pmOrderFpzzModel->id]);

        $res = $this->sendEmailToMember($pmOrderFpzzModel->email, $pdfModel->bill_pdf_url, $pdfModel->bill_code, $pdfModel->bill_num, $pmOrderFpzzModel->user_name);

        var_dump($res);
    }

    /**
     * @param integer $pmOrderFpzzId
     * @param string $postData
     * @param string $result
     */
    protected function writeFpzzLog($pmOrderFpzzId, $postData, $result)
    {
        $fpzzLog = new FpzzLog();
        $fpzzLog->pm_order_id = $pmOrderFpzzId;
        $fpzzLog->post_data = serialize($postData);
        $fpzzLog->result = serialize($result);
        $fpzzLog->fp_cached_id = isset($result['object']['id']) ? $result['object']['id'] : '';

        $this->stdout($fpzzLog->save() . "\n");
    }

    /**
     * @param int $kprq
     * @param int $fpdm
     * @param int $fphm
     * @param string $gfmc
     * @param string $xfmc
     * @param string $jehj
     * @return string
     */
    protected function emailHtml($kprq, $fpdm, $fphm, $gfmc, $xfmc, $jehj = '0.00')
    {
        $html = '<table width="606" align="center" border="0" cellspacing="0" cellpadding="0" background="" style="font-family:verdana;font-size:14px;line-height:180%">
				<tbody>
					<tr>
						<td height="66" colspan="3" background="">
						</td>
					</tr>
					<tr>
						<td width="34">&nbsp;</td>
						<td width="538">
							<p style="margin:0;padding:25px 0 15px">
								<strong>尊敬的客户您好</strong>：
							</p>
							
							<p style="text-indent:2em;margin:0;padding:0 0 0 0">
								发票信息如下：
							</p>
							<p style="text-indent:2em;margin:0;padding:0 0 0 0">
								开票日期：<span style="border-bottom:1px dashed #ccc;" t="5" times="">' . date('Y年m月d日', $kprq);

        $html .= '</span>
							</p>
							<p style="text-indent:2em;margin:0;padding:0 0 0 0">
								发票代码：<span style="border-bottom:1px dashed #ccc;z-index:1" t="7" onclick="return false;" data="' . $fpdm . '">' . $fpdm;

        $html .= '</span>
							</p>
							<p style="text-indent:2em;margin:0;padding:0 0 0 0">
								发票号码：<span style="border-bottom:1px dashed #ccc;z-index:1" t="7" onclick="return false;" data="' . $fphm . '">' . $fphm . '</span>
							</p>
							<p style="text-indent:2em;margin:0;padding:0 0 0 0">
								销方名称：' . $xfmc . '
							</p>
							<p style="text-indent:2em;margin:0;padding:0 0 0 0">
								购方名称：' . $gfmc . '
							</p>';

        if (!empty($jehj)) {
            $html .= '<p style="text-indent:2em;margin:0;padding:0 0 20px 0">价税合计：￥' . $jehj . '</p>';
        }

        $html .= '<p style="text-indent:2em;margin:0;padding:0 0 20px 0">
								<strong>附件是电子发票PDF文件</strong>，供下载使用。
							</p>
							<div style="border-top:1px dotted #ccc;height:1px;margin-top:10px"></div>
						</td>
						<td width="34">&nbsp;</td>
					</tr>
					<tr>
						<td height="20" colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td height="20" colspan="3" style="font-size:0;line-height:0" background="">&nbsp;</td>
					</tr>
				</tbody>
			</table>';

        return $html;
    }

    /**
     * 推送电子发票PDF至用户邮箱
     * @param $email
     * @param $pdfUrl
     * @param $fpdm
     * @param $fphm
     * @param $gfmc
     * @param string $xfmc
     * @param string $jehj
     * @return bool
     */
    protected function sendEmailToMember($email, $pdfUrl, $fpdm, $fphm, $gfmc, $xfmc='-', $jehj='-')
    {
        $kprq = time();
        $html = $this->emailHtml($kprq, $fpdm, $fphm, $gfmc, $xfmc, $jehj);

        $mailer = \Yii::$app->mailer->compose();
        $mailer->setFrom(['homemoney@51homemoney.com' => '我的发票']);
        $mailer->setTo($email);
        $mailer->setSubject("【我的发票】您收到一张新的电子发票[发票号码：{$fphm}]");
        $mailer->setHtmlBody($html);
        $mailer->attach($pdfUrl);
        return $mailer->send();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @author HQM 2018/11/23
     * @param array $log
     * @param string $pmOrderId
     */
    private function queryInvoiceLog($log, $pmOrderId)
    {
        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/queryInvoiceLog.log';
        $fileLog->messages[] =  ["pm_order_id:{$pmOrderId};" .json_encode($log), 8, 'application', microtime(true)];;
        $fileLog->export();
    }

}