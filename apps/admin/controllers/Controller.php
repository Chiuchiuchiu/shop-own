<?php

namespace apps\admin\controllers;

use apps\admin\models\Manager;
use apps\admin\models\RBAC;
use apps\admin\valueObject\FileCache;
use common\models\House;
use Yii;
use yii\helpers\Url;
use yii\log\FileTarget;
use yii\web\ForbiddenHttpException;

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
        if (!Yii::$app->user->isGuest) {
            $this->user = Yii::$app->user->identity;
            $this->nav = $this->navActiveCache();
            $this->filterNavPermission($this->nav);
        }
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            //验证登录
            if (in_array($this->route, $this->missPermission)) {
                return true;
            }
            if (Yii::$app->user->isGuest) {
                $this->redirect('/login?redirectURL=' . urlencode(Yii::$app->request->getUrl()));
                return false;
            }
            if($this->user->need_change_pw==Manager::NEED_CHANGE_PASSWORD_YES && $this->route!='manager/change-password'){
                $this->setFlashWarning("当前状态下，您必须要修改一次密码");
                $this->redirect(Url::to(['manager/change-password']));
                return false;
            }
//            权限模块
            if (!$this->user->hasPermission($this->route) && $this->route != 'default/error') {
                throw new ForbiddenHttpException("您没有权限访问这个功能 " . $this->route);
            }
            return true;
        }
        return false;
    }

    protected function filterNavPermission($item)
    {
         //启用缓存
        if (is_array($item)) {
            foreach ($item as &$row) {
                $this->filterNavPermission($row);
            }
        } else if ($item instanceof RBAC) {
            if (isset($item->id)) {
                $item->visible = $this->user->hasPermission($item);
                if (is_array($item->navItems) && sizeof($item->navItems) > 0) {
                    foreach ($item->navItems as $row) {
                        $this->filterNavPermission($row);
                    }
                }
            }
        }
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

    public function reloadParent(){
        return "<script>parent.location.href=parent.location.href;</script>";
    }

    protected function backRedirect($default='/'){
        $_referrer = $this->get('_referrer');
        return $this->redirect(Url::toRoute($_referrer?urldecode($this->get('_referrer')):$default));
    }

    protected function projectCache($ex=3600)
    {
        $key = "project_list";
        $projectLists = FileCache::init()->get($key);
        if(empty($projectLists)){
            $projectLists = House::find()->select('project_house_id AS house_id, house_name')
                ->where(['parent_id' => 0])
                ->orderBy('house_name ASC')
                ->asArray()
                ->all();
            FileCache::init()->set($key, $projectLists, $ex);
        }

        return $projectLists;
    }

    /**
     * 菜单缓存
     * @param int $ex
     * @return RBAC[]|mixed
     */
    protected function navActiveCache($ex=3600)
    {
        $key = "admin_nav_";
        $navLists = FileCache::init()->get($key);
        if(empty($navLists)){
            $navLists = RBAC::findNavActive();
            FileCache::init()->set($key, $navLists, $ex);
        }

        return $navLists;
    }

    /**
     * 记录请求参数
     * @author HQM 09-14
     * @param $msg
     * @param string $requestUrl
     * @throws \yii\base\InvalidConfigException
     */
    protected function fileLog($msg, $requestUrl='')
    {
        $msgLog = $msg;

        $fileLog = new FileTarget();
        $fileLog->logFile = \Yii::$app->getRuntimePath() . '/logs/repair.log';
        $fileLog->messages[] =  [$msgLog, 8, 'application', microtime(true)];;
        $fileLog->export();
    }

}