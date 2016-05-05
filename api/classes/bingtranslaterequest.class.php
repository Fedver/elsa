<?php
	
	/* Class for send request to Microsoft Translate and manage responses.
	Works with Microsoft Translate V2. Different versions compatibility is not guaranteed.
	Error coding:		011 BabelNet connection failed


	*/

	// Includes.
	require_once("httprequest.class.php");

	
	class BingTranslateRequest {
		
		// Internal service attributes.
		private $url, $client_id, $client_secret, $grant_type, $http, $token, $header, $query_modes;

		// Public attributes.
		public $bn_version, $service_name;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Requires mysqli resource.
		// Returns TRUE if is successful, FALSE otherwise.
		public function BingTranslateRequest(){

			$this->message = $this->errlog = NULL;
			
			$this->client_id		= "FedversELSA";
			$this->client_secret	= "6279886fde090b3038f267098bcca771a6efa946";
			$this->grant_type		= "client_credentials";
			$this->service_name		= "Microsoft Translator";
			$this->authUrl			= "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13";
			$this->url				= "http://api.microsofttranslator.com";
			$this->apiUrl			= "/v2/Http.svc/";
			$this->http				= new HttpRequest();
			$this->setModes();
			$ok = $this->checkConnection();
			$this->http->resetRequestSettings();

			if ($ok){
				$this->http->setHeader("Authorization: Bearer ".$this->token);
				$this->message	= "Class BingTranslateRequest instanced successfully. [BingTranslateRequest.BingTranslateRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$this->message	= "Error code 011: Microsoft Translator connection failed. [BingTranslateRequest.BingTranslateRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PRIVATE METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Hardcode setting of interaction modes with BabelNet.
		private function setModes(){
			
			$this->query_modes = array(	"detect"			=> "Detect",
										"translate"			=> "Translate",
										"languages"			=> "getLanguagesForTranslations"
									);
		}


		// Returns the correct BabelNet mode name stored in $this->query_modes. The parameter is a tag name.
		private function getMode($code){
			return $this->query_modes[$code];
		}


		// Checks the connection with Microsoft Translator API.
		// Returns TRUE if it's all ok and FALSE otherwise.
		// Also retrieves the authentication token, valid for 10 minutes, stored in $this->token.
		private function checkConnection(){

			$paramArr = array (
									 'grant_type'    => $this->grant_type,
									 'scope'         => $this->url,
									 'client_id'     => urlencode($this->client_id),
									 'client_secret' => urlencode($this->client_secret)
            );
			$paramArr = http_build_query($paramArr);

			$this->http->setURL($this->authUrl);
			$this->http->setPostRequest($paramArr);
			$response = $this->http->send();

			if (!$response['access_token']){
				$this->message	= "Microsoft Translator error code ".$response['error'].": ".$response['error_description'].". [BingTranslateRequest.checkConnection]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$this->token	= $response['access_token'];
				$this->message	= "Microsoft Translator connection successful. [BingTranslateRequest.checkConnection]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
				return TRUE;
			}
		}


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//									PUBLIC METHODS										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// Returns an HTML-compatible version of $this->errlog.
		public function HTMLizeErrlog(){
			return str_replace("\n", "<br />", $this->errlog);
		}


		// Perform a translation request.
		public function translate($word, $source_lang, $dest_lang){
			
			$paramArr = array (
									 'text'		=> urlencode($word),
									 'from'		=> $source_lang,
									 'to'		=> $dest_lang
            );
			$paramArr = http_build_query($paramArr);

			$this->http->setURL($this->url.$this->apiUrl.$this->getMode("translate")."?".$paramArr);
			$response = $this->http->send(FALSE);

			return $response;

		}


		// Perform a detection request.
		public function detect($word){
			
			$paramArr = array (
									 'text'	=> urlencode($word)
            );
			$paramArr = http_build_query($paramArr);

			$this->http->setURL($this->url.$this->apiUrl.$this->getMode("detect")."?".$paramArr);
			$response = $this->http->send(FALSE);

			return $response;

		}


		// Perform a detection request.
		public function getLanguage($word){
			
			$paramArr = array (
									 'locale'	=> urlencode($word)
            );
			$paramArr = http_build_query($paramArr);

			$this->http->setURL($this->url.$this->apiUrl.$this->getMode("languages")."?".$paramArr);
			$response = $this->http->send(FALSE);

			return $response;

		}

	} // End class.

?>