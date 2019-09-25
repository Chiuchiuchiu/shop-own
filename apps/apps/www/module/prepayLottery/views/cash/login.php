<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCss(<<<CSS
form{
margin-top: 20px;
}
form div{
width: 80%;
margin: 0 auto;
}
label{
font-size: 14px;
color: #434343;
width: 100%;
}
input{
border:1px solid #ccc;
line-height: 2;
display: block;
width: 100%;
}
button{
display: block;
width: 100%;
background-color: #00b3ee;
line-height: 3;
color: #fff;
margin-top: 20px;
}
p{
background-color: #00b3ee;
font-size: 18px;
line-height: 1.4;
padding:.5em 1em;
margin: 2em;
color: red;
}
CSS
);
?>

<?php if($msg){?>
    <p><?=$msg?></p>
    <?php }?>
<?php \yii\widgets\ActiveForm::begin()?>

<div>
    <label>账户名</label>
    <input type="text" name="username">
</div>

<div>
    <label>密码</label>
    <input type="password" name="password">
</div>

<div>
    <button type="submit">提交</button>
</div>
<?php \yii\widgets\ActiveForm::end()?>
