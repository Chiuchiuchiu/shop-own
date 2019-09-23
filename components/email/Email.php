<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2018/8/21
 * Time: 16:23
 */
namespace components\email;

use yii\helpers\ArrayHelper;

class Email
{
    public static $mailInstance;

    public function __construct()
    {
        self::$mailInstance = \Yii::$app->mailer->compose();
    }

    /**
     * @param $setSubject
     * @param string $email
     * @param string $html
     * @return bool
     */
    public function sendToAdmin($setSubject, $email='315780351@qq.com', $html='-')
    {
        self::$mailInstance->setTo($email);
        self::$mailInstance->setSubject($setSubject);
        self::$mailInstance->setHtmlBody($html);
        return self::$mailInstance->send();
    }
}