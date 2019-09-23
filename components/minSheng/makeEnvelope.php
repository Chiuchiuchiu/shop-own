<?php
require_once("php_java.php");//引用LAJP提供的PHP脚本
try
{
   $signAlg = $_REQUEST['signAlg'];
   $base64SourceData = $_REQUEST['base64SourceData'];
   $base64CertData = $_REQUEST['base64CertData'];
 
   $ret = lajp_call("cfca.sadk.api.EnvelopeKit::envelopeMessage",  $base64SourceData,$signAlg, $base64CertData);
   echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>
<a href="index.html">return</a>