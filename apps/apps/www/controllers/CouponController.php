<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/6/9
 * Time: 16:35
 */

namespace apps\www\controllers;


use common\models\MemberPromotionCode;
use yii\data\ActiveDataProvider;

class CouponController extends Controller
{
    public function actionIndex()
    {
        $authActivities = \Yii::$app->params['christmas_activities'];

        //认证房产红包最大使用时间
        $chCodeAllowedMaxTime = date('Y-m-d', $authActivities['allowedMaxTime']);

        $memberPromotionCodeToXg = [];
        $memberPromotionCodeCh = MemberPromotionCode::find()->where(['member_id' => $this->user->id])->all();

        return $this->render('index', [
            'xgCode' => $memberPromotionCodeToXg,
            'chCode' => $memberPromotionCodeCh,
            'chCodeAllowedMaxTime' => $chCodeAllowedMaxTime,
        ]);
    }
}