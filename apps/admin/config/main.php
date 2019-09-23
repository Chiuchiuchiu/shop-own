<?php
$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'admin',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'apps\admin\controllers',
    'defaultRoute'=>'default',
    'bootstrap' => [
        'log',
    ],
    'modules' => [
    ],
    'components' => [
        'adminSQLite' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:@root/database/admin.db',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaCache' => 'cache',
        ],
        'rbac'=>[
            'class'=>'apps\admin\models\RBAC'
        ],
        'user' => [
            'identityClass' => 'apps\admin\models\Manager',
            'enableAutoLogin' => false,
            'authTimeout'=>YII_DEBUG ? 300000:86400,
            'loginUrl'=>'login'
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
        'cache' => [
            'class' => 'yii\caching\DbCache',
//            'db' => 'db',
//            'cacheTable' => 'cache',
        ],
        'errorHandler' => [
            'errorAction' => 'default/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'login'=>'manager/login',
                'logout'=>'manager/logout',
                'upload'=>'default/upload',
                'upload-private'=>'default/upload-private',
            ],
        ],
        'assetManager'=>[
            'linkAssets'=>true,
            'bundles' => [//覆盖系统bootstrap资源
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => '@components/inTemplate/assets/resources/',
                    'css' => ['css/bootstrap.min.css']
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => '@components/inTemplate/assets/resources/',
                    'js' => ['js/bootstrap.min.js']
                ],
            ]
        ],
    ],
    'params' => $params,
];