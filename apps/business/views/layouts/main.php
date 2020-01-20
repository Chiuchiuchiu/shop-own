<?php

/* @var $this \yii\web\View */
/* @var $content string */
use apps\business\assets\AppAsset;
use yii\helpers\Html;
use components\inTemplate\widgets\Nav;
use yii\widgets\Breadcrumbs;
use \components\inTemplate\widgets\Alert;

AppAsset::register($this);
Alert::widget();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> 管理后台</title>
    <?php $this->head() ?>

    <?php if(isset($this->blocks['cssFile'])):?>
        <?= $this->blocks['cssFile']; ?>
    <?php endif; ?>

</head>
<body>
<?php $this->beginBody() ?>
<div id="wrapper">
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                            <img alt="image" height="48" class="img-circle" src="/static/images/avatar.png"/>
                             </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear">
                                <span class="block m-t-xs">
                                    <strong
                                        class="font-bold"><?= $this->params['_user']->name ?></strong>
                                <b class="caret"></b>
                                </span>
                            </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
<!--                            <li><a href="--><?//=\yii\helpers\Url::to(['manager/change-password'])?><!--">修改密码</a></li>-->
                            <li class="divider"></li>
                            <li><a href="<?=\yii\helpers\Url::to(['manager/logout'])?>">退出登录</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        LOGO
                    </div>
                </li>
                <?= Nav::widget(['items' => $this->params['_nav']]) ?>
            </ul>

        </div>
    </nav>
    <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-warning " href="#"><i class="fa fa-bars"></i>
                    </a>
                </div>
                <ul class="nav navbar-top-links navbar-right">
                    <li>
                        <span class="m-r-sm text-muted welcome-message">欢迎来到管理后台</span>
                    </li>
                    <li>
                        <a href="/shop-manager/logout">
                            <i class="fa fa-sign-out"></i> 安全登出
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-sm-12">
                <h2><?= $this->title ?></h2>

                <ol class="breadcrumb">
                    <?= Breadcrumbs::widget([
                        "links" => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]); ?>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content">
                    <div class="animated">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="pull-right">Copyright  Company &copy; 2014-<?= date('Y') ?></div>
        </div>
    </div>
</div>

<?php if(isset($this->blocks['jsFile'])):?>
    <?= $this->blocks['jsFile']; ?>
<?php endif; ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
