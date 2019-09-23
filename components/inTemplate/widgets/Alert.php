<?php
namespace components\inTemplate\widgets;
use components\inTemplate\assets\AlertAsset;
use yii\web\View;

/**
 * 结合前端显示提示信息，设置方法
 * controller中
 * ```php
 * $this->setFlash('info','title','content');
 * ```
 *
 */
class Alert extends \yii\base\Widget
{
    public $alertTypes = [
        'error' => 'alert-danger',
        'success' => 'alert-success',
        'info' => 'alert-info',
        'warning' => 'alert-warning'
    ];

    public function init()
    {
        parent::init();
        $view = $this->getView();
        AlertAsset::register($view);
        $jsCode='toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: "slideDown",
                    timeOut: 4000
                };';
        $session = \Yii::$app->session;
        $flashes = $session->getAllFlashes();
        if (sizeof($flashes) > 0) {
            foreach ($flashes as $type => $data) {
                if (isset($this->alertTypes[$type])) {
                    $data = (array)$data;
                    foreach ($data as $i => $message) {
                        var_dump($message);
                        $message = unserialize($message);
                        if(isset($message['title'])){
                            $jsCode.= sprintf("toastr.%s('%s', '%s');",$type,$message['title'],$message['message']) ;
                        }
                    }
                    $session->removeFlash($type);
                }
            }
        }
        $view->registerJs(sprintf('$(function(){%s})',$jsCode),View::POS_END);
    }
}
