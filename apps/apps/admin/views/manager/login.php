<?php
/**
 * @var $model \apps\admin\models\Manager
 */
$this->title = '登录';
?>

<div class="middle-box text-center loginscreen  animated fadeInDown">
    <div>
        <h1 class="logo-name animated rotateIn">
            <img src="/static/images/logo.png" style="" alt="logo">
        </h1>
    </div>
    <h3 class="animated slideInRight">商城管理后台</h3>
    <p>&nbsp;</p>
    <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
    <?= $form->field($model, 'email')->textInput(['type' => 'email', 'placeholder' => '账户','value'=>defined('YII_DEBUG')&&YII_DEBUG?'root@root.com':''])->label(false) ?>
    <?= $form->field($model, 'password')->passwordInput(['placeholder' => '密码','value'=>defined('YII_DEBUG')&&YII_DEBUG?'123456':''])->label(false) ?>
    <button type="submit" class="btn black-bg block full-width"><span style="color: white">登录</span></button>
    <?php \yii\bootstrap\ActiveForm::end(); ?>
    <p class="m-t">
        <a href="http://www.beian.miit.gov.cn"><small>Copyright &copy;  2016-<?= date('Y') ?></small></a>
    </p>

</div>
