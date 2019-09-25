<?php
use components\inTemplate\widgets\ActiveForm;
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <title>Title</title>
 <script type="application/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
 <style type="text/css">
  body {
   margin-left: 0px;
   margin-top: 0px;
   margin-right: 0px;
   margin-bottom: 0px;
   font-size: 14px;
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
$form = ActiveForm::begin(['layout' => 'horizontal','id'=>'uploadFile','action'=>'/activity/copy-save']);
?>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
      <td width="120" height="45" align="left" valign="middle">活动名称</td>
      <td><?=$Items->title;?></td>
     </tr>
 <tr>
  <td  align="left"  height="45">请选择分公司</td>
  <td><select name="region_id" id="region_id" onchange="ProjectChoose();">
    <option value="">请选择分公司</option>
          <?php foreach ($RegionList as $v){
              echo '<option value="'.$v->id.'">'.$v->name.'</option>';
          }?>
   </select>
  </td>
 </tr>
 <tr>
   <td align="left"  height="45">请选择项目</td>
    <td><select name="project_id" id="project_id">
    <option value="">请选择</option>
   </select>
  </td>
 </tr>
 <tr >
  <td>&nbsp;</td>
  <td height="50">
      <input type="hidden" name="id" id="id" value="<?=$Items->id;?>">
   <button type="submit" id="submitFile" class="btn btn-success">确认提交</button> &nbsp; &nbsp;&nbsp;&nbsp;<button type="button" id="submitFile" class="btn btn-primary" onclick="parent.layer.closeAll();">取消选择</button></td>
 </tr>
</table>
<?php
ActiveForm::end();
?>
<script>
    function ProjectChoose() {
        $.ajax({
            type: 'GET',
            url: "/question/project-choose?Region_id=" + $('#region_id').val(),
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
</script>

</body>
</html>