<?php

namespace apps\file\controllers;

use apps\admin\models\Manager;
use apps\admin\models\RBAC;
use Yii;

/**
 * Class Controller
 * Description:
 */
class Controller extends \common\controllers\Controller
{
    /**
     * @var array
     */
    protected $missPermission = ['default/error'];
    /**
     * @var Manager
     */
    protected $user = null;
    /**
     * @var null|RBAC
     */
    protected $nav = null;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     * Description:构造通用数据
     */
    public function render($view, $params = [])
    {
        $params['_user'] = $this->user;
        $params['_nav'] = $this->nav;
        return parent::render($view, $params);
    }
}