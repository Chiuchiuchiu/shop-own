<?php
/**
 * Created by PhpStorm.
 * User: zhaowenxi
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

                        <table class="layui-table">
                            <colgroup>
                                <col width="150">
                                <col width="400">
                                <col>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>房产地址</th>
                                <th>认证时间</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $v): ?>
                                    <tr>
                                        <td><?= $v['house_id']; ?></td>
                                        <td><?= $v['ancestor_name']; ?></td>
                                        <td><?= date('Y-m-d H:i', $v['updated_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="site-mobile-shade"></div>


</div>
<script src="//cdn.90so.net/layui/2.4.5/layui.all.js" charset="utf-8"></script>
<script>

</script>

</body>
</html>
