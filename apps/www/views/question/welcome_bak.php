<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">

    <title>调查问卷</title>
    <link rel="stylesheet" href="/static/css/base.css" />
    <link rel="stylesheet" href="/static/css/style.css" />
    <script src="/static/js/jquery-2.1.1.min.js"></script>
    <script src="/static/js/layer/layer.js"></script>
    <script src="/static/js/layer/Common.js"></script>
    <style type="text/css">
        body {
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .f-content {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 80px;
            left: 0;
            background-image: url(/static/images/welcome/f-bg.png);
            background-size: 100% 100%;
        }
        .f-btn {
            width: 120px;
            height: 40px;
            background: transparent;
            border: 1px solid white;
            border-radius: 5px;
            color: white;
            font-size: 16px;
        }
        input, button, textarea, select, div {
            outline: medium;
        }

        .f-footer {
            height: 80px;
            text-align: center;
            padding-top:10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

    </style>
    <script type="application/javascript">
        function  toIndex() {
            location='//'
        }

    </script>
</head>
<body style="background-color:#FFF;">
<div class="hjk-container">
    <div class="f-content">
        <div style="text-align: center;padding-top: 13%;padding-bottom: 30px;">
            <img src="/static/images/welcome/f-title2.png" style="width: 50%;">
        </div>
        <div style="padding: 10%;text-align: center;">
            <img src="/static/images/welcome/f-tipsr.png" style="width: 100%;">
        </div>
        <div style="text-align: center;position: relative;top: -5rem;">
            <button class="f-btn" onclick="toIndex()">开始问卷</button>
        </div>
    </div>
    <div class="f-footer">
        <img src="/static/images/welcome/f-ftexter.png" style="height: 72%;">
    </div>
</div>
</body>
</html>