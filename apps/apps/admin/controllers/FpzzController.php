<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/27
 * Time: 10:50
 */

namespace apps\admin\controllers;


use apps\admin\models\FpzzLog;
use apps\pm\models\ProjectFpzzAccount;
use common\models\FpzzFeedback;
use common\models\PmOrder;
use common\models\PmOrderFpzz;
use common\models\PmOrderFpzzItem;
use common\models\PmOrderItem;
use common\models\PmOrderNewwindowPdf;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class FpzzController extends Controller
{
    public function actionIndex()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $houseId = $this->get('house_id');
        $memberName = trim($this->get('member-name', ''));
        $email = trim($this->get('email', ''));
        $status = trim($this->get('status', ''));

        $projects = $this->projectCache();
        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');
        $paperInvoiceCount = PmOrderFpzz::find()->where(['type' => PmOrderFpzz::TYPE_P, 'show_status' => PmOrderFpzz::SHOW_STATUS_ACTIVE])->count();

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderFpzz::find()
            ->where(['type' => PmOrderFpzz::TYPE_E, 'show_status' => PmOrderFpzz::SHOW_STATUS_ACTIVE])
            ->andFilterWhere(['project_house_id' => $houseId])
            ->andFilterWhere(['email' => $email, 'user_name' => $memberName])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()]);

        $status && $dataProvider->query->andFilterWhere([$status == 1 ? '=' : '<>' ,'status',PmOrderFpzz::STATUS_SUCCESS]);

        $dataProvider->query->orderBy('id DESC');
        
        $dataProvider->setSort(false);

        return $this->render('index',
            [
                'dataProvider' => $dataProvider,
                'projects' => $projectsArray,
                'house_id' => $houseId,
                'dateTime' => $dateTime,
                'memberName' => $memberName,
                'email' => $email,
                'status' => $status,
                'paperInvoiceCount' => $paperInvoiceCount,
            ]
        );
    }

    /**
     * 纸质发票
     * @param null $type
     * @return string
     */
    public function actionPaper($type=null)
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $house_id = $this->get('house_id');
        $showStatus = $this->get('show-status', 1);
        $memberName = trim($this->get('member-name', ''));
        $email = trim($this->get('email', ''));

        $projects = $this->projectCache();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderFpzz::find()
            ->where(['show_status' => PmOrderFpzz::SHOW_STATUS_ACTIVE, 'type' => PmOrderFpzz::TYPE_P])
            ->andWhere(['show_status' => $showStatus])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->andFilterWhere(['like', 'user_name', $memberName])
            ->andFilterWhere(['like', 'email', $email])
            ->andFilterWhere(['project_house_id' => $house_id])
            ->orderBy('created_at DESC');
        $dataProvider->setSort(false);

        return $this->render('paper',
            [
                'dataProvider' => $dataProvider,
                'projects' => $projectsArray,
                'house_id' => $house_id,
                'type' => $type,
                'dateTime' => $dateTime,
                'memberName' => $memberName,
                'email' => $email,
            ]
        );
    }

    public function actionShowItem($id=null)
    {
        $pmOrderFpzz = PmOrderFpzz::findOne($id);

        $model = PmOrderFpzzItem::findOne(['pm_order_fpzz_id' => $id, 'status' => 0]);
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderFpzzItem::find()->where(['pm_order_fpzz_id' => $id])->orderBy('status DESC');
        $dataProvider->setSort(false);

        return $this->render('show-item', get_defined_vars());
    }

    /**
     * 展示电子发票
     * @param null $id
     * @return string
     */
    public function actionShowFped($id=null)
    {
        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderFpzzItem::find()
            ->where(['pm_order_fpzz_id' => $id]);
        $dataProvider->setSort(false);

        $pdfList = PmOrderNewwindowPdf::find()
            ->select('bill_pdf_url')
            ->where(['pm_order_fpzz_id' => $id])
            ->asArray()->all();

        return $this->render('show-fped', [
            'dataProvider' => $dataProvider,
            'pdfList' => $pdfList,
        ]);
    }

    public function actionReject($id=null)
    {
        $model = PmOrderFpzz::findOne($id);
        if(!$model){
            throw new NotFoundHttpException();
        }

        $model->status = 1;
        $model->save();

        $this->setFlashSuccess();
        return $this->backRedirect(['index']);
    }

    public function actionDetail($id=null)
    {
        $model = PmOrderFpzz::findOne($id);
        if(!$model){
            throw new NotFoundHttpException();
        }

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = PmOrderItem::find()->joinWith('pmOrder')
            ->where(
                [
                    'pm_order.status' => [PmOrder::STATUS_PAYED, PmOrder::STATUS_TEST_PAYED],
                    'pm_order_item.pm_order_id' => $model->pm_order_id
                ]
            )
            ->orderBy('payed_at DESC');

        $dataProvider->setSort(false);
        return $this->render('detail', get_defined_vars());
    }

    /**
     * 保存备注|更新纸质发票状态
     */
    public function actionSaveRemarks()
    {
        if($this->isAjax && $this->isPost){
            $fpId = $this->post('fp-id');
            $remarks = $this->post('remarks');

            $model = PmOrderFpzz::findOne(['id' => $fpId]);
            if($model){
                $model->remarks = trim($remarks);
                $model->status = PmOrderFpzz::STATUS_P_ACTIVE;
                $model->save();

                return $this->renderJsonSuccess('');
            }
        }

        return $this->renderJsonFail('提交失败');
    }

    /**
     * 作废电子发票
     * @return string
     */
    public function actionCancel()
    {
        return $this->renderJsonFail('未提供该功能！');
    }

    /**
     * 开票失败反馈
     */
    public function actionFeedback()
    {
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $dataProvider = new ActiveDataProvider();
        $dataProvider->query = FpzzFeedback::find()
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('id DESC');
        $dataProvider->setSort(false);

        return $this->render('feedback', get_defined_vars());
    }

    /**
     * 查找开票错误原因
     * @param $pmOrderId
     * @return false|string
     */
    public function actionCause($pmOrderId)
    {
        $fpzzLog = FpzzLog::find()->where(['pm_order_id' => $pmOrderId])->orderBy('id DESC')->one();
        if(!$fpzzLog){
            return $this->renderJsonSuccess(['message' => '未找到相关记录']);
        }

        $logRes = unserialize($fpzzLog->result);
        if(empty($logRes)){
            return $this->renderJsonSuccess(['message' => '请截图反馈技术处理']);
        }

        $code = $logRes['Response']['Data']['NWRespCode'];
        $message = $logRes['Response']['Data']['NWErrMsg'];
        $errorMessage = 'Code：【'.$code.'】；'.'Message：'.$message;
        return $this->renderJsonSuccess(['message' => $errorMessage]);
    }

