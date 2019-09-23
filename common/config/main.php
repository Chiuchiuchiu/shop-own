<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'timeFormat' => 'php:H:i:s',
            'currencyCode' => '￥',
            'nullDisplay' => '-'
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=cdj_master',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'eventDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=cdj_event',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'logDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=cdj_log',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'wmpDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=cdj_wmp',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
            'tablePrefix' => 'wp_',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',  //你的redis地址
            'port' => 6379, //端口
            'database' => 0,
        ]
    ],
    'language' => 'zh-CN',
    'timeZone' => 'PRC',
    'aliases' => [
        'cdnUrl' => '/attached/'
    ],

    'aliases' => [
        'cdnUrl' => '/attached/'
    ],
];