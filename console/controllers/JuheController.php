<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/23
 * Time: 9:19
 */

namespace console\controllers;


use common\models\MobileOrder;
use common\models\MobileOrderLog;
use components\juhe\Recharge;
use yii\console\Controller;

class JuheController extends Controller
{
    /**
     * 话费充值失败，重新充值 orderNumber
     * @param $orderNumber
     */
    public function actionToPrepaidPhone($orderNumber)
    {
        $mobileOrder = MobileOrder::findOne(['number' => $orderNumber]);
        if($mobileOrder){
            //进行充值

            $number = MobileOrder::createNumber();
            $this->stdout("订单号 {$mobileOrder->number} \n");
            $this->stdout("新的订单号：{$number} \n");

            $recharge = new Recharge('526084c4628d4ab41f974241abe1bafb', 'JHdd0b7ae7731aa448e571fc82f898dab9');
            $telRechargeRes = $recharge->telcz($mobileOrder->mobile, intval($mobileOrder->amount), $number); #可以选择的面额5、10、20、30、50、100、300
            if ($telRechargeRes['error_code'] == '0') {
                //提交话费充值成功，可以根据实际需求改写以下内容
                $mobileOrder->send_at = time();
                $mobileOrder->send_status = MobileOrder::SEND_STATUS_DONE;
                $mobileOrder->number= $number;
                $mobileOrder->save();

                $this->stdout("充值成功 \n");
            } else {
                $model = new MobileOrderLog();
                $model->data = serialize($telRechargeRes);
                $model->mobile = $mobileOrder->mobile;
                $model->amount = $mobileOrder->amount;
                $model->number = $mobileOrder->number;
                $model->save();

                $this->stdout("充值失败 \n");

                \Yii::warning(serialize($telRechargeRes));
            }
        }

        exit(1);
    }
}