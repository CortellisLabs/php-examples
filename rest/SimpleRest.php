<?php

$serverList = array('localhost', '127.0.0.1');

if(!in_array($_SERVER['HTTP_HOST'], $serverList)) {

	$u = "YOUR USERNAME";
	$p = "YOUR PASSWORD";
	$class = new LSapiRest($u,$p);
	$rest;
	$fmt;
	//$api can be: analytics, ciMatrix, company, conference, deals, diseasebriefings, drugs, literature, ontologies, patents, pressRelease, regulatory, trials, gvdb, omics, drugdesign, targets
	if(isset($_REQUEST['rest']))$rest=$_REQUEST['rest'];
	if(isset($_REQUEST['fmt']))$fmt=$_REQUEST['fmt'];

	$baseurl="https://lsapi.thomson-pharma.com"; //default
	$endpoint;
	$url;
	if(isset($rest)){
		$url=$rest;
		$baseops=array("https://lsapi.thomson-pharma.com", "http://metacoreapi.thomsonreuterslifesciences.com", "https://lsapi.thomsonreuterslifesciences.com");
		$endptops=array("/ls-api-ws/ws/rs/analytics-v2", "/ls-si-api-ws/ws/rs/biomarkers-v1", "/ls-api-ws/ws/rs/company-v1", "/ls-api-ws/ws/rs/conference-v1", "/ls-api-ws/ws/rs/deals-v1", "/ls-api-ws/ws/rs/diseasebriefings-v1", "/ls-si-api-ws/ws/rs/drugdesign-v1", "/ls-api-ws/ws/rs/drugs-v1", "/ls-api-ws/ws/rs/literature-v1", "/ls-api-ws/ws/rs/omics-v1", "/ls-api-ws/ws/rs/ontologies-v1", "/ls-api-ws/ws/rs/patents-v2", "/ls-api-ws/ws/rs/pressRelease-v1", "/ls-api-ws/ws/rs/regulatory-v1", "/targetapi/ws/rs/targets-v1", "/ls-api-ws/ws/rs/trials-v1", "/ls-si-api-ws/ws/rs/auth-v1", "/ls-api-ws/ws/rs/GeneVariants-v1", "/ls-api-ws/ws/rs/opportunity-v1", "/ws/rs/auth-v1");
		for($z=0; $z<count($baseops); $z++){
			if(stristr($rest, $baseops[$z]) !== FALSE){
				$baseurl=$baseops[$z];
				$url=str_replace($baseops[$z], "", $url);
			}
		}
		for($z=0; $z<count($endptops); $z++){
			if(stristr($rest, $endptops[$z]) !== FALSE){
				$endpoint=$endptops[$z];
				$url=str_replace($endptops[$z], "", $url);
			}
		}
	}

	if($baseurl!=null && $endpoint!=null && $url!=null){
		$test=$class->getResponse($baseurl,$endpoint,$url);
		if(isset($fmt) && ($fmt=="pdf" || stristr($url, 'fmt=pdf')) !== FALSE){
			header('Content-type: application/pdf');
			header('Content-Disposition: inline; filename="document.pdf"');
		}
		echo $test;
	}
}
else{
	echo "No access";
}

class LSapiRest{

	function __construct($username, $password){
		$this->username=$username;
		$this->password=$password;
	}

	function __destruct(){

	}

	function H($param) {
			return md5($param);
	}

	function KD($a,$b) {
			return $this->H("$a:$b");
	}

	function parseHttpDigest($digest) {
		$data = array();
		$parts = explode(", ", $digest);
		foreach ($parts as $element) {
			$bits = explode("=", $element);
			$data[$bits[0]] = str_replace('"','', $bits[1]);
		}
		return $data;
	}

	function response($wwwauth, $httpmethod, $uri) {
		list($dummy_digest, $value) = explode(' ', $wwwauth, 2);
		$x = $this->parseHttpDigest($value);
		$realm = $x['realm'];
		$A1 = $this->username.":".$realm.":".$this->password;
		$A2 = $httpmethod.":".$uri;
		$cnonce = time();
		$ncvalue = 1;
		$noncebit = $x['nonce'].":".$ncvalue.":".$cnonce.":auth:".$this->H($A2);
		$respdig = $this->KD($this->H($A1), $noncebit);
		$base  = 'Digest username="'.$this->username.'", realm="'.$x['realm'].'", nonce="'.$x['nonce'].'", uri="'.$uri.'", cnonce="'.$cnonce.'", nc="'.$ncvalue.'", response="'.$respdig.'", qop="auth"';
		return $base;
	}


	function getResponse($baseurl, $call, $url){
		$sethead="";
		$file = @file_get_contents($baseurl.$call.$url);
		//print_r($file);
		$headers=$http_response_header;
		//print_r($headers);
		for($z=0; $z<count($headers); $z++){
			if (strpos($headers[$z],'WWW-Authenticate') !== false) {
				$www_header = str_replace("WWW-Authenticate: ","",$headers[$z]);
			}
			if (strpos($headers[$z],'Set-') !== false) {
				$sethead=str_replace("Set-","",$headers[$z]);
			}
		}
		

		$theheader=$this->response($www_header, "GET", $call.$url);
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Authorization: ".$theheader."\r\n".$sethead
		    )
		);

		$context = stream_context_create($opts);

		$file = @file_get_contents($baseurl.$call.$url, false, $context);
	

		return $file;
	

	}

}
?>