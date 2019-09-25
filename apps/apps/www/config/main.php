<?php
$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'www',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'apps\www\controllers',
    'defaultRoute'=>'default',
    'bootstrap' => [
        'log',
    ],
    'modules' => [
        'prepay-lottery'=>[
            'class'=>'apps\www\module\prepayLottery\Module'
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'apps\www\models\Member',
            'enableAutoLogin' => false,
            'authTimeout'=>YII_DEBUG ? 300000:1200,
            'loginUrl'=>'/login'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'default/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'auth-code'=>'default/auth-code',
                'upload'=>'default/upload',
            ],
        ],
        'assetManager'=>[
            'linkAssets'=>true,
            'baseUrl'=>'//assets.cdn.cdj.loc.com/www/',
            'bundles' => [//覆盖系统bootstrap资源
                'yii\web\JqueryAsset' => [
                    'sourcePath' =>null,
                    'js' => []
                ],
            ]
        ],
    ],
    'params' => $params,
];