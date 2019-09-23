<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/5/22 12:31
 * Description:
 */
namespace components\swfUpload\widgets;


use components\swfUpload\assets\AppAsset;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;

class SwfUpload extends Widget
{
    public $name = 'UploadObject[file]';
    public $value;
    public $uploadUrl = '/default/upload';
    public $key;
    public $imgKey = 'img';
    public $uploadObject = null;
    public $model = null;
    public $stringName = null;
    public $field = null;
    public $originalField  = null;

    public function init()
    {
        parent::init();
        $view = $this->getView();
        $asset = AppAsset::register($view);
        $this->originalField = $this->field;
        $this->field = preg_replace(<<<TAG
/\[.+\]/i
TAG
,'',$this->field);
        if(empty($this->value) && $this->model!=null)
            $this->value = $this->model->{$this->field};
        if (empty($this->key)) $this->key = 'K_' . md5(microtime(true) . rand(0, 99999));
        $jsCode = <<<JS
        $(function(){
         $("#{$this->key}").uploadify({
                fileObjName : 'UploadObject[file]',
                height        : 30,
                swf           : '{$asset->baseUrl}/uploadify.swf',
                uploader      : '{$this->uploadUrl}',
                width         : 100,
                onUploadSuccess:function(file, data, response) {
                    console.log(data);
                    data = eval('('+data+');');
                    if (data.code == 0) {
                    console.log('ok')
                    $("#{$this->imgKey}").attr('src',data.data.url);
                    $("#{$this->key}file").attr('value',data.data.savePath);

                    }else alert('上传出错！');
                }
         });
        })
JS;
        $view->registerJs($jsCode, \yii\web\View::POS_END);

    }

    public function run()
    {

        if ($this->model instanceof Model) {
            $showField = strpos($this->originalField,'[')===0? preg_replace('/\]/','][',substr($this->originalField,1),1):$this->originalField;
            $fName = $this->model->formName() ? sprintf('%s[%s]', $this->model->formName(),$showField):$this->originalField;
        } elseif($this->stringName) {
            $fName = $this->stringName;
        }else{
            $fName = $this->name;
        }
        return Html::input('file', $this->name, '', ['id' => $this->key])
        . Html::input('hidden', $fName, $this->value, ['id' => $this->key . 'file']);
    }

}