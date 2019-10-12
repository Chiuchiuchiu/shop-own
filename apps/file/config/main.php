<?php
$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'file',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'apps\file\controllers',
    'defaultRoute'=>'default',
    'bootstrap' => [
        'log',
    ],
    'modules' => [

    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'some',
        ],
        'cache' => [
            'class' => 'yii\caching\DbCache',
        ],
        'errorHandler' => [
            'errorAction' => 'default/error',
        ],
    ],
    'params' => $params,
];