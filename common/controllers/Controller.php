<?php

namespace common\controllers;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\Cookie;

/**
 * Class Controller
 * @package apps\cpsCenter\controllers
 * Description:
 *
 *
 * @property boolean $isAjax
 * @property boolean $isGet
 * @property boolean $isPost
 */
class Controller extends \yii\web\Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }
    /**
     * @return boolean
     */
    public function getIsAjax()
    {
        return Yii::$app->request->isAjax;
    }
    /**
     * @return boolean
     */
    public function getIsGet()
    {
        return Yii::$app->request->isGet;
    }
    /**
     * @return boolean
     */
    public function getIsPost()
    {
        return Yii::$app->request->isPost;
    }
    /**
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed
     * Description:
     */
    public function post($name = null, $defaultValue = null)
    {
        return Yii::$app->request->post($name, $defaultValue);
    }
    public function get($name = null, $defaultValue = null)
    {
        return Yii::$app->request->get($name, $defaultValue);
    }
    /**
     * @param string $type 'success|error|info|warning'
     * @param $title
     * @param null $message
    Description:
     */
    protected function setFlash($type = "info", $title, $message = null)
    {
        Yii::$app->session->setFlash($type, serialize(['title' => $title, 'message' => $message]),false);
    }
    protected function setFlashSuccess($title = "操作成功", $message = null)
    {
        $this->setFlash('success', $title, $message);
    }
    protected function setFlashError($title, $message = null)
    {
        $this->setFlash('error', $title, $message);
    }
    protected function setFlashInfo($title, $message = null)
    {
        $this->setFlash('info', $title, $message);
    }
    protected function setFlashWarning($title, $message = null)
    {
        $this->setFlash('warning', $title, $message);
    }
    /**
     * @param string $view
     * @param array $params
     * @return string
     * Description:重写该方法，支持参数直接传递到布局，
     * 在任何view中可以通过 $this->params获取对应的参数
     * by:Zhao
     */
    public function render($view, $params = [])
    {
        $_view = $this->getView();

        $content = $_view->render($view, $params, $this);
        $_view->params = array_merge($_view->params, $params);
        return $this->renderContent($content);
    }
    protected function renderJson($params){
        return json_encode($params);
    }
    protected function setFlashErrors($datas)
    {
        foreach ($datas as $value){
            if(is_string($value)){
                $this->setFlash('error', $value, null);
            }elseif(is_array($value)){
                $this->setFlashErrors($value);
            }
        }
    }
    protected function renderJsonSuccess($params){
        return $this->renderJson([
            'code'=>0,
            'data'=>$params
        ]);
    }
    protected function renderJsonFail($message,$code=-1,$params=[]){
        return $this->renderJson([
            'code'=>$code,
            'message'=>$message,
            'data'=>$params
        ]);
    }
    /**
     * @param $data
     * @param string $type post|get
     * @return bool
     * @throws ErrorException
     * Description:
     */
    protected function loadTo(&$data,$type='post'){
        $res = true;
        foreach($data as &$obj){
            if(!$obj instanceof Model){
                throw new ErrorException;
            }
            $res = $res && $obj->load($this->$type());
        }
        return $res;
    }

    public function setCookie($name,$value,$expire=0,$domain='',$path='/',$httpOnly = true,$secure=false){
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new Cookie(
            compact('name','value','expire','domain','path','httpOnly','secure')
        ));
    }

    public function getCookie($name){
        $res =  Yii::$app->request->cookies->get($name);
        return isset($res->value)?$res->value:null;
    }
}