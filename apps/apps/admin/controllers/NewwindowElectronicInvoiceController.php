<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/5/8
 * Time: 11:21
 */

namespace apps\admin\controllers;


use apps\admin\models\PmOrderFpzzResult;
use common\models\PmOrderFpzzItem;
use common\models\SysSwitch;
use components\newWindow\NewWindow;

class NewwindowElectronicInvoiceController extends Controller
{
    protected $missPermission = ['newwindow-electronic-invoice/newwindow'];
    public $enableCsrfValidation = false;

    /**
     * @throws \yii\base\ErrorException
     */
    public function actionNewwindow()
    {
        $postData = $this->post();

        $model = PmOrderFpzzResult::findOne(['id' => $postData['pmOrderFpResultId']]);
        if($model){
            $ids = explode(',', $model->item_ids);
            $fpItems = PmOrderFpzzItem::find()->where(['id' => $ids])->all();
            $chargeDetailIdList = [];
            foreach($fpItems as $key => $row){
                /**
                 * @var PmOrderFpzzItem $row
                 */
                $chargeDetailIdList[] = $row->charge_detail_id_list;
            }

            if($chargeDetailIdList){
                $chargeDetailIdList = implode(',', $chargeDetailIdList);
                $jpgUrl = '';
                if(isset($model->pmOrderFpzzPdf->save_path)){
                    $jpgUrl = \Yii::getAlias($model->pmOrderFpzzPdf->save_path);
                }

                $pushData = [
                    'ChargeDetailIDList' => $chargeDetailIdList,
                    'BillNum' => $model->pmOrderFpzzPdf->fphm,
                    'BillCode' => $model->pmOrderFpzzPdf->fpdm,
                    'BillJPGUrl' => $jpgUrl,
                    'BillTitle' => $model->pmOrderFpzzPdf->gfmc,
                    'CustomerTaxCode' => $model->pmOrderFpzzPdf->gfsh,
                    'CustomerAddressPhone' => $model->pmOrderFpzz->house_address,
                    'FHRUserName' => '陆雨君',
                    'KPRUserName' => ''
                ];

                $model->send_window_status = 1;
                $model->save();
                if(!SysSwitch::inVal('testMember', $model->member_id)){
                    $newWindow = (new NewWindow())->updateInvoiceStatus($pushData);
                    if($newWindow['Response']['Data']['NWRespCode'] == '0000'){
                        $model->send_window_status = 2;
                        $model->save();
                    }
                }
            }

        }

    }

}