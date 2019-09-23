<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/22
 * Time: 11:01
 */

namespace apps\www\controllers;


use apps\mgt\models\FpzzLog;
use apps\mgt\models\PmOrderFpzzResult;
use apps\pm\models\ProjectFpzzAccount;
use apps\www\models\MemberExt;
use common\models\FpzzFeedback;
use common\models\PmOrder;
use common\models\PmOrderFpzz;
use common\models\PmOrderFpzzItem;
use common\models\PmOrderItem;
use common\models\PmOrderNewwindowPdf;
use common\models\WechatInvoiceCard;
use components\helper\HttpRequest;
use components\newWindow\NewWindow;
use components\Tcis\Tcis;
use components\wechatSDK\WechatSDK;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;

class TcisController extends Controller
{
    protected $missPermission = ['tcis/fpzz-notify'];
    public $enableCsrfValidation = false;

    public function actionRecipt($id=null)
    {
        $userId = $this->user->id;
        $pmOrder = PmOrder::findOne(['id' => $id, 'member_id' => $userId]);

        $projectAccount = ProjectFpzzAccount::findOne(['project_house_id' => $pmOrder->project_house_id]);
        $pmOrderFpzz = new PmOrderFpzz();
        $memberExt = MemberExt::findOne(['member_id' => $userId]);

        $rtn = [
            'pmOrder' => $pmOrder,
            'pmOrderFpzz' => $pmOrderFpzz,
            'memberExt' => $memberExt,
            'id' => $id,
            'tips' => $projectAccount->tips,
        ];

        return $this->render('recipt', $rtn);
    }

