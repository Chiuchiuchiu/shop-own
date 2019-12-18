<?php
namespace components\ajaxUpload\widgets;
use components\ajaxUpload\assets\AppAsset;
use yii\web\View;

class AjaxUpload extends \yii\base\Widget
{
    public $model = null;
    public $target = null;
    public $content = null;
    public $key = null;
    public $modelFiled = null;

    public function init()
    {
        parent::init();
        $view = $this->getView();
        AppAsset::register($view);

        $js = '';
        if(!empty($this->key)){
            $js = "$('#img{$this->key}').attr('src',response.data.url);
            $('#{$this->modelFiled}{$this->key}').val(response.data.savePath)";
        }

        $jsCode = <<<JS
        $('#{$this->target}').ajaxfileupload({
          action: '/default/upload',
          valid_extensions : ['jpg','gif','png'],
          params: {
            extra: 'info'
          },
          onComplete: function(response) {
              if(response.code == 200){
                  {$js}
              }
              
            console.log(response);
            // eval('response = '+response+';');
          },
          onStart: function() {
            // if(weWantedTo) return false; // cancels upload
          },
          onCancel: function() {
            console.log('no file selected');
          }
        });
JS;
        $view->registerJs(sprintf('$(function(){%s})', $jsCode), View::POS_END);
    }
}
