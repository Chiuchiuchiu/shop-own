<?php
use components\inTemplate\widgets\ActiveForm;
?>
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
        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #1ab394;
            border-color: #1ab394;
            color: #FFF;
        }
        .btn-success {
            background-color: #1c84c6;
            border-color: #1c84c6;
            color: #FFF;
        }
    </style>
</head>
<body>
<?php
$form = ActiveForm::begin(['layout' => 'horizontal','id'=>'uploadFile','action'=>'/question-project/choose-save','options' => ['enctype' => 'multipart/form-data']]);
?>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td></td>
        <td><h4>题目内容</h4></td>
        <td align="center"><h4>适用范围</h4></td>
    </tr>
    <?php foreach($Question as $v){
        $Arr = explode(',',$model->content);
        ?>
        <tr>
            <td width="28" height="28" align="center" valign="middle"><input type="checkbox" name="question[]" id="question[]" value="<?=$v->id;?>" <?php  if(in_array($v->id,$Arr)){ echo 'checked';} ?> /></td>
            <td width="80%"><label for="question[]"><?=$v->title;?></label></td>
            <td width="26%" align="center"><?= \apps\admin\models\Question::typeMap()[$v->type_id]?></td>
        </tr>
    <?php }?>
    <tr >
        <td>&nbsp;</td>
        <td height="50">
            <input type="hidden" name="ProjectID" id="ProjectID" value="<?=$model->id;?>">
            <button type="submit" id="submitFile" class="btn btn-success">确认提交</button> &nbsp; &nbsp;&nbsp;&nbsp;<button type="button" id="submitFile" class="btn btn-primary" onclick="parent.layer.closeAll();">取消选择</button></td>
    </tr>
</table>
<?php
ActiveForm::end();
?>
</body>
</html>
