<?php
namespace apps\www\controllers;

use common\models\Category;
use common\models\FriendlyLink;
use common\models\Site;
use common\valueObject\UploadObject;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class WechatLoginController extends Controller
{

    public function actionIndex()
    {
        $this->user->auth_key = md5(rand(0,9999)).md5(time());
        $this->user->save();
        return $this->redirect(Yii::$app->params['butler'].'/?code='.$this->user->auth_key);
    }
}
