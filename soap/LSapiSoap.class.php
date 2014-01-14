<?php
class LSapiSoap {
	var $client=null;
	function __construct($username, $password){
		$this->username=$username;
		$this->password=$password;
	}
	function __destruct(){
		unset ($this->client);
	}
	function makeTheCall($api, $funcname, $parameters=array()){
		require_once('/opt/bitnami/apps/drupal/htdocs/sites/all/modules/lsapi7/lib/nusoap.php');
		require_once('/opt/bitnami/apps/drupal/htdocs/sites/all/modules/lsapi7/lib/nusoapmime.php');
		date_default_timezone_set('America/New_York');
		$proxyhost = isset($_POST['proxyhost']) ? $_POST['proxyhost'] : '';
		$proxyport = isset($_POST['proxyport']) ? $_POST['proxyport'] : '';
		$proxyusername = isset($_POST['proxyusername']) ? $_POST['proxyusername'] : '';
		$proxypassword = isset($_POST['proxypassword']) ? $_POST['proxypassword'] : '';
		$username=$this->username;
		$password=$this->password;
		$timestamp=gmdate('Y-m-d\TH:i:s\Z');//The timestamp. The computer must be on time or the server you are connecting may reject the password digest for security.
		$nonce=mt_rand(); //A random word. The use of rand() may repeat the word if the server is very loaded.
		$passdigest=base64_encode(pack('H*',sha1(pack('H*',$nonce).pack('a*',$timestamp).pack('a*',$password))));//This is the right way to create the password digest. Using the password directly may work also, but it's not secure to transmit it without encryption. And anyway, at least with axis+wss4j, the nonce and timestamp are mandatory anyway.
		$auth='
		<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
		<wsse:UsernameToken>
			<wsse:Username>'.$username.'</wsse:Username>
			<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordDigest">'.$passdigest.'</wsse:Password>
			<wsse:Nonce EncodingType="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary">'.base64_encode(pack('H*',$nonce)).'</wsse:Nonce>
			<wsu:Created xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">'.$timestamp.'</wsu:Created>
		 </wsse:UsernameToken>
		</wsse:Security>
		';
		$wsdl='';
		switch ($api) {
			//Analytics 1
			case 'analytics':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/analytics/v2?wsdl';
			break;	

			//Biomarkers 2
			case 'biomarkers':
				$wsdl='https://lsapi.thomson-pharma.com/ls-si-api-ws/ws/LSSIApiService/biomarkers/v1?wsdl';
			break;			
			
			//Clinical 3 4 5 6
			case 'trials':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/trials/v1?wsdl';
			break;
			case 'conference':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/conference/v1?wsdl';
			break;

			case 'pressRelease':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/pressRelease/v1?wsdl';
			break;

			case 'literature':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/literature/v1?wsdl';
			break;

			//Drug Design 7
			case 'drugDesign':
				$wsdl='https://lsapi.thomson-pharma.com/ls-si-api-ws/ws/LSSIApiService/drugdesign/v1?wsdl';
			break;

			//Gene Variants 8
			case 'GeneVariants':
				$wsdl='https://lsapi.thomsonreuterslifesciences.com/ls-api-ws/ws/LsApiService/GeneVariants/v1?wsdl';
			break;

			//Investigational Drugs 9 10 11 12 13
			case 'drugs':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/drugs/v1?wsdl';
			break;

			case 'diseaseBriefings':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/diseasebriefings/v1?wsdl';
			break;

			case 'company':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/company/v1?wsdl';
			break;

			case 'deals':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/deals/v1?wsdl';
			break;

			case 'ciMatrix':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/cimatrix/v1?wsdl';
			break;									

			//Ontologies 14
			case 'ontologies':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/ontologies/v1?wsdl';
			break;

			//Omics 15
			case 'omics':
				$wsdl='http://metacoreapi.thomsonreuterslifesciences.com/ls-api-ws/ws/LsApiService/omics/v1?wsdl';
			break;

			//Opportunity Monitor 16
			case 'opportunity':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/opportunity/v1?wsdl';
			break;

			//Patents 17
			case 'patents':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/patents/v2?wsdl';
			break;

			//Regulatory 18
			case 'regulatory':
				$wsdl='https://lsapi.thomson-pharma.com/ls-api-ws/ws/LSApiService/regulatory/v1?wsdl';
			break;

			//Targets 19
			case 'targets':
				$wsdl=': https://lsapi.thomson-pharma.com/targetapi/ws/LSApiService/targets/v1?wsdl';
			break;			

		}
		$this->client = new nusoap_client_mime($wsdl, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
		$this->client->soap_defencoding = 'UTF-8';
		$this->client->decode_utf8 = false;
		$this->client->setHeaders($auth);
		$err = $this->client->getError();
		$result = $this->client->call($funcname,$parameters,'', '', false, true);
		//echo '<h2>Request</h2><pre>' . htmlspecialchars($this->client->request, ENT_QUOTES) . '</pre>';
		//echo '<h2>Response</h2><pre>' . htmlspecialchars($this->client->response, ENT_QUOTES) . '</pre>';
		//echo '<h2>Debug</h2><pre>' . htmlspecialchars($this->client->debug_str, ENT_QUOTES) . '</pre>';
		$funcRet[0]=$this->client->request;
		$piece=preg_split('/([<]ns[0-9][^>]*[^\/]>)|([<][\/]ns[0-9][^>]*[^\/]>)/i', $this->client->response);
		//$funcRet[1]=$piece[1];
		//return $funcRet;
		return $piece[1];
	}
}
?>