//    /**
//     * 项目列表
//     * @author HQM 2019/02/15
//     * @return string
//     */
    /**
     * @author dtfeng
     * @Date: 2019/4/4
     * @Time: 8:34
     * @description
     * @return string
     */
    public function actionProject()
    {
        $houseId = $this->get('house_id', null);
        $project = $this->projectCache();
        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($project, 'house_id', 'house_name');

      //  var_dump($projectsArray);die;

        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = ProjectFpzzAccount::find()->andFilterWhere(['project_house_id' => $houseId])->orderBy('id DESC');
        $dataProvider->setSort(false);
        $_data = array(
            'house_id'     => $houseId,
            'projects'     => $projectsArray,
            'dataProvider' => $dataProvider,
        );

       // var_dump($_data);die;

        return $this->render('project', $_data);
    }

    /**
     * 编辑项目
     * @param $dataId
     * @return string
     */
    public function actionEditProject($dataId)
    {
        $this->layout = false;
        $model = ProjectFpzzAccount::findOne(['id' => $dataId]);
        $status = $model->status;
        $csrf = \Yii::$app->request->getCsrfToken();

        return $this->render('edit-project', [
            'model' => $model,
            'status' => $status,
            'dataId' => $dataId,
            'csrf' => $csrf
        ]);
    }

    /**
     * 创建电子发票
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionCreate()
    {
        $model = new ProjectFpzzAccount();

        $projects = $this->projectCache();
        $projectsArray = [];
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $post = $this->post();

        if($model->load($post)){

            if(ProjectFpzzAccount::findOne(['project_house_id' => $model->project_house_id])){
                $this->setFlashErrors(['项目已存在！']);
                return $this->render('create', [
                    'model' => $model,
                    'projectsArray' => $projectsArray,
                ]);
            }

            if($model->save()){

                $this->setFlashSuccess();
                $this->backRedirect();

            }else{
                $this->setFlashError($model->getErrors());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'projectsArray' => $projectsArray,
        ]);
    }

    public function actionUpdate()
    {
        if($this->isPost){
            $id = $this->post('id', null);
            $status = $this->post('status', '');
            $tips = $this->post('tips', null);

            $model = ProjectFpzzAccount::findOne(['id' => $id]);
            if(!$model){
                return $this->renderJsonFail('error');
            }
            if($status != ''){
                $model->status = $status;
            }

            $model->tips = trim($tips);
            if($model->save()){
                return $this->renderJsonSuccess('');
            }

            return $this->renderJsonFail('update error');
        }

        return $this->renderJsonFail('error');
    }

}