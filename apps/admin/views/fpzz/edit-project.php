<?php
/**
 * Created by PhpStorm.
 * User: mickey
 * Date: 2019-02-15
 * Time: 11:02
 * @var string $checked
 * @var string $status
 * @var string $csrf
 * @var integer $dataId
 */
?>


<!DOCTYPE html>
<html class="site-demo-overflow">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <?= \yii\helpers\Html::csrfMetaTags() ?>

    <link rel="stylesheet" href="//cdn.90so.net/layui/2.4.5/css/layui.css"  media="all">
</head>
<body>
<div class="layui-layout layui-layout-admin">

    <div class="layui-tab layui-tab-brief">

        <div class="layui-tab-content">

            <div class="layui-tab-item layui-show">
                <div class="layui-main">
                    <div id="LAY_preview">

                        <form class="layui-form" action="">
                            <div class="layui-form-item">
                                <label class="layui-form-label">提示（客户端显示）</label>
                                <div class="layui-input-block">
                                    <input type="text" name="tips" lay-verify="title" autocomplete="off" placeholder="请输入" value="<?= $model->tips ?>" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">状态：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="0" title="禁用" <?= $status == 0 ? 'checked' : ''?> >
                                    <input type="radio" name="status" value="1" title="启用" <?= $status == 1 ? 'checked' : ''?> >

                                    <input type="hidden" name="id" value="<?= $dataId ?>">
                                    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="layui-btn" lay-submit lay-filter="update">更新</button>
                                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="site-mobile-shade"></div>
    <script src="//cdn.90so.net/layui/2.4.5/layui.all.js" charset="utf-8"></script>

</div>

<script>
    layui.use(['form'], function(){
        var form = layui.form, layer = layui.layer;
        var $ = layui.jquery;

        //自定义验证规则
        form.verify({
            title: function(value){
                if(value.length < 5){
                    return '不少于5个字符';
                }
            }
        });

        //监听提交
        form.on('submit(update)', function(data){
            var postData = $(data.form).serialize();

            $.ajax({
                url: 'update',
                type: 'POST',
                data: postData,
                success: function (res){
                    if(res.code == 0){
                        layer.alert('已更新');
                    }
                },
                dataType: 'JSON'
            });

            return false;
        });
    });
</script>

</body>
</html>
