<?php

namespace apps\admin\controllers;

use common\models\Ad;
use Yii;
use yii\base\Object;
use yii\data\ActiveDataProvider;
use common\valueObject\RangDateTime;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use apps\admin\module\shopApi;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class AdController extends Controller
{
    private $adTemplate = [];
    /**
     * @inheritdoc
     */
    public function actionIndex($search=null,$id=null)
    {

        $w = null;
        if(!empty($search)){
            $w = "title like '%".$search."%'";
        }
        $dataProvider = new ActiveDataProvider([
            'query' => Ad::find()->where($w)->orderBy('id DESC')
        ]);

        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'search'=>$search,
        ]);
    }

    /**
     * @author dtfeng
     * @Date: 2019/8/26
     * @Time: 10:48
     * @description 模板列表
     */
    public function actionTemplate(){

        return $this->render('template');
    }
    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $projects = $this->projectCache();


        $projects = array_combine(array_column($projects, 'house_id'), $projects);

        //---------广告模板
        $adList = [];
        $ser = new shopApi();
        $result = $ser->getAdList();

        if($result['code'] == 200){
            $adList = $result['data'];
        }
        //---------

        $dateTime = (new RangDateTime())->autoLoad($this->get());
        $model = new Ad();
        if($this->isPost && $model->load(Yii::$app->request->post())){
            $_Post = Yii::$app->request->post();

            $_templateId = $_Post['Ad']['template_id'];
            $_type = $_Post['Ad']['type'];
            $_pic = $_Post['Ad']['pic'];
            $_url = $_Post['Ad']['url'];
            $_stime = $_Post["RangDateTime"]['startDate'];
            $_etime = $_Post["RangDateTime"]['endDate'];
            $_stime = $_stime ." 0:0:0";
            $_etime = $_etime ." 23:59:59";
            $_s_time = strtotime($_stime);
            $_e_time = strtotime($_etime);
            $model->start_time = $_s_time;
            $model->end_time = $_e_time;
            $model->projects = ',' . implode(',', Yii::$app->request->post()['Ad']['projects']) . ',';
            $diy_json = '';
         //   var_dump($_templateId);die;
            if(intval($_type) == 2){
                if(!empty($_templateId)){
                    foreach($adList as $k => $v){
                        if($v['id'] == $_templateId){
                            $diy_json = $v['layouts'];
                            break;
                        }
                    }
                }else{
                    $this->setFlashErrors($model->getErrors());
                }
            }else{
                if(empty($_pic)){
                    $this->setFlashErrors($model->getErrors());
                }

                if(empty($_url)){
                    $this->setFlashErrors($model->getErrors());
                }
            }
            $model->pic = $_pic;
            $model->url = $_url;

            $model->diy_json = $diy_json;
            $model->template_id = $_templateId;

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }

        $dateTime->startDate = date( 'Y-m-d', time());
        $_etime =  strtotime( "+1 week");
        $dateTime->endDate = date("Y-m-d",$_etime);
        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
            'dateTime'=> $dateTime,
            'adlist'=> $adList,
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $projects = $this->projectCache();
        $dateTime = (new RangDateTime())->autoLoad($this->get());

        $projects = array_combine(array_column($projects, 'house_id'), $projects);

        $model = $this->findModel($id);

        //---------广告模板
        $adList = [];
        $ser = new shopApi();
        $result = $ser->getAdList();

        if($result['code'] == 200){
            $adList = $result['data'];
            $this->adTemplate= $result['data'];
        }

        //---------
        if ($this->isPost && $model->load(Yii::$app->request->post())) {
            $_Post = Yii::$app->request->post();
            $_stime = $_Post["RangDateTime"]['startDate'];
            $_etime = $_Post["RangDateTime"]['endDate'];

            $_templateId = $_Post["Ad"]['template_id'];
            $_pic= $_Post["Ad"]['pic'];
            $_url = $_Post["Ad"]['url'];
            $_type = $_Post["Ad"]['type'];
            $diy_json = '';
            if(intval($_type) == 2 && !empty($_templateId)){
                foreach($adList as $k => $v){
                    if($v['id'] == $_templateId){
                        $diy_json = $v['layouts'];
                        break;
                    }
                }
            }else{
                if(empty($_pic)){
                    $this->setFlashErrors($model->getErrors());
                }

                if(empty($_url)){
                    $this->setFlashErrors($model->getErrors());
                }
            }

            $model->diy_json = $diy_json;
            $model->template_id = $_templateId;
            $_stime = $_stime ." 0:0:0";
            $_etime = $_etime ." 23:59:59";
            $_s_time = strtotime($_stime);
            $_e_time = strtotime($_etime);
            $model->pic = $_pic;
            $model->url = $_url;
            $model->projects = ',' . implode(',', Yii::$app->request->post()['Ad']['projects']) . ',';
            $model->start_time = $_s_time;
            $model->end_time = $_e_time;
            if ($model->save()) {
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }
        }
        $dateTime->startDate = date("Y-m-d",$model->start_time);
        $dateTime->endDate = date("Y-m-d",$model->end_time);

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
            'dateTime'=> $dateTime,
            'adlist'=> $adList,
        ]);
    }


    public function actionDelete($id)
    {
        $model = Ad::findOne($id);
        if(isset($model)){
            $model->delete();
        }
        return $this->backRedirect(['index']);
    }


    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ad::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreateUrlAjax(){

        $post = $this->post();

        if(empty($post['params'])) return $this->renderJsonFail('参数缺失');
        if(!is_numeric($post['type_num'])) return $this->renderJsonFail('类型缺失');

        $url = '';

        switch ($post['type_num']){
            case Ad::TYPE_1:
                $url = sprintf(Ad::typeMap()[$post['type_num']]['url'], trim($post['params'], "|"));
                break;

            case Ad::TYPE_2:
                $params = explode('|', $post['params']);
                $url = sprintf(Ad::typeMap()[$post['type_num']]['url'], $params[0], $params[1]);
                break;
        }

        return $this->renderJsonSuccess(['url' => $url]);
    }
}
