<?php
namespace apps\business\controllers;



use common\models\House;
use common\models\Project;

class HouseController extends Controller
{
    public function actionQueryChild($id){
        if(empty($id)){
            $list = $this->projectCache();
        } else {
            $list = House::find()
                ->where(['parent_id'=>$id])
                ->select('house_id,house_name')
                ->asArray()
                ->all();
        }

        return $this->renderJsonSuccess(['list'=>$list]);
    }

    public function actionQueryProject()
    {
        $list = Project::find()->asArray()->select('house_id,house_name,area')->all();
        return $this->renderJsonSuccess(['list'=>$list]);
    }

}
