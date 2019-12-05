<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/16 12:10
 * Description:
 */

namespace apps\business\service;


use apps\business\models\ShopManagerLoginLog;
use apps\business\models\ShopManager;

class ManagerService extends Service
{

    /**
     * @param ShopManager $manager
     * @return \common\service\ServiceResult
     * Description:
     */
    public static function login(ShopManager $manager)
    {
        if ($manager->validate()) {
            $manager = ShopManager::findByEmail($manager->mobile);
            if (\Yii::$app->user->login($manager)) {
                $log = new ShopManagerLoginLog();
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