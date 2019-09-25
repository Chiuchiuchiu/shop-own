 <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="ThemeBucket">
        <title>我的生活</title>
        <link rel="stylesheet" href="/static/css/base.css" />
        <link rel="stylesheet" href="/static/css/style.css" />
        <script src="/static/js/jquery-2.1.1.min.js"></script>
        <script src="/static/js/layer_mobile/layer.js"></script>
        <style type="text/css">
          
        </style>
    </head>
<body class="bg-f1">
<div>

<span id='hello'>hello</span>
</div>

 <!-- footer部分 -->
 <?php 
    include(dirname(dirname(__FILE__)).'/public/foot.php');
?> 
<!-- end -->
<script>
    $(function(){
      $("#hello").on('click',function(){
       //提示
        layer.open({
            content: 'hello layer'
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
        // layer.msg('hello');
      });
    })
</script>
</body>
</html>
