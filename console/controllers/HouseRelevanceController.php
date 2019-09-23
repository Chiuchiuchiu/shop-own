<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/4/12
 * Time: 10:04
 */

namespace console\controllers;


use common\models\House;
use common\models\HouseRelevance;
use common\models\Project;
use yii\console\Controller;

class HouseRelevanceController extends Controller
{
    /**
     * 更新parent_ids字段，最高到项目 $projectId
     * @param null $projectId
     * @author zhaowenxi
     */
    public function actionUpdates($projectId = null){

        $this->stdout("{$projectId} start \n");

        $project = Project::find()->where(['status' => 1])->andFilterWhere(['house_id' => $projectId])->all();

        if($project){

            /** @var $v Project */
            foreach ($project as $k => $v){
                $houseData = House::find()->where(['project_house_id' => $v->house_id, 'deepest_node' => 1])->all();

                if($houseData){
                    foreach ($houseData as $key => $val){

                        /** @var $val House */
                        $arr = $this->findParent($val);

                        $model = HouseRelevance::findOrCreate($val->house_id);
                        $model->house_id = $val->house_id;
                        $model->project_id = $arr[0];
                        $model->area_id = $arr[1];
                        $model->parent_ids = ',' . implode(',', $arr) . ',';
                        $model->created_at = time();
                        $model->save();
                    }
                }

                sleep(1);

                $this->stdout("project: {$v->house_name} finish \n");
            }

        }

        $this->stdout("{$projectId} end \n");
    }

    private function findParent($house, &$res = []){

        $parentInfo = House::findOne(['house_id' => $house->parent_id]);

        array_unshift($res, $parentInfo->house_id);

        if($parentInfo->parent_id != 0){

            $this->findParent($parentInfo, $res);

            unset($parentInfo);

        }

        return $res;
    }

    public function actionUpdatesAuto(){

        $projects = Project::find()->where(['status' => Project::STATUS_ACTIVE])->all();

        foreach ($projects as $v){
            /** @var Project $v */

            $this->actionUpdates($v->house_id);

            sleep(0.5);

            $this->stdout("sleep 0.5s \n");
        }
    }
}