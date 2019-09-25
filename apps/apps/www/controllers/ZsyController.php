<?php
/**
 * Created by
 * Author: zhao
 * Time: 2017/1/11 18:24
 * Description:
 */

namespace apps\www\controllers;


use components\zsy\ZSYSDK;

class ZsyController extends Controller
{
    /**
     * @var $zsyMember
     */
    private $zsyMember = null;


    public function beforeAction($action)
    {
//        if (empty($this->user->phone)) {
//            $this->redirect('auth/mobile?');
//            return false;
//        }
        return parent::beforeAction($action);
    }


    /**
     *
     * Description:
     */
    public function actionDeposit()
    {
        $zsy = new ZSYSDK();
        $res = $zsy->deposit([
//            'amount'=>100,
            'frontNotifyUrl' => '' . \Yii::$app->request->hostInfo . '/zsy/deposit-notify',
            'notifyUrl' => '' . \Yii::$app->request->hostInfo . '/zsy/deposit-background-notify',
            'loginName' => md5($this->user->id . ''),
            'outCustomerId' => crc32(md5($this->user->id . 'zsy-cdj-good')),
            'outTradeNo' => 'dsc' . time(),
        ]);
        if ($res['code'] === 1) {
            return $this->redirect($res['data']['resultInfo']['widgetPageUrl']);
        }
    }

    public function actionWithdraw()
    {
        $zsy = new ZSYSDK();
        $res = $zsy->withdraw([
            'frontNotifyUrl' => '' . \Yii::$app->request->hostInfo . '/zsy/withdraw-notify',
            'notifyUrl' => '' . \Yii::$app->request->hostInfo . '/zsy/withdraw-background-notify',
            'loginName' => md5($this->user->id . ''),
            'outCustomerId' => crc32(md5($this->user->id . 'zsy-cdj-good')),
            'outTradeNo' => 'id' . time(),
        ]);
        if ($res['code'] === 1) {
            return $this->redirect($res['data']['resultInfo']['widgetPageUrl']);
        }
    }

    public function actionWithdrawNotify()
    {
        $this->redirect('/member');
    }

    public function actionDepositNotify()
    {
        $this->redirect('/member');
    }

    public function actionRecord()
    {
        $zsy = new ZSYSDK();
        $res = $zsy->transDetail([
            'frontNotifyUrl' => '' . \Yii::$app->request->hostInfo . '/zsy/',
            'notifyUrl' => '' . \Yii::$app->request->hostInfo . '/zsy/',
            'loginName' => md5($this->user->id . ''),
            'outCustomerId' => crc32(md5($this->user->id . 'zsy-cdj-good')),
        ]);
        if ($res['code'] === 1) {
            return $this->redirect($res['data']['resultInfo']['widgetPageUrl']);
        }
    }

    public function actionAssets()
    {
        $zsy = new ZSYSDK();
        $res = $zsy->myAssets([
            'loginName' => md5($this->user->id . ''),
            'outCustomerId' => crc32(md5($this->user->id . 'zsy-cdj-good')),
        ]);
        if ($res['code'] === 1) {
            return $this->redirect($res['data']['resultInfo']['widgetPageUrl']);
        }
    }

}