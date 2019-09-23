<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">

    <title>调查问卷</title>
        <style type="text/css">
            <!--
            body {
                margin-left: 0px;
                margin-top: 0px;
                margin-right: 0px;
                margin-bottom: 0px;
            }
            -->
        </style></head>

<body>
<table width="1000" border="1"  align="center"  cellspacing="0" cellpadding="0">
    <tr>
        <td>项目名称</td>
        <td align="center">样本量</td>
        <td align="center">调查数</td>
        <td align="center">有效调查数</td>
        <td align="center">1分数</td>
        <td align="center">2分数</td>
        <td align="center">3分数</td>
        <td align="center">4分数</td>
        <td align="center">5分数</td>
    </tr>
    <?php foreach ($ListArr as $key=>$row){?>
    <tr>
        <td><?=$row['ProjectName'];?></td>
        <td align="center"><?=$row['plan_count'];?></td>
        <td align="center"><?=$row['Count'];?></td>
        <td align="center"><?=$row['QuestionAnswerCount'];?></td>
        <td><?=$row['number1'];?></td>
        <td><?=$row['number2'];?></td>
        <td><?=$row['number3'];?></td>
        <td><?=$row['number4'];?></td>
        <td><?=$row['number5'];?></td>
    </tr>
    <?php }?>
</table>
</body>
</html>
