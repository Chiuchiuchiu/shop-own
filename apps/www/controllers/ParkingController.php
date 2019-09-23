<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/11/9
 * Time: 11:06
 */

namespace apps\www\controllers;


use apps\www\models\MemberCar;
use common\models\ParkingLog;
use common\models\ParkingOrder;
use common\models\ProjectParkingOneToOne;
use common\models\SysSwitch;
use components\genvict\Genvict;
use components\IRain\IRain;
use yii\web\NotFoundHttpException;

class ParkingController extends Controller
{
    public function actionIndex()
    {
        if(empty($this->project->house_id)){
            return $this->render('result', [
                'msg' => '服务异常，请前去收费岗缴费！',
                'aChart' => '返回首页',
                'href' => '/'
            ]);
        }

        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);
        $postData = [
            'openid' => $this->user->wechat_open_id,
            'parkingid' => $projectParkInfo->parking_id,
        ];
        $parkingInfo = (new Genvict($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingInfo($postData);
        $parkingInfoResult = $parkingInfo->getValue();
        if(!$parkingInfoResult){
            $errors = $parkingInfo->getErrors();
            ParkingLog::writeLog($this->user->id, $parkingInfoResult, $errors['msg'], $errors['code']);
            return $this->render('result', [
                'msg' => '服务异常，无车场信息！',
                'aChart' => '返回首页',
                'href' => '/'
            ]);
        }

        $memberPlate = MemberCar::find()->where(['member_id' => $this->user->id])->all();

        $viewData = [
            'parkingName' => $parkingInfoResult['Data']['parkingname'],
            'parkingId' => $parkingInfoResult['Data']['parkingid'],
        ];

        return $this->render('index', ['data' => $viewData, 'memberPlate' => $memberPlate]);
    }

    public function actionTempBill($parkingId, $plateNo = '粤B12345')
    {
        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);
        $postData = [
            'parkingid' => $parkingId,
            'plateno' => $plateNo,
            'platecolor' => '蓝',
        ];

        $res = (new Genvict($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingFeeTempCalcFeePlateno($postData);
        $data = $res->getValue();

        if($data){
            $data['Data']['parkingtimes'] = self::getTimeDhm($data['Data']['parkingtimes']);
            $data['Data']['payAmount'] = bcsub($data['Data']['paidamt'], $data['Data']['discount'], 2);

            //写入日志
            ParkingLog::writeLog($this->user->id, $data, '获取临卡费用', 'gen');

            return $this->render('temp-bill', ['data' => $data['Data'], 'payType' => 't']);
        }
        $errors = $res->getErrors();
        ParkingLog::writeLog($this->user->id, $postData, $errors['msg'], $errors['code']);

        return $this->render('result', ['msg' => $errors['tipsMsg'], 'aChart' => '返回',]);
    }

    public function actionMonth($parkingId, $plateNo)
    {

        return $this->render('month', ['parkingId' => $parkingId, 'plateNo' => $plateNo]);
    }

    public function actionMonthBill($parkingId, $plateNo,int $month=1)
    {
        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);
        $postData = [
            'parkingid' => $parkingId,
            'plateno' => $plateNo,
            'quantity' => $month,
        ];
        $monthFee = (new Genvict($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingMonthCalcFeePlateno($postData);
        $monthFeeVal = $monthFee->getValue();
        if(empty($monthFeeVal)){
            ParkingLog::writeLog($this->user->id, $postData, $monthFee->errMsg, $monthFee->errCode);
            return $this->render('result');
        }

        ParkingLog::writeLog($this->user->id, $monthFeeVal, '-', '-');

        return $this->render('month-bill', ['data' => $monthFeeVal['Data']]);
    }

    public function actionBillSubmit()
    {
        if($this->isGet){
            return $this->renderJsonFail('服务异常');
        }

        $plateno = $this->post('plateno');
        $payType = $this->post('payType');
        $parkingId = $this->post('parkingid');
        $quantity = $this->post('quantity', 1);

        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);

        $postData = [
            'parkingid' => $parkingId,
            'plateno' => $plateno,
            'platecolor' => '蓝',
        ];

        //临时停车费用
        $tempCalcFee = (new Genvict($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingFeeTempCalcFeePlateno($postData);
        $result = $tempCalcFee->getValue();

        if($result){
            $discount = $result['Data']['discount'];
            $receivable = $result['Data']['receivable'];
            $effectDate = 0;
            $expireDate = 0;

            //费用
            $orderAmount = bcsub($result['Data']['paidamt'], $result['Data']['discount'], 2);

            //入场时间
            if(isset($result['Data']['entrytime'])){
                $effectDate = strtotime($result['Data']['entrytime']);
            }

            $orderAmount = floatval($orderAmount);
            if(empty($orderAmount)){
                return $this->renderJsonFail('支付费用不能为0');
            }

            $model = new ParkingOrder();
            $model->member_id = $this->user->id;
            $model->type = $payType == 't' ? 1 : 2;
            $model->project_house_id = $this->project->house_id;
            $model->amount = $orderAmount;
            $model->disc = $discount;
            $model->receivable = $receivable;
            $model->plate_number = $result['Data']['plateno'];
            $model->calc_id = $result['Data']['calcid'];
            $model->quantity = $quantity;
            $model->effect_date = $effectDate;
            $model->expire_date = $expireDate;
            if($model->save())
            {
                ParkingLog::writeLog($this->user->id, $result, '创建临卡订单', 'gen', $plateno);
                return $this->renderJsonSuccess(['id' => $model->id]);
            }
        }

        return $this->renderJsonFail('未找到该车牌费用信息');
    }

    public function actionResult($message='未找到该车牌')
    {

        return $this->render('result', ['msg' => $message]);
    }

    public function actionPayView()
    {

        return $this->render('pay-view');
    }

    /**
     * 艾润道闸
     * @return string
     */
    public function actionIrainIndex()
    {
        if(empty($this->project->house_id)){
            return $this->render('result', [
                'msg' => '服务异常，请前去收费岗缴费！',
                'aChart' => '返回首页',
                'href' => '/'
            ]);
        }

        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);

        $memberPlate = MemberCar::find()->where(['member_id' => $this->user->id])->all();

        $viewData = [
            'parkingName' => $this->project->house_name,
            'parkingId' => $projectParkInfo->parking_id,
            'tempBillUrl' => '/parking/irain-temp-bill?',
            'monthBillUrl' => '/',
        ];

        return $this->render('irain', ['data' => $viewData, 'memberPlate' => $memberPlate]);
    }

    /**
     * 艾润道闸临卡费用接口（点击查询）
     * @param null $plateNo
     * @return string
     */
    public function actionIrainTempBill($plateNo=null)
    {
        $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);
        $postData = [
            'park_code' => $projectParkInfo->parking_id,
            'vpl_number' => $plateNo,
        ];

        $res = (new IRain($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingFeeTempCalcFee($postData);

        if(isset($res['data']) && is_array($res['data'])){
            //应缴金额（元）
            $unpaid = 0;
            if($res['data']['charge']['unpaid'] > 0){
                $unpaid = bcdiv($res['data']['charge']['unpaid'], 100, 2);
            }

            //在本次支付中车场所能提供的优惠费用 单位分
            $parkDiscount = 0;
            if(isset($res['data']['charge']['park_discount'])){
                if($res['data']['charge']['park_discount'] > 0){
                    $parkDiscount = bcdiv($res['data']['charge']['park_discount'], 100, 2);
                }
            }

            //停车时长
            $duration = self::getTimeDhm($res['data']['charge']['duration'], 's');

            //停车场名称
            $parkName = $res['data']['park']['name'];

            //入场时间
            $entryTime = $res['data']['in']['time'];

            $data = [
                'unpaid' => $unpaid,
                'parkName' => $parkName,
                'parkDiscount' => $parkDiscount,
                'duration' => $duration,
                'entryTime' => $entryTime,
                'number' => $plateNo,
            ];

            //记录日志
            ParkingLog::writeLog($this->user->id, $res, '获取临卡订单', 'irain', $plateNo);

            return $this->render('irain-temp-bill', ['data' => $data, 'payType' => 't']);
        }

        ParkingLog::writeLog($this->user->id, $postData, $res['message'], $res['code'], $plateNo);

        return $this->render('result', [
            'msg' => $res['tipsMsg'], 'aChart' => '返回', 'href' => '/parking/irain-index?'
        ]);
    }

    /**
     * 艾润临卡支付，创建本地订单
     * @return string
     */
    public function actionIrainCreateTempOrder()
    {
        if($this->isPost){
            $plateNo = $this->post('plateno');
            $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);
            $postData = [
                'park_code' => $projectParkInfo->parking_id,
                'vpl_number' => $plateNo,
            ];

            $res = (new IRain($projectParkInfo->app_id, $projectParkInfo->app_key))->getParkingFeeTempCalcFee($postData);

            ParkingLog::writeLog($this->user->id, $res, '创建临卡订单', 'irain', $plateNo);

            if(!isset($res['data'])){
                return $this->renderJsonFail($res['message']);
            }

            if($res['data']['charge']['unpaid'] < 1){
                return $this->renderJsonFail('无需支付');
            }

            //原停车费用，分转元
            $receivable = $res['data']['charge']['due'];
            if($receivable > 0){
                $receivable = bcdiv($receivable, 100, 2);
            }

            //当前的停车费，分转元
            $unpaid = bcdiv($res['data']['charge']['unpaid'], 100, 2);

            //在本次支付中车场所能提供的优惠费用 单位分
            $parkDiscount = 0;
            if(isset($res['data']['charge']['park_discount'])){
                if($res['data']['charge']['park_discount'] > 0){
                    $parkDiscount = bcdiv($res['data']['charge']['park_discount'], 100, 2);
                }
            }
            $effectDate = strtotime($res['data']['in']['time']);

            $model = new ParkingOrder();
            $model->member_id = $this->user->id;
            $model->type = 1;
            $model->parking_type = ParkingOrder::PARKING_TYPE_I;
            $model->project_house_id = $this->project->house_id;
            $model->amount = $unpaid;
            $model->disc = $parkDiscount;
            $model->receivable = $receivable;
            $model->plate_number = $plateNo;
            $model->calc_id = (string) $res['data']['bill_id'];
            $model->quantity = 1;
            $model->effect_date = $effectDate;
            $model->expire_date = 0;

            if($model->save()){
                return $this->renderJsonSuccess(['id' => $model->id]);
            }

            ParkingLog::writeLog($this->user->id, $model->getErrors(), $res['message'], $res['code']);

        }

        return $this->renderJsonFail('无法创建订单信息！');
    }

    /**
     * 获取项目停车缴费页面入口链接
     * @return string
     */
    public function actionGetUrl()
    {
        if($this->isAjax && isset($this->project->house_id)){
            $projectParkInfo = ProjectParkingOneToOne::findOne(['project_house_id' => $this->project->house_id]);
            $url = '/';
            switch ($projectParkInfo->type){
                case 2:
                    $url = '/parking/irain-index';
                    break;
                default:
                    $url = '/parking';
                    break;
            }

            return $this->renderJsonSuccess(['goUrl' => $url]);
        }

        return $this->renderJsonFail('');
    }

    /**
     * 删除会员车牌
     * @return string
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelPlate()
    {
        if($this->isGet){
            throw new NotFoundHttpException();
        }

        $id = $this->post('plaId', null);
        $model = MemberCar::findOne(['id' => $id, 'member_id' => $this->user->id]);
        if(!$model){
            return $this->renderJsonFail('无法删除，未找到对应信息');
        }

        $model->delete();

        return $this->renderJsonSuccess('');
    }

    /**
     * 获取时间： 天/小时/分
     * @param $minutes
     * @param $type
     * @return mixed
     */
    protected static function getTimeDhm($minutes, $type='m')
    {
        if(empty($minutes)){
            return $minutes;
        }

        switch ($type){
            case 's':
                $maxSecond = $minutes;
                break;
            default:
                $maxSecond = $minutes * 60;
                break;
        }

        $times = '';
        if($maxSecond >= 86400){
            $m = floor($maxSecond / 86400);
            $maxSecond = $maxSecond % 86400;
            $times = $m . '天';
        }
        if($maxSecond >= 3600){
            $m = floor($maxSecond / 3600);
            $maxSecond = $maxSecond % 3600;
            $times = $times . $m . '小时';
        }
        if($maxSecond >= 60){
            $m = floor($maxSecond / 60);
            $times = $times . $m . '分钟';
        }

        return $times;
    }

}