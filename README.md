Yii 2 Advanced Project Template
===============================

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-advanced/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii2-app-advanced/downloads.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

服务器版本
---------------
* php 7.1.0
* mysql 5.6
* nginx 1.1+

前端管理
----------------
采用一下技术点
* scss
* typescript
编译见 gulpfile.js && gulp/*-config.json


数据SQL
-------------
见 sql 目录

数据字典与关系
--------------
见各model定义

DIRECTORY STRUCTURE
-------------------
```
apps
    mgt/           后台系统
        assets/          资源管理
        config/          配置
        controllers/     控制器
        models/          私有模型
        module/          模块(如果需要)
        runtime/         
        service/         服务层，减少models之间的业务依懒。使用服务器处理多个models的业务
        valueObject       VO
        views/           模板
        web/             web根目录
    www                  平台管理后台
        ....             略,与上述项目一致
    pm                   管理处端
        ....
    butler               管家端
        ....
components               组件
    inTemplate           后台通用的模板组件
    rbac                 权限控制组件--依懒数据库
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
sql/                     程序对应数据库导出SQL
    */                   代表库名      
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
```



NGINX REWRITE
-----
```
location / {
    index  index.php;
    try_files $uri $uri/ /index.php?$query_string;
}
```
