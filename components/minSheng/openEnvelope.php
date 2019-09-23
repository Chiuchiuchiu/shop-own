<?php
require_once("php_java.php");//引用LAJP提供的PHP脚本
try
{
	 $base64EnvelopeData = $_REQUEST['base64EnvelopeData'];
   $signAlg = $_REQUEST['signAlg'];
  
   $base64P12Data = $_REQUEST['base64P12Data'];
   $p12Password = $_REQUEST['p12Password'];
 
   $ret = lajp_call("cfca.sadk.api.EnvelopeKit::openEvelopedMessage",  $base64EnvelopeData,$signAlg, $base64P12Data, $p12Password);
   echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>
<a href="index.html">return</a>