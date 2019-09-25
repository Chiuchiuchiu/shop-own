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
$form = ActiveForm::begin(['layout' => 'horizontal','id'=>'uploadFile','action'=>'/question-category/upload-xls-save','options' => ['enctype' => 'multipart/form-data']]);
?>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td height="50"><input name="UploadObject[file]" required id="excelFile" class="form-control" type="file" accept=".csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"></td>
    </tr>
    <tr>
        <td>
            <input type="hidden" id="category_id" name="category_id" value="<?=$model->id?>">
            <?=$model->title?>
        </td>
    </tr>
    <tr >
        <td height="30"><button type="submit" id="submitFile" class="btn btn-success">确认上传</button> &nbsp; &nbsp;&nbsp;&nbsp;<button type="button" id="submitFile" class="btn btn-primary" onclick="parent.layer.closeAll();">取消上传</button></td>
    </tr>

    <tr >
    <td  height="30">*请上传.xls结尾的表格文件<font color="red">表格示例下载</font> </td>
    </tr>
</table>
<?php
ActiveForm::end();
?>


</body>
</html>
