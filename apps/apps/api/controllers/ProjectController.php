<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
 * Date: 2018/9/20
 * Time: 10:59
 */

namespace apps\api\controllers;

use apps\api\models\Project;
use common\models\House;

class ProjectController extends Controller
{
    public $modelClass = 'apps\api\models\Project';

    public function actions()
    {
        $actions =  parent::actions();

        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['update']);
        unset($actions['create']);

        return $actions;
    }

    public function actionIndex(){

        $page = $this->get('page', 1);

        is_numeric($page) || $this->renderJsonFail(40010);

        $page = ($page - 1 < 0) ? 0 : $page - 1;

        $res = Project::find()->select('house_id AS projectId, house_name AS name')
            ->where(['status' => Project::STATUS_ACTIVE])
            ->offset($page)
            ->limit(20)
            ->asArray()->all();

        $this->renderJsonSuccess(200, $res);
    }

    public function actionSon($projectId, $parentId){

        $res = ['list' => [], 'parentId' => 0];

        $projectIsExist = self::getProject($projectId);

        $projectIsExist || $this->renderJsonSuccess(200, $res);

        $res['list'] = House::find()->select("house_id AS id, house_name AS name")->where(['project_house_id' => $projectId, 'parent_id' => $parentId])
            ->AsArray()
            ->all();

        $res['parentId'] = $projectIsExist->house_id;

        $this->renderJsonSuccess(200, $res);
    }

    public function getProject($project){

        is_numeric($project) || $this->renderJsonFail(40011);

        return Project::findOne($project);
    }
}