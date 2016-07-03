<?php
	
	/* Class for send request to Microsoft Translate and manage responses.
	Works with Microsoft Translate V2. Different versions compatibility is not guaranteed.
	Error coding:		011 BabelNet connection failed


	*/

	// Includes.
	require_once("httprequest.class.php");

	
	class BingTranslateRequest {
		
		// Internal service attributes.
		private $url;
		private $client_id;
		private $client_secret;
		private $grant_type;
		private $http;
		private $token;
		private $header;
		private $query_modes;

		// Public attributes.
		public $service_name;

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

			global $msg;

			$this->message = $this->errlog = NULL;
			
			$this->client_id		= "FedversELSA";
			$this->client_secret	= "6279886fde090b3038f267098bcca771a6efa946";
			$this->grant_type		= "client_credentials";
			$this->service_name		= "Microsoft Translator v2";
			$this->authUrl			= "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13";
			$this->url				= "http://api.microsofttranslator.com";
			$this->apiUrl			= "/V2/Ajax.svc/";
			$this->http				= new HttpRequest();
			$this->setModes();
			$ok = $this->checkConnection();
			$this->http->resetRequestSettings();

			if ($ok){
				$msg->log("999", __METHOD);
				$this->http->setHeader("Authorization: Bearer ".$this->token);
				$this->message	= "Class BingTranslateRequest instanced successfully. [BingTranslateRequest.BingTranslateRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$msg->log("017", __METHOD);
				$this->message	= "Error code 016: Microsoft Translator token not valid. [BingTranslateRequest.BingTranslateRequest]";
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
										"languages"			=> "getLanguagesForTranslations",
										"translates"		=> "GetTranslations"
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

			global $msg;

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
				$msg->logCustom("Microsoft Translator", $response['error'], $response['error_description'], "error", __METHOD);
				$this->message	= "Microsoft Translator error code ".$response['error'].": ".$response['error_description'].". [".__METHOD__."]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				return FALSE;
			}else{
				$this->token	= $response['access_token'];
				$msg->logCustom("Microsoft Translator", $response['error'], "connection successful", "notice", __METHOD);
				$this->message	= "Microsoft Translator connection successful. [".__METHOD__."]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
				return TRUE;
			}
		}

		// Filters every word in a synset, removing annoying characters that are not letters.
		// Returns a filtered synset.
		private function synsetFilter($synset){

			for ($i = 0; $i < count($synset); $i++){
				$synset[$i] = str_replace("(", "", $synset[$i]);
				$synset[$i] = str_replace(")", "", $synset[$i]);
				$synset[$i] = str_replace("_", "", $synset[$i]);
				$synset[$i] = str_replace(",", "", $synset[$i]);
				$synset[$i] = str_replace(".", "", $synset[$i]);
				$synset[$i] = str_replace(";", "", $synset[$i]);
				$synset[$i] = str_replace(":", "", $synset[$i]);
				$synset[$i] = str_replace("+", "", $synset[$i]);
				$synset[$i] = str_replace("*", "", $synset[$i]);

				$synset[$i] = trim($synset[$i]);
			}

			return $synset;
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

		// Outputs a variable in a preformatted form.
		public function out($var){
			echo "<br><br><pre>";
			print_r($var);
			echo "</pre><br><br>";
		}


		// Perform a translation request to retrieve ad array of possible translations.
		public function translate($word, $source_lang, $dest_lang, $max=4, $domain="general"){
			
			$paramArr = array (
									 'text'				=> urlencode($word),
									 'from'				=> $source_lang,
									 'to'				=> $dest_lang,
									 'maxTranslations'	=> $max
									 /*'options'			=> "{'Category': '".$domain."'}"*/
            );

			//$this->out($paramArr);
			$paramArr = http_build_query($paramArr);
			$this->http->setPostRequest($paramArr);
			$this->http->setURL($this->url.$this->apiUrl.$this->getMode("translates"));
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


		// Perform a translation request to retrieve a single translation.
		public function translateSingle($word, $source_lang, $dest_lang){
			
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


		// Perform a translation request to retrieve a single translation.
		public function translateMany($word, $source_lang, $dest_lang, $max=10, $domain="general"){
			
			$paramArr = array (
									 'text'				=> urlencode($word),
									 'from'				=> $source_lang,
									 'to'				=> $dest_lang,
									 'maxTranslations'	=> $max
									 /*'options'			=> "{'Category': '".$domain."'}"*/
            );
			$paramArr = http_build_query($paramArr);

			$this->http->setURL($this->url.$this->apiUrl.$this->getMode("translates")."?".$paramArr);
			$response = $this->http->send(FALSE);

			preg_match_all('@"TranslatedText":"(.*?)"@i', $response, $matches, PREG_OFFSET_CAPTURE);
			$string = NULL;

			foreach ($matches[1] as $val)
				$string .= strtolower($val[0]).",";

			$string = substr($string, 0, -1);

			return array_unique($this->synsetFilter(explode(",", $string)));

			//return $response;

		}

	} // End class.

?>