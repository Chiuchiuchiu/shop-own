<?php
namespace apps\admin\controllers;

use apps\admin\valueObject\FileCache;
use common\valueObject\UploadObject;
use Yii;

/**
 * Site controller
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUpload(){
        $uploadModel = new UploadObject();
        if($uploadModel->save('file',UploadObject::SAVE_PUBLIC_PATH)){
            return $this->renderJsonSuccess([
                'url'=>Yii::getAlias('@cdnUrl/'.$uploadModel->getBaseFileName()),
                'savePath'=>'@cdnUrl/'.$uploadModel->getBaseFileName()
            ]);
        }
        return $this->renderJsonFail("上传失败");
    }

    public function actionKindUpload(){
        $file = $_FILES['imgFile'];
        $name = md5(uniqid() . time() . rand(0, 9999999));
        $name = strtoupper(base_convert(substr($name, rand(0, 24), 8), 16, 32)).'.jpg';
        $path = '/' . date('Wy');
        $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']).'public' . $path;
        if (!file_exists($savePath)){
            @mkdir($savePath);
        }
        if (!is_dir($savePath)) {
            throw new \Exception('can not make dir');
        }
        move_uploaded_file($file['tmp_name'],$savePath.'/'.$name);
        $savePath = '@cdnUrl/'.$path.'/'.$name;
        return $this->renderJson(['error'=>0,'url'=>Yii::getAlias($savePath),'saveUrl'=>$savePath]);
    }

    public function actionUploadPrivate(){
        $uploadModel = new UploadObject();
        if($uploadModel->save('file',UploadObject::SAVE_PRIVATE_PATH)){
            return $this->renderJsonSuccess([
                'savePath'=>$uploadModel->getBaseFileName()
            ]);
        }
        return $this->renderJsonFail("上传失败");
    }

    /**
     * 清空缓存
     * @return string
     */
    public function actionCleanCache()
    {
        $keyLists = ['project_list', 'admin_nav_'];
        foreach ($keyLists as $key => $value){
            FileCache::init()->delete($value);
        }

        return $this->renderJsonSuccess([]);
    }
}
