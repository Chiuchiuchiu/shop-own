<?php
/**
 *
 */

$this->registerCss(<<<CSS
p{
font-size: 18px;
line-height: 1.4;
padding:.5em 1em;
margin: 2em;
color: #fff;
}
.s1{
background-color: green;
}
.s0{
background-color: red;
}
.s2{
background-color: yellow;
}
CSS
);
?>
<p class="s<?=$code?>"><?=$msg?></p>
