<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/30 10:39
 * Description:
 */

namespace components\rbac;


interface RBACPermissionInterface
{
    /**
     * @param $permission
     * @return mixed
     * Description:
     */
    public function hasPermission($permission);
}