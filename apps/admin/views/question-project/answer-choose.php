<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style type="text/css">
        body {
            margin-left: 0px;
            margin-top: 0px;
            margin-right: 0px;
            margin-bottom: 0px;
            font-size: 12px;
        }
        #questionTable tr th{
            background-color: #eeeeee;
            height: 35px;
        }
        #questionTable tr td{
            text-align: center;
            width: 140px;
            height: 35px;
            background-color: #eef4f9;
        }
    </style>
</head>
<body>
<table width="780" border="0" cellpadding="1" cellspacing="1" id="questionTable">
    <tr>
        <th>调研内容</th>
        <th>非常满意</th>
        <th>满意</th>
        <th>一般</th>
        <th>比较不满意</th>
        <th>非常不满意</th>
        <th>评语</th>
    </tr>
    <?php foreach($AnswerList as $v){?>
        <tr height="30">
        <td>
            <?php
            $feeCycleName =  isset($v->question->site) ? $v->question->site : '--';
            echo $feeCycleName;
            ?>
        </td>
            <?php if($v->replys==5){
                echo '<td><img src="/static/images/yes.png" width="20"></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
            }elseif($v->replys==4){
                echo '<td>&nbsp;</td><td><img src="/static/images/yes.png" width="20"></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
            }elseif($v->replys==3){
                echo '<td>&nbsp;</td><td>&nbsp;</td><td><img src="/static/images/yes.png" width="20"></td><td>&nbsp;</td><td>&nbsp;</td>';
            }elseif($v->replys==2){
                echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><img src="/static/images/yes.png" width="20"></td><td>&nbsp;</td>';
            }elseif($v->replys==1){
                echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><img src="/static/images/yes.png" width="20"></td>';
            }
            ?>
            <td style="text-align: left;"><?=$v->site;?></td>
    </tr>
    <?php }?>
</table>
</body>
</html>
