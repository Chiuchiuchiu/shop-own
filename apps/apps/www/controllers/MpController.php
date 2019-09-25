<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2017/11/28
 * Time: 14:04
 */

namespace apps\www\controllers;


use apps\www\models\Member;

class MpController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        return $this->redirect('/');
    }

    /**
     * 小程序
     * @author HQM 2018/11/22
     */
    public function actionMini()
    {
        $this->layout = 'mini';

        $nickname = $this->user->nickname;
        $unionid = $this->user->wechat_unionid;
        $tips = '授权失败';
        $style = 'fail';
        if(!empty($unionid)){
            $tips = '授权成功';
            $style = 'success';
        } else {
            $userInfo = $this->wechatInfo();
            if($userInfo instanceof Member){
                $tips = '授权成功';
                $style = 'success';
            }
        }

        return $this->render('mini', [
            'nickname' => $nickname, 'tips' => $tips, 'style' => $style
        ]);
    }

}