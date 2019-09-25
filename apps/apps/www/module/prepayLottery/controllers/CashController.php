<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/14 10:43
 * Description:
 */

namespace apps\www\module\prepayLottery\controllers;


use apps\www\module\prepayLottery\models\PrepayLotteryResult;
use yii\web\Controller;

class CashController extends Controller
{
    private $userinfo = [
        'zhongao' => '123456',
    ];

    public function actionIndex($id, $memberId)
    {
        $this->layout = 'main';

        if (!\Yii::$app->session->get('isCashLogin') && $this->route !== 'prepay-lottery/cash/login') {
            return $this->redirect(['login']);
        }
        $result = PrepayLotteryResult::findOne(['id' => $id, 'member_id' => $memberId]);
        if ($result) {
            if ($result->gave_at < 1) {
                $result->gave_at = time();
                $result->manager = \Yii::$app->session->get('isCashLogin');
                if ($result->save()) {
                    return $this->render('index',
                        ['msg' => '验证成功,请发放礼品','code'=>1]
                    );
                } else {
                    return $this->render('index',
                        ['msg' => '系统错误登记号码:'.$id,'code'=>2]
                    );
                }
            }else{
                return $this->render('index',
                    ['msg' => '该礼品已经领取<br />['.date('Y-m-d H:i:s',$result->gave_at).'] 操作人:'.$result->manager
                        ,'code'=>0]
                );
            }
        }
        return $this->render('index',
            ['msg' => '无效验证码','code'=>0]
        );

    }

    public function actionLogin()
    {
        $this->layout = 'main';
        $msg = '';
        if (\Yii::$app->request->isPost) {
            $msg = '登录失败';
            if (isset($this->userinfo[\Yii::$app->request->post('username')])
                &&
                $this->userinfo[\Yii::$app->request->post('username')] == \Yii::$app->request->post('password')
            ) {
                \Yii::$app->session->set('isCashLogin', \Yii::$app->request->post('username'));
                $msg = '登录成功,请重新扫码';
            }

        }
        return $this->render('login', ['msg' => $msg]);
    }

}