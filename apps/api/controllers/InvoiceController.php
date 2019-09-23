<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/11/12
 */

namespace apps\api\controllers;


use apps\api\models\MemberExt;
use apps\api\models\PmOrder;
use apps\api\models\PmOrderFpzz;
use apps\mgt\models\FpzzLog;
use apps\pm\models\ProjectFpzzAccount;
use common\models\PmOrderFpzzItem;
use common\models\PmOrderItem;
use common\models\PmOrderNewwindowPdf;
use components\newWindow\NewWindow;
use components\wechatSDK\WechatSDK;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class InvoiceController extends Controller
{
    public $modelClass = 'apps\api\models\PmOrderFpzz';

    public function actions()
    {
        $actions = parent::actions(); // TODO: Change the autogenerated stub
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @param $orderId
     * @return array|string
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionItem($orderId)
    {
        $userId = $this->userId;

        $pmOrderFpzz = PmOrderFpzz::findOne(['pm_order_id' => $orderId]);
        if($pmOrderFpzz){
            return [
                'code' => 41010,
                'message' => '您已提交过申请！',
                'data' => ['id' => $pmOrderFpzz->id]
            ];
        }

        //查询是否已经开具
        $queryInvoice = $this->newwindowQueryInv($orderId);
        if($queryInvoice['code'] < 1){
            return ['code' => 4000, 'message' => $queryInvoice['message']];
        }

        //合并订单明细进行归类
        $pmOrderItem = $this->buildFpzzItem($orderId);
        if($pmOrderItem['code'] < 1){
            return ['code' => 4000, 'message' => $pmOrderItem['message']];
        }

        //检查项目是否停用开发票
        $projectAccount = ProjectFpzzAccount::findOne(['project_house_id' => $pmOrderItem['projectHouseId']]);
        $tips = $projectAccount->tips;
        /*if($projectAccount->status != 1){
            return ['code' => 4000, 'message' => $tips];
        }*/

        $memberExt = MemberExt::findOne(['member_id' => $userId]);

        $rtn = [
            'customerName' => $pmOrderItem['customerName'],
            'itemList' => $pmOrderItem['list'],
            'totalAmount' => $pmOrderItem['totalAmount'],
            'houseName' => $queryInvoice['houseName'],
            'phone' => $memberExt->phone,
            'email' => $memberExt->email,
            'tips' => $tips,
        ];

        return $this->renderJsonSuccess(200, $rtn);
    }

    /**
     * 请求开票
     * @return array|string
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $post = $this->post();
        $invoiceTy = $post['invoiceType'];

        if(empty($post['orderId'])){
            return ['code' => 41001, 'message' => '非法传值'];
        }

        $pmOrderFpzz = PmOrderFpzz::findOne(['pm_order_id' => $post['orderId'], 'member_id' => $this->userId]);
        if($pmOrderFpzz){
            return ['code' => 41010,
                'message' => '您已提交申请',
                'data' => ['id' => $pmOrderFpzz->id]
            ];
        }

        $pmOrder = PmOrder::findOne(['id' => $post['orderId'], 'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]]);
        if(!$pmOrder){
            return ['code' => 41001, 'message' => '未找到对应订单'];
        } else {
            $pmOrderFpzz = new PmOrderFpzz();
        }

        $type = trim($post['type']);
        $pmOrderFpzz->email = trim($post['email']);
        $pmOrderFpzz->phone = trim($post['phone']);
        $pmOrderFpzz->user_name = trim($post['customerName']);
        $pmOrderFpzz->pm_order_id = $post['orderId'];
        $pmOrderFpzz->client_type = 2;

        $pmOrderFpzz->member_id = $this->userId;

        //p 纸质前台开, e 电子发票
        if($type == 'p'){
            $pmOrderFpzz->type = 2;
            $pmOrderFpzz->setScenario('p_invoice');
        } else {
            $pmOrderFpzz->type = 1;
        }

        //单位发票
        if($invoiceTy == 2){
            $registerId = !empty($post['register_id']) ? trim($post['register_id']) : null;
            if(empty($registerId)){
                return ['code' => 41001, 'message' => '请填写纳税人识别号'];
            }
            $pmOrderFpzz->register_id = $registerId;
        }

        if(empty($pmOrderFpzz->user_name)){
            return ['code' => 41001, 'message' => '无法找到业主信息！'];
        }

        $pmOrderFpzz->category = $invoiceTy;
        $pmOrderFpzz->house_address = $pmOrder->house->ancestor_name;
        $pmOrderFpzz->house_id = $pmOrder->house_id;
        $pmOrderFpzz->project_house_id = $pmOrder->project_house_id;
        $pmOrderFpzz->total_amount = $pmOrder->total_amount;

        if($pmOrderFpzz->save()){
            $this->saveMemberExt($this->userId, $pmOrderFpzz->email, $pmOrderFpzz->phone);
            $this->updatePmOrderFpzzItem($pmOrderFpzz->id, $pmOrder->id);

            if($pmOrderFpzz->type == PmOrderFpzz::TYPE_E){
                $this->newwindowOpenFp($pmOrderFpzz->pm_order_id);
            }

            return $this->renderJsonSuccess();
        } else {
            return ['code' => 41001, 'message' => '无法处理请求'];
        }
    }

    /**
     * 请求新视窗开发票
     * @param $pmOrderId
     * @return bool
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    private function newwindowOpenFp($pmOrderId)
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
                $projectKeyUrl = $pmOrder->project->url_key;
                $chargeDetailIdList = implode(',', $chargeDetailIdList);
                $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList, 1);

                $this->writeFpzzLog($pmOrderId, $chargeDetailIdList, $newWindows);
                if(empty($newWindows)){
                    //发送发票模板消息通知用户
                    $this->sendWxTemplateToMember('o4sxcxLBzmvgpO_0BVo9gPbkQWxE', $projectKeyUrl);
                }

                if($newWindows['Response']['Data']['NWRespCode'] != '0000'){
                    $NWErrMsg = $newWindows['Response']['Data']['NWErrMsg'];
                    $NWErrMsg .= ',订单ID：' . $pmOrder->id;

                    //发送发票模板消息通知用户
                    $this->sendWxTemplateToMember('o4sxcxLBzmvgpO_0BVo9gPbkQWxE', $projectKeyUrl, $NWErrMsg);
                }
            }
        }

        return true;
    }

    /**
     * @param integer $pmOrderId
     * @param string|array $postData
     * @param string|array $result
     * @param string $type
     */
    private function writeFpzzLog($pmOrderId, $postData, $result, $type='-')
    {
        $fpzzLog = new FpzzLog();
        $fpzzLog->pm_order_id = $pmOrderId;
        $fpzzLog->post_data = serialize($postData);
        $fpzzLog->result = serialize($result);
        $fpzzLog->fp_cached_id = isset($result['object']['id']) ? $result['object']['id'] : '';
        $fpzzLog->type = $type;
        $fpzzLog->save();
    }

    /**
     * 电子发票模板消息
     * @param string $memberOpenId
     * @param string $projectKeyUrl
     * @param string $errorMessage
     */
    private function sendWxTemplateToMember($memberOpenId, $projectKeyUrl, $errorMessage='')
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
                    'value' => '开票失败：'. $errorMessage,
                ],
                'keyword3' => [
                    'value' => '-',
                ],
                'keyword4' => [
                    'value' => '-',
                ],
                'keyword5' => [
                    'value' => '-',
                ],
                'remark' => [
                    'value' => '如有疑问，请联系项目管家',
                ],
            ]
        ];
        $wx_obj = new WechatSDK(\Yii::$app->params['wechat']);
        $wx_obj->sendTemplateMessage($postData);
    }

    private function saveMemberExt($memberId, $email, $phone)
    {
        $model = MemberExt::findOne(['member_id' => $memberId]) ?? new MemberExt();
        $model->member_id = $memberId;
        $model->email = $email;
        $model->phone= $phone;
        $model->save();
    }

    /**
     * 更新发票已操作明细状态
     * @param int $fpzzOrderId
     * @param int $pmOrderId
     * @return int
     */
    private function updatePmOrderFpzzItem(int $fpzzOrderId, int $pmOrderId)
    {
        $update = PmOrderFpzzItem::updateAll(['pm_order_fpzz_id' => $fpzzOrderId], ['pm_order_id' => $pmOrderId]);

        return $update;
    }

    /**
     * 新视窗查询缴费明细是否已经开票
     * @param integer $pmOrderId
     * @return array
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    private function newwindowQueryInv($pmOrderId)
    {
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId]);
        $houseName = $pmOrder->house->ancestor_name;

        if(isset($pmOrder->items)){
            $chargeDetailIdList = [];
            foreach ($pmOrder->items as $row){
                /**
                 * @var PmOrderItem $row
                 */
                if(!empty($row->charge_detail_id_list)){
                    $chargeDetailIdList[] = $row->charge_detail_id_list;
                }
            }

            if(empty($chargeDetailIdList)){
                return ['code' => 0, 'message' => '商品明细为空'];
            }

            $chargeDetailIdList = implode(',', $chargeDetailIdList);
            $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList);

            if(empty($newWindows)){
                return ['code' => 0, 'message' => '请求服务商失败！'];
            }
            if($newWindows['Response']['Data']['NWRespCode'] != '0000'){
                return ['code' => 0, 'message' => '请求服务商失败！'];
            }

            $record = $newWindows['Response']['Data']['Record'];
            if(!empty($record)){
                if(!empty($record[0]['BillPDFUrl'])){
                    return ['code' => 0, 'message' => '无法开具，您或者已在前台开具'];
                }
            }

            return ['code' => 2, 'houseName' => $houseName];
        }

        return ['code' => 0, 'message' => '无订单明细'];
    }

    /**
     * 生成发票明细记录
     * @param integer $pmOrderId
     * @return bool|array
     * @throws \yii\db\Exception
     */
    private function buildFpzzItem($pmOrderId)
    {
        $pmOrderFpzz = PmOrderFpzz::findOne(['pm_order_id' => $pmOrderId]);
        if($pmOrderFpzz){
            return ['code' => 0, 'message' => '已存在'];
        }

        /**
         * @var $model PmOrder
         */
        $model = PmOrder::find()
            ->where(['member_id' => $this->userId, 'id' => $pmOrderId, 'discount_status' => 0])
            ->andWhere(['status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]])
            ->one();

        if(!$model){
            return ['code' => 0, 'message' => '无账单'];
        }

        if($model->total_amount < 1){
            return ['code' => 0, 'message' => '该账单金额无法开具发票'];
        }

        $totalAmount = $model->total_amount;
        $projectHouseId = $model->project_house_id;
        $pmOrderItem = PmOrderItem::findOne(['pm_order_id' => $pmOrderId]);
        $accountName = $pmOrderItem->customer_name;
        if(empty($accountName)){
            return ['code' => 0, 'message' => '未找到客户名称！'];
        }

        $rs = PmOrderItem::find()
            ->where(['pm_order_id' => $pmOrderId,])
            ->orderBy('charge_item_name DESC')
            ->asArray()
            ->all();

        //检查是否已经生成开票明细
        $pmOrderFpzzItem = PmOrderFpzzItem::find()
            ->select('spmc, ggxh, origin_amount')
            ->where(['pm_order_id' => $pmOrderId, 'status' => PmOrderFpzzItem::STATUS_DEFAULT])
            ->asArray()
            ->all();

        $lists = $spobj = [];
        $originAmount = 0;

        if($pmOrderFpzzItem > 0){
            foreach($pmOrderFpzzItem as $key => $iRow){
                $lists[] = [
                    'spmc' => $iRow['spmc'],
                    'ggxh' => $iRow['ggxh'],
                    'origin_amount' => $iRow['origin_amount'],
                ];
                $originAmount = bcadd($iRow['origin_amount'], $originAmount, 2);
            }
        }

        if(count($lists) > 0){
            return ['code' => 1, 'projectHouseId' => $projectHouseId, 'totalAmount' => $totalAmount , 'customerName' => $accountName,'list' => $lists];
        }

        $tempArray = [];

        foreach ($rs as $key => $row){
            $temp = explode('-', $row['bill_date']);
            $tempArray[$row['charge_item_name']]['contract_no'][] = $row['contract_no'];

            $chargeDetailIdList = $row['charge_detail_id_list'];
            if(empty($chargeDetailIdList)){
                $conNoIds = explode('-' ,$row['contract_no']);
                $chargeDetailIdList = $conNoIds[1];
            }

            $tempArray[$row['charge_item_name']]['charge_detail_id_list'][] = $chargeDetailIdList;
            $tempArray[$row['charge_item_name']]['bill_date'][] = $row['bill_date'];
            $tempArray[$row['charge_item_name']]['date'][] = strtotime($temp[0]);
            $tempArray[$row['charge_item_name']]['date'][] = strtotime($temp[1]);
            $tempArray[$row['charge_item_name']]['total_amount'] = isset($tempArray[$row['charge_item_name']]['total_amount']) ? bcadd($tempArray[$row['charge_item_name']]['total_amount'], $row['amount'], 2) : $row['amount'];
        }

        $arrayKeys = array_keys($tempArray);

        foreach ($arrayKeys as $key){
            $je = $tempArray[$key]['total_amount'];
            $contractNo = implode(',', $tempArray[$key]['contract_no']);
            $cDetailIdList = implode(',', $tempArray[$key]['charge_detail_id_list']);

            $dj = 0;
            $lists[] = [
                'spmc' => $key,
                'ggxh' => date('Y.n.j', min($tempArray[$key]['date'])) . '-' . date('Y.n.j', max($tempArray[$key]['date'])),
                'origin_amount' => $je,
            ];

            $spobj[] = [
                'pm_order_id' => $model->id,
                'contract_no' => $contractNo,
                'charge_detail_id_list' => $cDetailIdList,
                'spmc' => $key,
                'spbm' => '-',
                'ggxh' => date('Y.n.j', min($tempArray[$key]['date'])) . '-' . date('Y.n.j', max($tempArray[$key]['date'])),
                'dw' => '-',
                'sl' => '',
                'dj' => empty($dj) ? '' : $dj,
                'origin_amount' => $je,
                'slv' => '%',
                'status' => PmOrderFpzzItem::STATUS_DEFAULT,
                'created_at' => time(),
            ];
        }

        PmOrderFpzzItem::deleteAll(['pm_order_id' => $pmOrderId]);

        $definedArray = [
            'pm_order_id' => $model->id,
            'contract_no' => '',
            'charge_detail_id_list' => '',
            'spmc' => '用来显示总：金额+税额',
            'spbm' => '',
            'ggxh' => '',
            'dw' => '',
            'sl' => '',
            'dj' => '',
            'origin_amount' => $originAmount,
            'slv' => '',
            'status' => 0,
            'created_at' => time(),
        ];
        array_push($spobj, $definedArray);

        $insert = \Yii::$app->db->createCommand()->batchInsert('pm_order_fpzz_item', ['pm_order_id', 'contract_no', 'charge_detail_id_list', 'spmc', 'spbm', 'ggxh', 'dw', 'sl', 'dj', 'origin_amount', 'slv', 'status', 'created_at'], $spobj)->execute();

        if($insert){
            unset($tempArray);
            unset($spobj);
        } else {
            PmOrderFpzzItem::deleteAll(['pm_order_id' => $pmOrderId]);
        }

        return ['code' => 1, 'projectHouseId' => $projectHouseId, 'totalAmount' => $totalAmount , 'customerName' => $accountName,'list' => $lists];
    }

    public function actionList()
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderFpzz::find()
            ->where([
                'member_id' => $this->userId
            ])
            ->orderBy('created_at DESC');
        $dataProvider->setSort(false);
        $dataProvider->setPagination(new Pagination(['validatePage' => false]));

        $list = [];
        foreach ($dataProvider->getModels() as $model){
            /**
             * @var $model PmOrderFpzz
             */
            $list[] = [
                'id' => $model->id,
                'totalAmount' => $model->total_amount,
                'statusText' => $model->statusText,
                'style' => $model->getStatusStyle(),
                'typeText' => $model->typeText,
                'createdAt' => date('Y-m-d H:i:s', $model->created_at)
            ];
        }

        return $this->renderJsonSuccess(200, $list);
    }

    public function actionDetail($id=0)
    {
        $pmOrderFpzz = PmOrderFpzz::findOne(['id' => $id, 'member_id' => $this->userId]);
        if(!$pmOrderFpzz){
            return $this->renderJsonFail(80001);
        }
        $pmOrderFpzzItem = PmOrderFpzzItem::find()->where(['pm_order_fpzz_id' => $id])
            ->andWhere(['>', 'status', 0])->all();
        $pdfList = $childList = [];
        foreach($pmOrderFpzzItem as $row){
            /**
             * @var $row PmOrderFpzzItem
             */
            $childList[] = [
                'spmc' => $row->spmc,
                'ggxh' => $row->ggxh,
                'originAmount' => $row->origin_amount,
            ];
        }
        foreach($pmOrderFpzz->pmOrderNewwindowPdf as $row){
            /**
             * @var $row PmOrderNewwindowPdf
             */
            $pdfList[] = [
                'id' => $row->id
            ];
        }

        $detail = [
            'id' => $id,
            'createdAt' => date('Y-m-d', $pmOrderFpzz->created_at),
            'email' => $pmOrderFpzz->email,
            'title' => $pmOrderFpzz->user_name,
            'totalAmount' => $pmOrderFpzz->total_amount,
            'pmOrderId' => $pmOrderFpzz->pm_order_id,
            'pdfList' => $pdfList,
            'childList' => $childList
        ];

        return $this->renderJsonSuccess(200, $detail);
    }

    public function actionJpg($id=0)
    {
        $model = PmOrderNewwindowPdf::findOne([
            'id' => $id,
        ]);
        if(isset($model->save_path) && !empty($model->save_path)){
            $pdfJpg = \Yii::getAlias($model->save_path);
            $pdfJpg .= '.jpg';

            return $this->renderJsonSuccess(200, [$pdfJpg]);
        }

        return [
            'code' => 40444,
            'message' => '无法查看JPG格式',
        ];
    }

    /**
     * 获取邮箱地址
     * @author HQM 2019/02/14
     * @return array|string
     */
    public function actionEmail()
    {
        $list = ['email' => ''];
        $member = MemberExt::findOne(['member_id' => $this->userId]);
        if($member){
            $list['email'] = $member->email;
        }

        return $this->renderJsonSuccess(200, $list);
    }

    /**
     * 重发邮件
     * @author HQM 2019/02/14
     * @return array|string
     */
    public function actionResendEmail()
    {
        $email = $this->post('email', null);
        $pdfId = $this->post('pdfId', null);

        $rule = '/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/';
        $emailMatch = preg_match($rule, $email);

        if(!empty($pdfId) && $emailMatch){
            $model = PmOrderNewwindowPdf::findOne(['id' => $pdfId]);
            if($model){
                $pmOrderFpzz = PmOrderFpzz::findOne(['id' => $model->pm_order_fpzz_id, 'member_id' => $this->userId]);
                if($pmOrderFpzz){
                    $this->sendEmail($email, $model->bill_pdf_url, $model->created_at, $model->bill_code, $model->bill_num, $pmOrderFpzz->user_name, '-', '-');
                    return $this->renderJsonSuccess();
                }

                return $this->renderJsonFail();
            }
        }

        return $this->renderJsonFail(40011);
    }

    /**
     * @author HQM 2019/02/14
     * @param string $recipientsEmail
     * @param string $pdf
     * @param int $kprq
     * @param integer $fpdm
     * @param integer $fphm
     * @param string $gfmc
     * @param string $xfmc
     * @param string $jehj
     * @return bool
     */
    private function sendEmail($recipientsEmail, $pdf, $kprq, $fpdm, $fphm, $gfmc, $xfmc, $jehj)
    {
        $html = $this->emailHtml($kprq, $fpdm, $fphm, $gfmc, $xfmc, $jehj);

        $mailer = \Yii::$app->mailer->compose();
        $mailer->setTo($recipientsEmail);
        $mailer->setSubject('电子发票');
        $mailer->setHtmlBody($html);
        $mailer->attach($pdf);
        if($mailer->send()){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @author HQM 2019/02/14
     * @param int $kprq
     * @param int $fpdm
     * @param int $fphm
     * @param string $gfmc
     * @param string $xfmc
     * @param string $jehj
     * @return string
     */
    private function emailHtml($kprq, $fpdm, $fphm, $gfmc, $xfmc, $jehj = '0.00')
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

}