<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/23
 * Time: 16:19
 */

namespace apps\www\controllers;


use common\models\LxjLog;
use yii\web\NotFoundHttpException;

class LxjController extends Controller
{
    public function actionLog()
    {
        $memberId= $this->user->id;

        if(empty($memberId)){
            throw new NotFoundHttpException();
        }

        if(empty($this->user->phone)){
            return $this->renderJsonFail('', -1, ['url' => '/auth/mobile?goUrl=/']);
        }

        $projectHouseId = isset($this->project->house_id) ? $this->project->house_id : 0;
        $ip = \Yii::$app->request->userIP;

        LxjLog::writeLog($memberId, $projectHouseId, $ip);

        return $this->renderJsonSuccess('');
    }
}