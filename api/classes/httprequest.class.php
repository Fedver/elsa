<?php
		
		/* Class for send request to BabelNet and manage responses.
	Works with BabelNet 3.6 and lower. Higher versions compatibility is not guaranteed.
	Error coding:		001 missing parameters
						009 URL format is not valid
						010 no URL specified
						012 method is not valid
						013 parameters are not valid
						014 conflictual settings
						015 required attribute not found


	*/

	
	class HttpRequest {
		
		// Internal service attributes.
		private $url, $method, $params, $header;

		// Output attributes.
		public $message, $errlog, $status;


		//////////////////////////////////////////////////////////////////////////////////////////
		//																						//
		//										CONSTRUCTOR										//
		//																						//
		//////////////////////////////////////////////////////////////////////////////////////////


		// URL is optional in the costructor, see method $this->setURL().
		public function HttpRequest($url=NULL){

			if ($url){
				if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE){
					$this->url		= $url;
					$this->method	= "GET";
					$this->params	= NULL;
					$this->message	= "Class HttpRequest instanced successfully. [HttpRequest.HttpRequest]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= TRUE;
				}else{
					$this->message	= "Error code 009: URL format is not valid. [HttpRequest.HttpRequest]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
			}else{
				$this->message	= "Class HttpRequest instanced successfully without URL. [HttpRequest.HttpRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
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
		
		
		// Send a cURL request.
		// Returns the message received or FALSE in case of errors.
		// $type indicates output type. FALSE -> JSON string, TRUE -> associative array decoded from the JSON string.
		public function send($type=TRUE){

			if ($this->url){

				if ($this->method == "POST" && strstr($this->url, "?")){
					$this->message	= "Errore code 014: conflictual settings (GET params and POST method). [HttpRequest.send]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}elseif ($this->method == "GET" && $this->params){
					$this->message	= "Errore code 014: conflictual settings (POST params and GET method). [HttpRequest.send]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}elseif (!$this->method){
					$this->message	= "Errore code 015: required attribute not found (method). [HttpRequest.send]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
			
				try{
					$http	= curl_init();
					curl_setopt($http, CURLOPT_URL, $this->url);
					curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
					if ($this->method == "POST"){
						curl_setopt($http, CURLOPT_POST, TRUE);
						curl_setopt($http, CURLOPT_POSTFIELDS, $this->params);
					}
					if ($this->header) curl_setopt ($http, CURLOPT_HTTPHEADER, array($this->header,"Content-Type: text/plain"));
					curl_setopt($http, CURLOPT_SSL_VERIFYPEER, FALSE);
					$response = curl_exec($http);

					if (curl_errno($http) > 0){
						$this->message	= "cURL error code ".curl_errno($http).": ".curl_error($http).". [HttpRequest.send]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= FALSE;
						curl_close($http);
						return FALSE;
					}else{
						$this->message	= "cURL message code ".curl_errno($http).": ".curl_error($http).". [HttpRequest.send]";
						$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
						$this->status	= TRUE;
						curl_close($http);
						if ($type == FALSE) return $response;
						else return json_decode($response, TRUE);
					}
				}catch (Exception $e){
					$this->message	= "Exception code ".$e->getCode().": ".$e->getMessage().". [HttpRequest.send]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					if ($http) @curl_close($http);
					return FALSE;
				}
			}else{
				$this->message	= "Error code 010: no URL specified. [HttpRequest.send]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
				curl_close($http);
				return FALSE;
			}
		}


		// Sets the URL used within the current session.
		// Returns TRUE if it's all ok and FALSE otherwise.
		// If finds a '?' in the URL, automatically sets the method as GET.
		public function setURL($url){

			if ($url){
				if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE){
					$this->url		= $url;
					if (strstr($this->url, "?")) $this->method = "GET";
					$this->status	= TRUE;
				}else{
					$this->message	= "Error code 009: URL format is not valid. [HttpRequest.setURL]";
					$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
					$this->status	= FALSE;
					return FALSE;
				}
			}else{
				$this->message	= "Error code 001: missing parameters (URL). [HttpRequest.setURL]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		public function resetRequestSettings($url=""){
			$this->method	= "GET";
			$this->params	= NULL;
			if ($url)	$this->setURL($url);
			else		$this->url = NULL;
		}


		public function getUrl(){
			return $this->url;
		}


		// Sets the method used by the HTTP request, GET or POST.
		// Returns TRUE if it's all ok and FALSE otherwise.
		public function setMethod($method){

			if ($method == "GET" || $method == "get" || $method == "POST" || $method == "post"){
				$this->method = strtoupper($method);
				$this->message	= "Method ".$this->method." set successfully. [HttpRequest.setMethod]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$this->message	= "Error code 012: method is not valid. [HttpRequest.setMethod]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		// Sets parameters for POST request and $this->method as POST. Accepts only associative arrays.
		// Returns TRUE if it's all ok and FALSE otherwise.
		public function setPostRequest($params){

			if ($params){
				$this->params	= $params;
				$this->method	= "POST";
				$this->message	= "POST request set successfully. [HttpRequest.setPostRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$this->message	= "Error code 013: parameters are not valid. [HttpRequest.setPostRequest]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}


		// Sets parameters for POST request and $this->method as POST. Accepts only associative arrays.
		// Returns TRUE if it's all ok and FALSE otherwise.
		public function setHeader($header){
			
			if ($header){
				$this->header	= $header;
				$this->message	= "Header set successfully. [HttpRequest.setHeader]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= TRUE;
			}else{
				$this->message	= "Error code 001: missing parameter [header]. [HttpRequest.setHeader]";
				$this->errlog	.= "[".date("d-m-o H:i:s")."] ".$this->message."\n";
				$this->status	= FALSE;
			}
		}

	} // End class.

?>