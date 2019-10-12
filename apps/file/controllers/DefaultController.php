<?php
namespace apps\file\controllers;

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
        $data = ['code' => 500, 'message' => "error"];
        return $this->renderJson($data);
    }

    public function actionError()
    {
        $data = ['code' => 500, 'message' => "error"];
        return $this->renderJson($data);
    }
}
