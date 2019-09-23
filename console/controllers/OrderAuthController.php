<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/2 16:20
 * Description:
 */

namespace console\controllers;


use common\models\House;
use common\models\HouseBillOutline;
use common\models\PmOrder;
use common\models\PmOrderAuditing;
use components\newWindow\NewWindow;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class OrderAuthController extends Controller
{
    public function actionIndex($date = null)
    {
        if ($date === null) {
            $date = date('Ymd', time() - 86400);
        }
        $time = strtotime($date . ' 00:00:00');
        $ids = PmOrder::find()->where(['status' => PmOrder::STATUS_PAYED])
            ->andWhere(['BETWEEN', 'payed_at', $time, $time + 86400])->select('id')->all();
        $ids = ArrayHelper::getColumn($ids, 'id');
        if ($ids) {
            $model = new PmOrderAuditing();
            $model->date = $date;
            $model->pm_order_ids = implode(',', $ids);
            if($model->save()){
                $this->stdout("success\n");
                return true;
            }
        }
    }
}