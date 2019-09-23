<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/30 10:39
 * Description:
 */

namespace components\rbac;


/**
 * Interface RBACInterface
 * @package components\rbac
 *
 * [table]
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $order_id
 * @property integer $parent_id
 * @property string $route
 * @property string $auth_route
 * @property string $icon
 * @property integer $state
 * @property integer $type
 *
 *
 * [extend]
 * @property $visible = true;
 * @property array $navItems
 */
interface RBACInterface
{
    public static function findByRoute($name);
    public static function findByAuthRoute($name);

    /**
     * @return mixed
     * Description:创建菜单栏使用
     */
    public static function findNavActive();

    /**en
     * @return mixed
     * Description:创建菜单栏使用
     */
    public function getNavItems();
    /**
     * @return mixed
     * Description:创建菜单栏使用
     */
    public function getChild();

}