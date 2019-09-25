<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/11/14
 * Time: 11:58
 */

namespace apps\api\controllers;

use apps\api\models\Member;
use apps\api\models\MobileOrder;
use components\wechatSDK\WechatSDK;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class MobileOrderController extends Controller
{
    public $modelClass = 'apps\api\models\MobileOrder';

    public function actions()
    {
        $actions = parent::actions(); // TODO: Change the autogenerated stub
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    /**
     * 手机充值
     * @author HQM 2018/12/14
     * @return string
     * @throws \components\wechatSDK\lib\WxPayException
     */
    public function actionPay()
    {
        $orderId = $this->post('orderId');

        $mpParams = \Yii::$app->params['wechatMini'];
        $wechatSDK = new WechatSDK($mpParams);
        $mobileOrder = MobileOrder::findOne($orderId);

        if ($mobileOrder && in_array($mobileOrder->status, [MobileOrder::STATUS_READY, MobileOrder::STATUS_WAIT_PAY])) {
            $mobileOrder->number = MobileOrder::createNumber();//重置一下NumberId
            $mobileOrder->status = MobileOrder::STATUS_WAIT_PAY;

            if ($mobileOrder->save()) {
                $userInfo = Member::findOne($mobileOrder->member_id);
                $js = $wechatSDK->wxJsApiPay(
                    $mobileOrder->number,
                    $userInfo->mp_open_id,
                    $mobileOrder->amount,
                    [
                        'notifyUrl' => "https://www.51homemoney.com/pay/mobile-wx-notify",
                        'goodsTag' => '手机充值',
                        'mpAppId' => $mpParams['appId']
                    ]
                );

                return $this->renderJsonSuccess(200, $js);
            }
        }

        return $this->renderJsonFail(40011);
    }

    public function actionList()
    {
        $list = [];
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = MobileOrder::find()
            ->where([
                'member_id' => $this->userId
            ])
            ->andWhere(['>', 'status', 1])
            ->orderBy('created_at DESC');
        $dataProvider->setPagination(new Pagination(['validatePage' => false]));
        $dataProvider->setSort(false);

        foreach($dataProvider->getModels() as $model){
            /**
             * @var $model MobileOrder
             */
            $list[] = [
                'orderId' => $model->id,
                'amount' => $model->amount,
                'payedAt' => date('Y-m-d H:i:s', $model->payed_at),
                'status' => $model->status,
                'phone' => $model->mobile,
                'statusText' => $model->statusText,
                'number' => $model->number,
            ];
        }

        return $this->renderJsonSuccess(200, $list);
    }

}