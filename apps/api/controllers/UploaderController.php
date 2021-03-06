<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/11/19
 * Time: 14:32
 */

namespace apps\api\controllers;


use apps\api\models\UploadFileLog;
use yii\log\FileTarget;
use yii\web\UploadedFile;

class UploaderController extends Controller
{
    public $modelClass = 'apps\api\models\UploadFileLog';
    public $files;

    public function actions()
    {
        $actions = parent::actions(); // TODO: Change the autogenerated stub
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);

        return $actions;
    }

    /**
     * @return string
     */
    public function actionUploadFile()
    {
        $res = [];
        $this->files = UploadedFile::getInstanceByName('file');
        $saveFileName = $this->saveFileName();
        $baseFileName = $this->getBaseFileName($saveFileName);
        $saveAsPath = \Yii::getAlias(\Yii::$app->params['attached.path']) . $saveFileName;
        if($this->files->saveAs($saveAsPath)){
            $res = [
                'url' => \Yii::getAlias('@cdnUrl/'.$baseFileName),
                'savePath' => '@cdnUrl/'.$baseFileName,
            ];

            $logId = UploadFileLog::log($res['savePath'], $res['url']);
            $res['logId'] = $logId;
            return $this->renderJsonSuccess(200, $res);
        }

        $errors = $this->errors($this->files->error);
        return $this->renderJsonFail(-1, ['message' => $errors]);
    }

    public function actionDownload()
    {

        return $this->renderJsonSuccess();
    }

    /**
     * 删除文件，将需要删除的文件ID 存入 delfile.log 日志中，手动删除
     * @param int $logId
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDelfile($logId=0)
    {
        $this->writeFilelog(['id' =>$logId ], 'delfile');

        return $this->renderJsonSuccess();
    }

    /**
     * 生成一个短名字，保存文件
     * @param string $path
     * @return string
     */
    private function saveFileName($path='public')
    {
        $name = md5($this->files->baseName . time() . rand(0, 9999999));
        $name = strtoupper(base_convert(substr($name, rand(0, 24), 8), 16, 32));
        $path = $path . '/' . date('Wy');
        $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']) . $path;
        if (!file_exists($savePath)){
            @mkdir($savePath);
        }
        if (!is_dir($savePath)) {
            throw new Exception('can not make dir');
        }
        $extension = $this->files->extension;
        if (empty($extension)) {
            $type = explode('/', $this->files->type);
            if ($type[0] == 'image') {
                $extension = $type[1];
            }
        }
        return $path . '/' . $name . (empty($extension) ? '' : ('.' . $extension));
    }

    private function getBaseFileName($saveFileName, $path='public')
    {
        $p = '/^' . $path . '\//is';
        return preg_replace($p, '', $saveFileName);
    }

    /**
     * 文件上传错误提示
     * @author HQM 2018/11/21
     * @param $code
     * @return string
     */
    private function errors($code)
    {
        switch($code){
            case UPLOAD_ERR_OK:
                $errors = 'ok';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $errors = '超出文件大小限制';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errors = '超出表单上传大小';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors = '';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors = '没有文件上传';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errors = '无临时目录';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errors = '无法写入文件';
                break;
            case UPLOAD_ERR_EXTENSION:
                $errors = '非法文件扩展';
                break;
            default:
                $errors = '未知错误';
                break;
        }

        return $errors;
    }

}