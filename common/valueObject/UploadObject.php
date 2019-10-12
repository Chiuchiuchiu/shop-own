<?php
/**
 * Created by
 * Author: zhao
 * Time: 16/6/13 11:47
 * Description:
 */

namespace common\valueObject;


use yii\base\ErrorException;
use yii\base\Exception;
use yii\web\UploadedFile;

/**
 * Class UploadObject
 * @package common\valueObject
 * Description:
 * @property string $baseFileName
 */
class UploadObject extends ValueObject
{
    /**
     *UploadedFile file attribute
     */
    public $file;

//    public $authExtension = ['jpg','gif','jpeg','png'];
    const SAVE_PRIVATE_PATH = 'private';
    const SAVE_PUBLIC_PATH = 'public';

    public $saveFileName = '';

    //怎么重写这个办法，让这个可以支持多个文件上传
    public function formName()
    {
        return parent::formName();
    }


    public function getBaseFileName()
    {
        $p = '/^' . self::SAVE_PUBLIC_PATH . '\//is';
        return preg_replace($p, '', $this->saveFileName);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $res = parent::validate($attributeNames, $clearErrors);
        //TODO 验证文件类型,上传权限等
        return $res;
    }


    public function save($name, $path = '')
    {
        $this->file = UploadedFile::getInstance($this, $name);
        if ($this->file && $this->validate()) {
            $this->saveFileName = $this->saveFileName($path);
            return $this->file->saveAs(\Yii::getAlias("@file") . "/web/attached/" . $this->saveFileName);
        }
        return false;
    }

    /**
     * @param $path
     * @return string
     * @throws Exception
     * Description:生成一个短名字，保存文件
     */
    private function saveFileName($path)
    {
        $name = md5($this->file->baseName . time() . rand(0, 9999999));
        $name = strtoupper(base_convert(substr($name, rand(0, 24), 8), 16, 32));
        $path = $path . '/' . date('Wy');
        $savePath = \Yii::getAlias("@file") . '/web/attached/' . $path;

        if (!file_exists($savePath)){
            @mkdir($savePath, 0777);
        }
        if (!is_dir($savePath)) {
            throw new Exception('can not make dir');
        }
        $extension = $this->file->extension;
        if (empty($extension)) {
            $type = explode('/', $this->file->type);
            if ($type[0] == 'image') {
                $extension = $type[1];
            }
        }
        return $path . '/' . $name . (empty($extension) ? '' : ('.' . $extension));
    }

    public function unlink()
    {
        return unlink(\Yii::getAlias(\Yii::$app->params['attached.path']) . $this->saveFileName);
    }
}