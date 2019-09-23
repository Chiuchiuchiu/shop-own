<?php
use \common\models\QuestionAnswer;
use \apps\business\models\QuestionProject;
use \common\models\MemberHouse;
use \components\inTemplate\widgets\ActiveForm;
use components\inTemplate\widgets\Html;
use components\inTemplate\widgets\GridView;

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
    <script src="/static/js/jquery-2.1.1.min.js"></script>
</head>
<body>
<?php
ActiveForm::begin(['method' => 'get', 'action' => \yii\helpers\Url::to(['/question-project/answer-save'])])
?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="40%">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td height="35" align="right">房产信息：</td>
            <td><?=$AnswerItem->ancestor_name?></td>
        </tr>
        <tr>
            <td height="35" align="right">调研名单：</td>
            <td><?=$AnswerItem->chose_ancestor_name?></td>
        </tr>
        <tr>
            <td height="35" align="right">调查姓名：</td>
            <td><?=$AnswerItem->surname?></td>
        </tr> <tr>
            <td height="35" align="right">联系电话：</td>
            <td><?=$AnswerItem->telephone?></td>
        </tr>
        <tr>
            <td height="35" align="right">分 公 司：</td>
            <td>
                <select class="input-group form-control" name="project_region_id" id="project_region_id" onchange="ProjectChoose();">
                    <option value="" <?php if($AnswerItem->project_region_id==''){ echo ' selected';}?> >请选择分公司</option>
                    <?php foreach ($ProjectRegion as $v){?>
                        <option value="<?=$v['id']?>" <?php if($AnswerItem->project_region_id==$v['id']){ echo ' selected';}?> ><?=$v['name']?></option>
                    <?php }?>
                </select>
            </td>
        </tr>
        <tr>
            <td height="35" align="right">项目名称：</td>
            <td> <select class="input-group form-control" name="project_id" id="project_id" onclick="ButlerChoose();">
                    <?php foreach ($Project as $v){?>
                        <option value="<?=$v['house_id']?>" <?php if($AnswerItem->project_house_id==$v['house_id']){ echo ' selected';}?> ><?=$v['house_name']?></option>
                    <?php }?>
                </select></td>
        </tr>
        <tr>
            <td height="35" align="right">管家名称：</td>
            <td> <select class="input-group form-control" name="butler_id" id="butler_id">
                    <option value="<?=$AnswerItem->butler_id?>" selected><?=$AnswerItem->butler->nickname;?></option>
                </select></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td><input type="hidden" id="id" name="id" value="<?=$AnswerItem->id;?>">
                <input type="submit" name="button" id="button" value="提交修改"  class="btn btn-success" /> <input type="button" name="button2" id="button2" value="取消修改"  class="btn btn-primary" onclick="parent.layer.closeAll();" /></td>
        </tr>
    </table>
<script>
    function ProjectChoose() {
        $.ajax({
            type: 'GET',
            url: "/question/project-choose?Region_id=" + $('#project_region_id').val(),
            timeout: 3000, //超时时间：30秒
            dataType:'json',
            success: function (data) {
                $('select[name=project_id]').empty();
                $('select[name=project_id]').append("<option data-id='0' value='0'>不选择子项目</option>");
                $.each(data.houseArr,function(n,value){

                    $('select[name=project_id]').append("<option data-id='"+value['house_id']+"' value='"+value['house_id']+"'>"+value['house_name']+"</option>");
                });
                $('select[name=project_id]').trigger("change");
            },
            error: function (data) {
                alert(data);
            }
        });
    }
    function ButlerChoose() {
        $.ajax({
            type: 'GET',
            url: "/question/butler-choose?project_id=" + $('#project_id').val(),
            timeout: 3000, //超时时间：30秒
            dataType:'json',
            success: function (data) {
                $('select[name=butler_id]').empty();

                $.each(data.ButlerList,function(n,value){

                    $('select[name=butler_id]').append("<option data-id='"+value['id']+"' value='"+value['id']+"'>"+value['nickname']+"</option>");
                });
                $('select[name=butler_id]').trigger("change");
            },
            error: function (data) {
                alert(data);
            }
        });
    }

</script>
<?php
ActiveForm::end();
?>
</body>
</html>
