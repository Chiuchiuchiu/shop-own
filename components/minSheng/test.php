<?php

require_once("php_java.php");//引用LAJP提供的PHP脚本

$name ="LAJP";  //定义一个名称

try
{
  //调用Java的hello.HelloClass类中的hello方法
  $ret = lajp_call("hello.HelloClass::hello", $name);
  echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$ret}<br>";
  echo "$e";
}
?>

<?php

    Phpinfo();

?>