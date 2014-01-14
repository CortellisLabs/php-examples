<?php 
include_once("XMLtoArray.class.php"); 

$xml=file_get_contents("http://".str_replace(substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1),"",$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"])."SimpleRest.php?rest=".urlencode("https://lsapi.thomson-pharma.com/ls-api-ws/ws/rs/deals-v1/search/pfizer/?hits=20")); 

$xmlary=xml2ary($xml); 

print_r($xmlary); 
?>