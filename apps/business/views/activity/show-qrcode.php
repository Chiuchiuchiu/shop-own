<?php
use components\inTemplate\widgets\ActiveForm;
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <title></title>
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
     .QrcodeTitle{
         font-size: 18px;
         line-height: 50px;
     }
 </style>
</head>
<body>

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
      <td align="center"><img src="/activity/show-qrcode-url?id=<?=$Items->id;?>" width="300" height="300"></td>
     </tr>
 <tr>
     <td align="center"><?=$Items->ProjectName;?> / <?=$Items->title;?></td>
 </tr>
</table>

</body>
</html>