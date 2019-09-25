<?php
/**
 * Created by
 * Author: feng
 * Time: 2019/08/20 9:55
 * Description:
 */

namespace apps\www\controllers;
use common\models\Ad;
use common\models\Project;

class WelcomeController extends Controller
{

    public function actionIndex()
    {
        $projectId     = isset($this->project->house_id) ? $this->project->house_id : null;
        $_project = Project::find()->where(['house_id'=>$projectId])->all();
        $_url_key = $_project[0]['url_key'];
        $_ctime = time();
        $newDataProvider = Ad::find()
            ->where(['status' => Ad::STATUS_ACTIVE])
            ->andWhere(['type' => Ad::TYPE_1])
            ->andWhere(['like', 'projects', ','.$projectId.','])
            ->andWhere(['<', 'start_time', $_ctime])
            ->andWhere(['>', 'end_time', $_ctime])
            ->orderBy('sort ASC')
            ->all();

        $_list = [];
            $index = 0;
        foreach ($newDataProvider as $keys=>&$row){
            $_v = [];
            $_v['pic'] = $row['pic'];

            if(!empty($row['url'])){
                $_v['url'] = $row['url'] .'/pk/'.$_url_key ."/pid/" .$projectId;
            }else{
                $_v['url'] = $row['url'];
            }
            $_list[$index++] = $_v;
        }

        $_data = array(
            'newDataProvider'=> $_list
        );

      //  var_dump($_list);die;
        //   return $this->redirect("https://ngay.p.51homemoney.com/default/index");
        return $this->renderPartial("welcome", $_data);

    }

}