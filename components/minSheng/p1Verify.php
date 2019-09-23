<?php
require_once("php_java.php");//引用LAJP提供的PHP脚本
try
{
	$signAlg = $_REQUEST['signAlg'];
	$base64SourceData = $_REQUEST['base64SourceData'];
  $base64X509CertData = $_REQUEST['base64X509CertData'];
  $base64P1SignatureData = $_REQUEST['base64P1SignatureData'];
 
	$ret = lajp_call("cfca.sadk.api.SignatureKit::P1VerifyMessage", $signAlg,$base64SourceData, $base64X509CertData,$base64P1SignatureData);
    echo "{$ret}<br>";
}
catch(Exception $e)
{
  echo "Err:{$e}<br>";
}
?>
<a href="index.html">return</a>