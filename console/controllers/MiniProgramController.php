<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018-11-26
 * Time: 11:45
 */

namespace console\controllers;

use common\models\Project;
use components\wechatSDK\WxMiniProgram;
use yii\console\Controller;

class MiniProgramController extends Controller
{
    /**
     * 创建项目带参二维码: projectId
     * @author HQM 2018/11/26
     * @param integer $projectId
     * @throws \yii\base\Exception
     */
    public function actionCreateProjectQrCode($projectId)
    {
        $project = Project::findOne(['house_id' => $projectId]);
        if(!empty($project->mp_qrcode)){
            $this->stdout('已存在' . PHP_EOL);
            exit(0);
        }

        $miniParams = \Yii::$app->params['wechatMini'];
        $miniProgram = new WxMiniProgram($miniParams);

        $data = [
            'path' => 'pages/index/main?projectId=' . $projectId,
        ];
        $json = $miniProgram->getWXACode($data);

        if($json){
            $project->mp_qrcode = $json;
            if($project->save()){
                $this->stdout($json . PHP_EOL);
                $this->stdout('done' . PHP_EOL);
                exit(0);
            }
        }

        var_dump($json);
        echo $miniProgram->errCode . PHP_EOL;
        echo $miniProgram->errMsg . PHP_EOL;

    }
}