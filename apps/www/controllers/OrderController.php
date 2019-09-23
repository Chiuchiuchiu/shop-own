<?php
/**
 * Created by
 * Author: zhao
 * Time: 2016/12/1 16:40
 * Description:
 */

namespace apps\www\controllers;


use apps\mgt\models\FpzzLog;
use common\models\PmOrder;
use common\models\PrepayPmOrder;
use yii\data\ActiveDataProvider;

class OrderController extends Controller
{
    public function actionPmList()
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrder::find()
            ->where([
                'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED, PmOrder::STATUS_REFUND],
                'member_id' => $this->user->id
            ])
            ->orderBy('created_at DESC');
        if ($this->isAjax && $this->get('page')) {
            return $this->renderPartial('pm-list-cell', ['dataProvider' => $dataProvider]);
        } else {
            return $this->render('pm-list', [
                'dataProvider' => $dataProvider
            ]);
        }
    }

//    public function actionPrepayPmList()
//    {
//        $dataProvider = new ActiveDataProvider();
//        $dataProvider->query = PrepayPmOrder::find()
//            ->where([
//                'status' => [PrepayPmOrder::STATUS_PAYED, PrepayPmOrder::STATUS_TEST_PAYED],
//                'member_id' => $this->user->id
//            ])
//            ->orderBy('created_at DESC');
//        if ($this->isAjax && $this->get('page')) {
//            return $this->renderPartial('prepay-pm-list-cell', ['dataProvider' => $dataProvider]);
//        } else {
//            return $this->render('prepay-pm-list', [
//                'dataProvider' => $dataProvider
//            ]);
//        }
//    }

    public function actionPmView($id = null)
    {
        $model = PmOrder::find()
            ->where([
                'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED, PmOrder::STATUS_REFUND],
                'member_id' => $this->user->id,
                'id' => $id
            ])
            ->one();

        return $this->render('pm-view', [
            'model' => $model,
            'gift' => PmOrder::find()
                    ->joinWith('house')
                    ->where([
                        'house.project_house_id' => 286855,//230975,
                        'member_id' => $this->user->id,
                        'status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED]
                    ])->count() > 0
        ]);
    }

    public function actionPrepayPmView($id)
    {
        $model = PrepayPmOrder::find()
            ->where([
                'status' => [PrepayPmOrder::STATUS_PAYED, PrepayPmOrder::STATUS_TEST_PAYED],
                'member_id' => $this->user->id,
                'id' => $id
            ])
            ->one();
        return $this->render('prepay-pm-view', [
            'model' => $model
        ]);
    }

    public function actionSuccess(){
        return $this->render("success");
    }

    /**
     * @param integer $pmOrderFpzzId
     * @param string|array $postData
     * @param string $result
     */
    protected function writeFpzzLog($pmOrderFpzzId, $postData, $result)
    {
        $fpzzLog = new FpzzLog();
        $fpzzLog->pm_order_fpzz_id = $pmOrderFpzzId;
        $fpzzLog->post_data = serialize($postData);
        $fpzzLog->result = serialize($result);
        $fpzzLog->fp_cached_id = isset($result['object']['id']) ? $result['object']['id'] : '';
        $fpzzLog->save();
    }

}