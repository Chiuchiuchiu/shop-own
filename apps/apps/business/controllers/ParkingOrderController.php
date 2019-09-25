<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/23
 * Time: 15:49
 */

namespace apps\business\controllers;


use common\models\House;
use common\models\ParkingOrder;
use common\models\ProjectParkingOneToOne;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ParkingOrderController extends Controller
{
    public function actionIndex($status=null)
    {
        $whereStatus = ParkingOrder::STATUS_PAYED;
        switch($status){
            case '0':
                $whereStatus = ParkingOrder::STATUS_PAYED;
                break;
            case '1':
                $whereStatus = ParkingOrder::STATUS_REFUND;;
                break;
        }

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $plateNumber = $this->get('plate-number');

        $Amount = ParkingOrder::find()
            ->andFilterWhere(['status' => $whereStatus])
            ->andFilterWhere(['BETWEEN', 'payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['like', 'plate_number', $plateNumber])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->sum('amount');

        $projects = $this->projectCache();
        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ParkingOrder::find()
            ->andFilterWhere(['status' => $whereStatus])
            ->andFilterWhere(['BETWEEN', 'payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['like', 'plate_number', $plateNumber])
            ->orderBy('payed_at DESC');
        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider'=>$dataProvider,
            'dateTime'=>$dateTime,
            'house_id' => $house_id,
            'Amount' => $Amount,
            'status' => $status,
            'projects' => $projectsArray,
            'plateNumber' => $plateNumber,
        ]);
    }

    public function actionUpdate()
    {
        if($this->isAjax){
            $id = intval($this->post('id'));
            if(!empty($id)){
                $model = ParkingOrder::findOne($id);
                $model->status = ParkingOrder::STATUS_REFUND;
                $model->m_id = $this->user->id;

                if($model->save()){
                    return $this->renderJsonSuccess(['message' => '退款成功']);
                }
            }
        }

        return $this->renderJsonFail('退款失败');
    }

    public function actionExport($status=null)
    {
        $whereStatus = ParkingOrder::STATUS_PAYED;
        switch($status){
            case '0':
                $whereStatus = ParkingOrder::STATUS_PAYED;
                break;
            case '1':
                $whereStatus = ParkingOrder::STATUS_REFUND;;
                break;
        }

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $plateNumber = $this->get('plate-number');

        $rs = ParkingOrder::find()
            ->andFilterWhere(['status' => $whereStatus])
            ->andFilterWhere(['BETWEEN', 'payed_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->andFilterWhere(['like', 'plate_number', $plateNumber])
            ->orderBy('payed_at DESC');

        $projectName = '当前数据报表--';

        $fileName = $projectName . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'账单.csv';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        $str="姓名（昵称）,手机号,车牌,项目,财到家订单号,道闸订单号,类型,量（月）,缴费开始日期,缴费结束日期,状态,金额,支付时间\n";
        echo mb_convert_encoding($str,'GBK','UTF8');

        foreach($rs->each() as $row){
            /**
             * @var $row ParkingOrder
             */
            $quantity = $row->type == ParkingOrder::TYPE_T ? '-' : $row->quantity;
            $effectDate = $row->effect_date > 0 ? date('Y-m-d', $row->effect_date) : '-';
            $expireDate = $row->expire_date > 0 ? date('Y-m-d', $row->expire_date) : '-';

            $str = implode(',',[
                    $row->member->showName,
                    $row->member->phone,
                    $row->plate_number,
                    $row->project->house_name,
                    $row->number,
                    $row->calc_id,
                    $row->typeText,
                    $quantity,
                    $effectDate,
                    $expireDate,
                    $row->statusText,
                    number_format($row->amount,2,'.',''),
                    date('Y-m-d H:i:s', $row->payed_at)])."\n";

            echo mb_convert_encoding($str,'GBK','UTF8');
        }
        die();
    }

    public function actionProject($projectId=null)
    {
        $projects =$this->projectCache();
        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');
        $pDomain = \Yii::$app->params['domain.p'];

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = ProjectParkingOneToOne::find()
            ->andFilterWhere(['project_house_id' => $projectId])
            ->orderBy("id DESC");
        $dataProvider->setSort(false);

        return $this->render('project', [
            'dataProvider' => $dataProvider,
            'projects' => $projectsArray,
            'projectId' => $projectId,
            'pDomain' => $pDomain,
        ]);
    }

    /**
     * 统计项目订单数
     * @return false|string
     */
    public function actionProjectOrderC()
    {
        $projectIds = $this->get('projectIds');
        $lists = [];
        if($projectIds){
            $projectIds = explode(',', $projectIds);

            foreach ($projectIds as $ids){
                $lists[] = [
                    'house_id' => $ids,
                    'c' => ParkingOrder::find()->where(['project_house_id' => $ids, 'status' => ParkingOrder::STATUS_PAYED])->count(),
                ];
            }
        }

        return $this->renderJsonSuccess($lists);
    }

    public function actionCreate()
    {
        $model = new ProjectParkingOneToOne();
        $projectList = $this->projectCache();
        $projectList = ArrayHelper::map($projectList, 'house_id', 'house_name');
        $_post = $this->post();


        if($this->isPost && $model->load($this->post())){
            $_type = $_post["ProjectParkingOneToOne"]['type'];
            $_name = $_post["ProjectParkingOneToOne"]['name'];
            $_icon = $_post["ProjectParkingOneToOne"]['pic'];

            $model['type'] = $_type;
            $model['name'] = $_name;
            $model['pic'] = $_icon;

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect('project');
            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }


        return $this->render('create', ['model' => $model, 'projectList' => $projectList]);
    }

    public function actionDelete($id){

        $vo = ProjectParkingOneToOne::findOne($id);
        if($vo !== null){
            $res = $vo->delete();
        }
        return $this->backRedirect(['project']);
    }


    protected function findModel($id)
    {
        if (($model = ParkingOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}