    public function actionSave()
    {
        if($this->isPost && $this->isAjax){
            $post = $this->post();
            $invoiceTy = $post['invoice-ty'];

            if(empty($post['PmOrderFpzz']['pm_order_id'])){
                return $this->renderJsonFail('非法传值');
            }

            $pmOrderFpzz = PmOrderFpzz::findOne(['pm_order_id' => $post['PmOrderFpzz']['pm_order_id'], 'member_id' => $this->user->id]);
            $pmOrder = PmOrder::findOne(['id' => $post['PmOrderFpzz']['pm_order_id'], 'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]]);
            if(!$pmOrder || $pmOrderFpzz){
                return $this->renderJsonFail('无法开具！');
            } else {
                $pmOrderFpzz = new PmOrderFpzz();
            }

            $pmOrderFpzz->load($this->post());
            $pmOrderFpzz->member_id = $this->user->id;

            if($pmOrderFpzz->type == 2){
                $pmOrderFpzz->setScenario('p_invoice');
            }

            if($invoiceTy == 2){
                $registerId = !empty($post['register_id']) ? trim($post['register_id']) : null;
                if(empty($registerId)){
                    return $this->renderJsonFail('请填写纳税人识别号');
                }
                $pmOrderFpzz->register_id = $registerId;
            }

            if(empty($pmOrderFpzz->user_name)){
                return $this->renderJsonFail('无法找到业主信息！');
            }

            $projectHouseId = $pmOrder->project_house_id;
            //检查项目是否停用开发票
            $projectF = ProjectFpzzAccount::findOne(['project_house_id' => $projectHouseId]);
            if($projectF->status != 1){
                return $this->renderJsonFail($projectF->tips);
            }

            $pmOrderFpzz->category = $invoiceTy;
            $pmOrderFpzz->house_address = $pmOrder->house->ancestor_name;
            $pmOrderFpzz->house_id = $pmOrder->house_id;
            $pmOrderFpzz->project_house_id = $projectHouseId;
            $pmOrderFpzz->total_amount = $pmOrder->total_amount;

            if($pmOrderFpzz->save()){
                //记录业主接收邮箱地址
                $this->saveMemberExt($this->user->id, $pmOrderFpzz->email, $pmOrderFpzz->phone);
                $this->updatePmOrderFpzzItem($pmOrderFpzz->id, $pmOrder->id);

                if($pmOrderFpzz->type == PmOrderFpzz::TYPE_E){
                    $requestUrl = 'http://testmgt.51homemoney.com/member-bill-notify/newwindow-open-fp';
                    $data = [
                        'pmOrderFpzzId' => $pmOrderFpzz->pm_order_id,
                    ];
                    HttpRequest::post($requestUrl, $data);
                }

                return $this->renderJsonSuccess('');
            } else {
                return $this->renderJsonFail('error', -1, ['errorMsg' => $pmOrderFpzz->getFirstErrors()]);
            }
        }

        return $this->redirect(['lists']);
    }

    public function actionLists()
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderFpzz::find()
            ->where([
                'member_id' => $this->user->id
            ])
            ->orderBy('created_at DESC');
        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('fpzz-list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('lists', [
                'dataProvider' => $dataProvider
            ]);
        }
    }

    public function actionDetail($id=null)
    {
        $model = PmOrderFpzz::findOne($id);
        $pmOrderFpzzItem = PmOrderFpzzItem::find()
            ->where(['pm_order_fpzz_id' => $id])
            ->andWhere(['>', 'status', 0])->all();

        $rtn = [
            'model' => $model,
            'pmOrderFpzzItem' => $pmOrderFpzzItem,
        ];
        return $this->render('detail', $rtn);
    }

    /**
     * 重新发送到邮箱（最新：默认使用此方法）
     * @author HQM 2018-10-19
     * @param $id
     * @return string
     */
    public function actionResendEmail($id)
    {
        $model = PmOrderNewwindowPdf::findOne(['id' => $id]);
        $memberExt = MemberExt::findOne(['member_id' => $this->user->id]);
        $email = '';
        if($memberExt){
            $email = $memberExt->email;
        }

        return $this->render('send-again', ['model' => $model, 'email' => $email]);
    }

    public function actionChangeEmailSubmit()
    {
        if($this->isAjax && $this->isPost){
            $post = $this->post();
            $rule = '/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/';
            if(!empty($post['id']) && preg_match($rule, $post['email'])){
                $model = PmOrderNewwindowPdf::findOne(['id' => $post['id']]);
                if($model){
                    $pmOrderFpzz = PmOrderFpzz::findOne(['id' => $model->pm_order_fpzz_id, 'member_id' => $this->user->id]);
                    if($pmOrderFpzz){
                        $this->sendEmail($post['email'], $model->bill_pdf_url, $model->created_at, $model->bill_code, $model->bill_num, $pmOrderFpzz->user_name, '-', '-');
                        return $this->renderJsonSuccess('');
                    }

                    return $this->renderJsonFail('Error');
                }
            }
        }

        return $this->renderJsonFail('Error');
    }

    /**
     * 获取PDF转JPG格式
     * @param int|null $id
     * @return string
     */
    public function actionShowPdf(int $id=null)
    {
        $model = PmOrderNewwindowPdf::findOne([
            'id' => $id,
        ]);
        if(isset($model->save_path)){
            $pdfJpg = \Yii::getAlias($model->save_path);
            $pdfJpg .= '.jpg';

            return $this->renderJsonSuccess(['pdfJpg' => $pdfJpg]);
        }

        return $this->renderJsonFail('未提供图片格式查看');
    }

    /**
     * 开票结果回调
     * @return bool
     * @return mixed
     */
    public function actionFpzzNotify()
    {
        $data = file_get_contents("php://input");
        $newArray = [];

        if($data){
            parse_str($data, $newArray);

            $this->writeFpzzLog(0, $newArray, [], 'async');

            if($newArray['status'] == 2){
                if(!empty($newArray['url'])){
                    if(isset($newArray['email'])){
                        $this->sendEmail($newArray['email'], $newArray['url'], '-', '-', '-', '-', '-', '-');
                    }
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 生成发票明细记录
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionBuildFpzzItem()
    {
        if($this->isAjax || $this->isPost){
            $pmOrderId = $this->post('order_id');

            /*$dayJ = date('j', time());
            if($dayJ < 15){
                return $this->renderJsonFail('每月15号之前无法申请');
            }*/

            $pmOrderFpzz = PmOrderFpzz::findOne(['pm_order_id' => $pmOrderId]);
            if($pmOrderFpzz){
                return $this->renderJsonFail('您已提交过申请！');
            }

            $model = PmOrder::find()
                ->where(['member_id' => $this->user->id, 'id' => $pmOrderId, 'discount_status' => 0])
                ->andWhere(['status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]])
                ->one();

            if(!$model){
                return $this->renderJsonFail('无账单');
            }

            if($model->total_amount < 1){
                return $this->renderJsonFail('该账单金额无法开具发票');
            }

            $pmOrderItem = PmOrderItem::findOne(['pm_order_id' => $pmOrderId]);
            $accountName = $pmOrderItem->customer_name;
            if(empty($accountName)){
                return $this->renderJsonFail('未找到客户名称！');
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
                return $this->renderJsonSuccess(['lists' => $lists, 'amount' => $originAmount, 'accountName' => $accountName]);
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

//                $taxName = PmOrderFpzzSpmc::findOne(['spmc' => $key]);
                $taxName = true;
                if($taxName){
//                    $res = self::getSpmc($model->project_house_id, $taxName->as_spmc);
                    $res['object'] = true;
                    if($res['object'])
                    {
                        $dj = 0;

                        /*if($res['object']['dj'] > 0) {
                            $dj = bcdiv($res['object']['dj'], 1 + $res['object']['slv'], 15);
                        }*/

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

                        $originAmount = bcadd($je, $originAmount, 2);
                    } else {
                        PmOrderFpzzItem::deleteAll(['pm_order_id' => $pmOrderId]);
                        return $this->renderJsonFail('无法获取发票内容');
                    }
                }
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
                return $this->renderJsonSuccess(['lists' => $lists, 'amount' => $originAmount, 'accountName' => $accountName]);
            } else {
                PmOrderFpzzItem::deleteAll(['pm_order_id' => $pmOrderId]);
            }
        }

        return $this->renderJsonFail('请求失败！');
    }

    /**
     * 新视窗查询缴费明细是否已经开票
     * @return false|string
     * @throws ErrorException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionNewwindowQueryInv()
    {
        $pmOrderId = $this->get('order_id');
        $pmOrder = PmOrder::findOne(['id' => $pmOrderId]);

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

            if(empty($chargeDetailIdList)){
                return $this->renderJsonFail('商品明细为空');
            }

            $chargeDetailIdList = implode(',', $chargeDetailIdList);
            $newWindows = (new NewWindow())->queryInvoice($chargeDetailIdList);

            if(empty($newWindows)){
                return $this->renderJsonFail('请求服务商失败！');
            }
            if($newWindows['Response']['Data']['NWRespCode'] != '0000'){
                return $this->renderJsonFail('请求服务商失败！');
            }

            $record = $newWindows['Response']['Data']['Record'];
            if(!empty($record)){
                if(!empty($record[0]['BillPDFUrl'])){
                    return $this->renderJsonFail('无法开具，您或者已在前台开具');
                }
            }

            return $this->renderJsonSuccess(['orderId' => $pmOrderId]);
        }

        return $this->renderJsonFail('无法请求服务');
    }

    /**
     * 记录业主开票反馈信息
     */
    public function actionFeedback()
    {
        if($this->isAjax && $this->isPost){
            $id = $this->post('id');
            $memberId = $this->user->id;
            $ip = isset(\Yii::$app->request->userIP) ? \Yii::$app->request->userIP : '';

            FpzzFeedback::writeIn($memberId, $id, $ip);

            return $this->renderJsonSuccess($id);
        }

        return $this->renderJsonFail('反馈出错!');
    }

    /**
     * 跳转关注财到家服务号页面
     * @return string
     */
    public function actionSuccess()
    {
        return $this->render('success');
    }

    public function actionTest()
    {
        $wechatParam = \Yii::$app->params['wechat'];
        $wechatSDK = new WechatSDK($wechatParam);
        $contact = [
            'contact' => [
                'phone' => 13202446168,
                'time_out' => 60
            ]
        ];
        //设置商户联系
        $setBizattr = $wechatSDK->invoiceSetBizattr($contact);
        $localUrl = '';
        if($setBizattr){
            //获取授权页 ticket
            $wxcardTicket = $wechatSDK->getWxcardTicket();
            if($wxcardTicket){
                //获取授权页链接
                $authData = [
                    's_pappid' => 'd3gxN2RjOThhNGE1NjE0YWVjX4ppxxGVnFrNVh_0auf2WSDfF2NRpeXN7o1cuFX-NCkk',
                    'order_id' => PmOrder::createNumber(),
                    'money' => 100,
                    'timestamp' => time(),
                    'source' => 'web',
                    'ticket' => $wxcardTicket,
                    'type' => 2,
                ];
                $authUrl = $wechatSDK->invoiceGetAuthurl($authData);
                if($authUrl){
                    $localUrl = $authUrl['auth_url'];
                }
            }
        }

        return $this->render('test', ['localUrl' => $localUrl]);
    }

    /**
     * 获取微信电子发票授权页链接
     * @author HQM 2018-09-20
     * @return false|string
     */
    public function actionGetAuthurl()
    {
        $fid = $this->get('fid');
        if(empty($fid)){
            return $this->renderJsonFail('order_number is empty');
        }
        $fInfo = PmOrderFpzz::find()
            ->where(['id' => $fid, 'member_id' => $this->user->id])->one();
        if(!$fInfo){
            return $this->renderJsonFail('order is empty');
        }

        /**
         * 获取微信授权页
         * @var $fInfo PmOrderFpzz
         */
        $authurl = $this->getAuthurl($fInfo->pmOrder->number, $fInfo->total_amount);
        if(empty($authurl)){
            return $this->renderJsonFail('授权失败');
        }

        return $this->renderJsonSuccess(['auth_url' => $authurl]);
    }

    /**
     * 获取授权页链接
     * @author HQM 2018-09-20
     * @param string $orderNumber
     * @param $money
     * @return string
     */
    private function getAuthurl($orderNumber, $money)
    {
        $wechatInvoiceCard = WechatInvoiceCard::findOne(['pm_order_number' => $orderNumber]);
        if($wechatInvoiceCard){
            return false;
        }

        $wechatParam = \Yii::$app->params['wechat'];
        $wechatSDK = new WechatSDK($wechatParam);
        $contact = [
            'contact' => [
                'phone' => '020-83339571',
                'time_out' => 60
            ]
        ];
        //设置商户联系
        $setBizattr = $wechatSDK->invoiceSetBizattr($contact);
        $localUrl = '';
        if($setBizattr){
            //获取授权页 ticket
            $wxcardTicket = $wechatSDK->getWxcardTicket();
            if($wxcardTicket){
                //获取授权页链接
                $authData = [
                    's_pappid' => 'd3gxN2RjOThhNGE1NjE0YWVjX4ppxxGVnFrNVh_0auf2WSDfF2NRpeXN7o1cuFX-NCkk',
                    'order_id' => $orderNumber,
                    'money' => $money * 100,
                    'timestamp' => time(),
                    'source' => 'web',
                    'ticket' => $wxcardTicket,
                    'type' => 2,
                ];
                $authUrl = $wechatSDK->invoiceGetAuthurl($authData);
                if($authUrl){
                    $localUrl = $authUrl['auth_url'];
                }
            }
        }

        return $localUrl;
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
     * 记录业主接收电子发票的邮箱、手机
     * @param $memberId
     * @param $email
     * @param $phone
     */
    private function saveMemberExt($memberId, $email, $phone)
    {
        $model = MemberExt::findOne(['member_id' => $memberId]) ?? new MemberExt();
        $model->member_id = $memberId;
        $model->email = $email;
        $model->phone= $phone;
        $model->save();
    }

    /**
     * @param integer $pmOrderFpzzId
     * @param string|array $postData
     * @param string|array $result
     * @param string $type
     */
    private function writeFpzzLog($pmOrderFpzzId, $postData, $result, $type='-')
    {
        $fpzzLog = new FpzzLog();
        $fpzzLog->pm_order_fpzz_id = $pmOrderFpzzId;
        $fpzzLog->post_data = serialize($postData);
        $fpzzLog->result = serialize($result);
        $fpzzLog->fp_cached_id = isset($result['object']['id']) ? $result['object']['id'] : '';
        $fpzzLog->type = $type;
        $fpzzLog->save();
    }

    /**
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
     * @param integer $pmOrderFpzzId
     * @param string $resultId
     * @param integer $pmOrderId
     * @param string $email
     * @param integer $memberId
     * @param string $itemIds
     * @param string $projectHouseId
     * @param float $jehj
     */
    private function beforeSaveFpId($pmOrderFpzzId, $resultId, $pmOrderId, $email, $memberId, $itemIds, $projectHouseId, $jehj=0.00)
    {
        $model = new PmOrderFpzzResult();
        $model->pm_order_fpzz_id = $pmOrderFpzzId;
        $model->result_id = $resultId;
        $model->pm_order_id = $pmOrderId;
        $model->email = $email;
        $model->member_id = $memberId;
        $model->item_ids = $itemIds;
        $model->project_house_id = $projectHouseId;
        $model->jehj = $jehj;
        $model->save();
    }

    /**
     * @param $projectHouseId
     * @param $asSpmc
     * @return bool|mixed
     * @throws ErrorException
     */
    private static function getSpmc($projectHouseId, $asSpmc)
    {
        $fpzzAccount = ProjectFpzzAccount::findOne(['project_house_id' => $projectHouseId, 'status' => 1]);
        if(!$fpzzAccount){
            return 0;
        }

        $appid = $fpzzAccount->appid;
        $prikey = $fpzzAccount->prikey;

        $res = (new Tcis($appid, $prikey))->getSpmc($asSpmc);

        return $res;
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