<?php
/**
 * Created by
 * Author: zhaowenxi
 * Description:
 */

namespace apps\admin\controllers;


use common\models\Project;
use common\models\ProjectServiceCategory;
use common\models\ProjectServicePhone;
use apps\admin\valueObject\FileCache;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ServiceController extends Controller
{
    /**
     * 常用电话列表
     * @param null $id 项目id
     * @param null $status
     * @return string
     * @author zhaowenxi
     */
    public function actionTelephone($id = null, $status = null){

        //分公司
        $projectSelect[''] = '全部';
        $projects = $this->projectsCache();
        $projects = ArrayHelper::map($projects, 'house_id', 'house_name');

        $projectSelect['项目列表'] = $projects;

        $dataProvider = new ActiveDataProvider([
            'query' => ProjectServicePhone::find()->filterWhere(['project_house_id' => $id])
                ->filterWhere(['status' => $status])
                ->orderBy('id DESC, project_house_id ASC'),
        ]);
        $dataProvider->setSort(false);

        return $this->render('telephone', get_defined_vars());
    }

    /**
     * 关闭/启用一条常用电话
     * @param $id
     * @param $status
     * @param $type 1电话2分类
     * @return \yii\web\Response
     * @author zhaowenxi
     */
    public function actionSetStatus($id, $status, $type){

        $setStatus = $status == 1 ? 0 : 1;

        switch ($type){
            case 1 : $res = ProjectServicePhone::findOne($id);break;
            case 2 : $res = ProjectServiceCategory::findOne($id);break;
            default : $res = ProjectServicePhone::findOne($id);break;
        }

        $res->status = $setStatus;

        return $res->save() ? $this->renderJsonSuccess([]) : $this->renderJsonFail('修改失败');
    }

    /**
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionCreate()
    {
        $model = new ProjectServicePhone();

        $projects = $this->projectsCache();

        $projectList = ArrayHelper::merge([''=>'请选择',0=>'全部'], ArrayHelper::map($projects, 'house_id', 'house_name'));

        if($this->isPost && $model->load(\Yii::$app->request->post())){

            $model->category_id = \Yii::$app->request->post()['ProjectServicePhone']['category_id'];

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['index']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }
        return $this->render('create', [
            'model' => $model,
            'projectList' => $projectList,
        ]);
    }

    /**
     * @author HQM 2019/02/19
     * @return string|\yii\web\Response
     */
    public function actionCreateCategory()
    {
        $model = new ProjectServiceCategory();
        $project = $this->projectCache();
        $projectsArray = ArrayHelper::map($project, 'house_id', 'house_name');

        if($this->isPost && $model->load(\Yii::$app->request->post())){
            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['category']);
            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }

        return $this->render('create-category', [
            'model' => $model,
            'projectList' => $projectsArray,
        ]);
    }

    /**
     * 通过项目id获取所属分类
     * @param $id
     * @return string
     * @author zhaowenxi
     */
    public function actionGetCategorise($id){

        $categoryList = [];

        $categorise = ProjectServiceCategory::find()->select("id,name")
            ->where(['project_house_id' => $id,'status' => ProjectServiceCategory::STATUS_ACTIVE])->all();

        $categorise && $categoryList = ArrayHelper::toArray($categorise);

        return $this->renderJsonSuccess($categoryList);

    }

    /**
     * 编辑&详情
     * @param $id
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionUpdate($id)
    {
        $model = ProjectServicePhone::findOne($id);

        $projects = $this->projectsCache();

        $projectInfo = ArrayHelper::merge([''=>'请选择',0=>'全部'], ArrayHelper::map($projects, 'house_id', 'house_name'));

        $categories = ProjectServiceCategory::find()->where(['project_house_id' => $model->project_house_id])->all();

        $categoryInfo = ArrayHelper::map($categories, 'id', 'name');

        if ($this->isPost) {
            if ($model->load($this->post())) {

                $model->category_id = $this->post('ProjectServicePhone')['category_id'];

                if($model->save()){

                    $this->setFlashSuccess();

                    return $this->backRedirect();
                }

            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'projectInfo' => $projectInfo,
            'categoryInfo' => $categoryInfo]);
    }

    /**
     * 便民电话分类
     * @param null $id
     * @param null $status
     * @return string
     * @author zhaowenxi
     */
    public function actionCategory($id = null, $status = null){

        //分公司
        $projectSelect[''] = '全部';
        $projects = $this->projectsCache();
        $projects = ArrayHelper::map($projects, 'house_id', 'house_name');

        $projectSelect['项目列表'] = $projects;

        $dataProvider = new ActiveDataProvider([
            'query' => ProjectServiceCategory::find()->filterWhere(['project_house_id' => $id])
                ->filterWhere(['status' => $status])
                ->orderBy('id DESC, project_house_id ASC'),
        ]);

        return $this->render('category', get_defined_vars());
    }

    /**
     * 编辑&详情
     * @param $id
     * @return string|\yii\web\Response
     * @author HQM 2019/02/20
     */
    public function actionCategoryUpdate($id)
    {
        $model = ProjectServiceCategory::findOne($id);

        $projects = $this->projectsCache();
        $projectInfo = ArrayHelper::merge([''=>'请选择',0=>'全部'], ArrayHelper::map($projects, 'house_id', 'house_name'));

        if ($this->isPost) {
            if ($model->load($this->post())) {
                if($model->save()){

                    $this->setFlashSuccess();

                    return $this->backRedirect();
                }

            } else {
                $this->setFlashErrors($model->getErrors());
            }
        }

        return $this->render('category-update', [
            'model' => $model,
            'projectInfo' => $projectInfo,
        ]);
    }

    /**
     * 添加分类
     * @return string|\yii\web\Response
     * @author zhaowenxi
     */
    public function actionCategoryCreate()
    {
        $model = new ProjectServiceCategory();

        $projects = $this->projectsCache();

        $projectList = ArrayHelper::merge([''=>'请选择',0=>'全部'], ArrayHelper::map($projects, 'house_id', 'house_name'));

        if($this->isPost && $model->load(\Yii::$app->request->post())){

            if($model->save()){
                $this->setFlashSuccess();
                return $this->backRedirect(['category']);

            }else{
                $this->setFlashErrors($model->getErrors());
            }

        }

        return $this->render('category-create', [
            'model' => $model,
            'projectList' => $projectList,
        ]);
    }

    /**
     * 项目缓存
     * @param int $ex
     * @return array|mixed|\yii\db\ActiveRecord[]
     * @author zhaowenxi
     */
    protected function projectsCache($ex=7200)
    {
        $key = 'projectLists';
        $projectLists = FileCache::init()->get($key);
        if(empty($projectLists)){
            $projectLists = Project::find()->where(['status' => Project::STATUS_ACTIVE])->select('house_id, house_name')->asArray()->all();
            FileCache::init()->set($key, $projectLists, $ex);
        }

        return $projectLists;
    }

}