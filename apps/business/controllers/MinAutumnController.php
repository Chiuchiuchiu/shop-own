<?php namespace apps\business\controllers;

use apps\business\models\Member;
use common\models\House;
use common\models\MinAutumnQuestion;
use common\models\MinAutumnRedPack;
use common\valueObject\RangDateTime;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * 中秋答题活动
 * Class MinAutumnController
 * @package apps\www\controllers
 */
class MinAutumnController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * 业主答题记录
     * @author zhaowenxi
     */
    public function actionIndex()
    {
        $projectId = $this->get('house_id', null);
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = House::find()->select('project_house_id as house_id, house_name')
            ->where(['parent_id' => 0])
            ->asArray()
            ->orderBy('house_name')
            ->all();

        $projectsArray = [];
        $projectsArray[''] = '全部';
        $projectsArray['项目列表'] = ArrayHelper::map($projects, 'house_id', 'house_name');

        $redPackAmount = MinAutumnRedPack::find()
            ->andFilterWhere(['project_id' => $projectId])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->sum('amount');

        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = MinAutumnRedPack::find()
            ->andFilterWhere(['project_id' => $projectId])
            ->andFilterWhere(['BETWEEN', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()])
            ->orderBy('created_at DESC');

        $dataProvider->setSort(false);

        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'house_id' => $projectId,
                'projectsArray' => $projectsArray,
                'dateTime' => $dateTime,
                'redPackAmount' => $redPackAmount / 100,    //单位：元
            ]
        );
    }

    /**
     * 答题列表
     * @param null $search
     * @return string
     * @author zhaowenxi
     */
    public function actionAnswerList($search = null)
    {

        $dataProvider = new ActiveDataProvider();

        $dataProvider->query = MinAutumnQuestion::find()
            ->andFilterWhere(['LIKE', 'title', $search])
            ->orderBy('id DESC');

        $dataProvider->setSort(false);

        return $this->render('answer-list', ['dataProvider' => $dataProvider, 'search' => $search]);
    }


    public function actionCreate(){

        $model = new MinAutumnQuestion();

        if($this->isPost && $model->load(\Yii::$app->request->post())){

            $answerArr  = explode('|', \Yii::$app->request->post("MinAutumnQuestion")['answer']);
            $answer     = json_encode($answerArr);
            $answerTrue = \Yii::$app->request->post('MinAutumnQuestion')['answer_true'] - 1;

            $model->answer      = $answer;
            $model->answer_true = $answerTrue;
            $model->title       = \Yii::$app->request->post('MinAutumnQuestion')['title'];
            $model->status      = MinAutumnQuestion::STATUS_SUCCESS;
            $model->created_at  = time();
            $model->updated_at  = time();

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['answer-list']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }
        return $this->render('create', [
            'model' => $model,
            'data' => (object)[],
        ]);

    }

    /**
     * 修改题目状态
     * @param $id
     * @param $status
     * @return \yii\web\Response
     * @author zhaowenxi
     */
    public function actionSetStatus($id, $status){

        $setStatus = $status == 1 ? 2 : 1;
        $res = MinAutumnQuestion::findOne($id);
        $res->status = $setStatus;
        $res->save();

        return $this->renderJsonSuccess([]);
    }

    public function actionUpdate($id){

        $data = MinAutumnQuestion::findOne($id);

        $model = new MinAutumnQuestion();

        if($this->isPost && $data->load(\Yii::$app->request->post())){

            $answerArr = explode('|', \Yii::$app->request->post("MinAutumnQuestion")['answer']);
            $answer = json_encode($answerArr);
            $answerTrue = \Yii::$app->request->post('MinAutumnQuestion')['answer_true'] - 1;

            $data->answer      = $answer;
            $data->answer_true = $answerTrue;
            $data->title       = \Yii::$app->request->post('MinAutumnQuestion')['title'];
            $data->updated_at  = time();

            if($data->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['answer-list']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }

        return $this->render('create', ['model' => $model, 'data' => $data]);
    }

    public function actionExport($house_id = 0){

        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $model = MinAutumnRedPack::find()
            ->andFilterWhere(['between', 'created_at', $dateTime->getStartTime(), $dateTime->getEndTime()]);

        if($house_id){
            $model->andFilterWhere(['project_id' => $house_id]);
        }

        $data = $model->orderBy('id')->all();

        $fileName = '中秋答题' . $dateTime->getStartDate().'至'.$dateTime->getEndDate().'.xlsx';

        //先查出所有关联数据，不要在循环里面查
        $houseIds = ArrayHelper::getColumn($data, 'house_id');
        $userIds = ArrayHelper::getColumn($data, 'member_id');

        $houseInfo = House::find()->where(['IN', 'house_id', $houseIds])
            ->select('ancestor_name, house_id')
            ->asArray()
            ->all();
        $userInfo = Member::find()->where(['IN', 'id', $userIds])
            ->select('nickname, id, phone')
            ->asArray()
            ->all();

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Document")
            ->setSubject("Office 2007 XLSX Document")
            ->setKeywords("office 2007 openxml php");

        $objPHPExcel->getActiveSheet()->setTitle($fileName);
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '#')
            ->setCellValue('B1', '手机号')
            ->setCellValue('C1', '用户')
            ->setCellValue('D1', '住址')
            ->setCellValue('E1', '红包金额（元）')
            ->setCellValue('F1', '微信订单号')
            ->setCellValue('G1', '状态')
            ->setCellValue('H1', '创建时间');

        $i=2;
        foreach ($data as $v){

            foreach ($userInfo as $uval){

                $nickname = $phone = "无";

                if($v->member_id == $uval['id']){
                    $phone    = $uval['phone'];
                    $nickname = $uval['nickname'];
                    break;
                }
            }

            foreach ($houseInfo as $hval){

                $ancestor_name = "无";

                if($v->house_id == $hval['house_id']){
                    $ancestor_name = $hval['ancestor_name'];
                    break;
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, ' '.$v->id)
                ->setCellValue('B'.$i, $phone)
                ->setCellValue('C'.$i, ' '.$nickname)
                ->setCellValue('D'.$i, $ancestor_name)
                ->setCellValue('E'.$i, $v->amount / 100)
                ->setCellValue('F'.$i, ' '.$v->wechat_mch_id)
                ->setCellValue('G'.$i, MinAutumnRedPack::statusType()[$v->status])
                ->setCellValue('H'.$i, date("Y-m-d H:i:s", $v->created_at));
            $i++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . mb_convert_encoding($fileName, 'GBK', 'UTF8'));
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}