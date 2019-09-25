<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 12:10
 * Description:
 */

namespace apps\admin\service;


use apps\admin\models\Manager;
use apps\admin\models\ManagerLoginLog;

class ManagerService extends Service
{

    /**
     * @param Manager $manager
     * @return \common\service\ServiceResult
     * Description:
     */
    public static function login(Manager $manager)
    {
        if ($manager->validate()) {
            $manager = Manager::findByEmail($manager->email);
            if (\Yii::$app->user->login($manager)) {
                $log = new ManagerLoginLog();
                $log->manager_id = $manager->id;
                $log->ip = \Yii::$app->request->getUserIP();
                $log->save();
                return self::success();
            } else {
                return self::fail(2, "登录失败");
            }
        }
        return self::fail(1, "账户或密码错误");
    }
}