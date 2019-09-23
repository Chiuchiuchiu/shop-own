<?php
/**
 * Created by PhpStorm.
 * User: HQM
 * Date: 2018/4/24
 * Time: 17:36
 */

namespace components\helper;


use tpmanc\imagick\Imagick;

class File
{
    /**
     * 保存远程PDF文件到本地
     * @param $pdfUrl
     * @param string $dirPath
     * @param string $aliasName
     * @return bool|string
     */
    public static function savePdf($pdfUrl, $dirPath='/pdf/', $aliasName='@cdnUrl')
    {
        if(empty($pdfUrl)){
            return '';
        }

        $savePath = \Yii::getAlias(\Yii::$app->params['attached.path']) . 'public' . $dirPath;

        $fileName = pathinfo($pdfUrl, PATHINFO_BASENAME);
        $subDir = date('Wy') . '/';
        $savePath .= $subDir;
        if (!file_exists($savePath)) {
            @mkdir($savePath, 0775, true);
        }
        if (!is_dir($savePath)) {
            return false;
        }

        $files = file_get_contents($pdfUrl);

        file_put_contents($savePath . $fileName, $files);

        //保存 Jpg 格式
        if(class_exists("Imagick")){
            $pdfFile = $savePath . $fileName;
            $imagick = Imagick::open($pdfFile.'[0]');
            $saveJpg = $savePath . $fileName . '.jpg';
            $imagick->saveTo($saveJpg);
        }

        return $savePath = $aliasName. $dirPath . $subDir . $fileName;
    }

    /**
     * 生成文件名
     * @author HQM 2018/11/26
     * @param string $ext 文件扩展
     * @return string
     */
    public static function makeName($ext='.jpg')
    {
        $name = md5(uniqid() . time() . rand(0, 9999999));
        $name = strtoupper(base_convert(substr($name, rand(0, 24), 8), 16, 32));
        $name .= $ext;

        return $name;
    }

}