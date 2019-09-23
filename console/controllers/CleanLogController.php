<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/17
 * Time: 11:55
 */

namespace console\controllers;


use apps\mgt\models\FpzzLog;
use yii\console\Controller;

class CleanLogController extends Controller
{
    public function actionClean()
    {
        $time = time() - 24 * 3600 * 7;
        FpzzLog::deleteAll("created_at < {$time} AND fp_cached_id=''");
    }
}