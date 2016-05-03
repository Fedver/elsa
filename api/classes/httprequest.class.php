<?php
		
		/* Class for send request to BabelNet and manage responses.
	Works with BabelNet 3.6 and lower. Higher versions compatibility is not guaranteed.
	Error coding:		001 missing parameters
						009 URL format is not valid
						010 no URL specified


	*/

	
	class HttpRequest {
		
		// Internal service attributes.
		private $url;

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
					$this->url = $url;
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
		// Returns an associative array containing the message received or FALSE in case of errors.
		// $type indicates output type. FALSE -> JSON string, TRUE -> associative array of the JSON string.
		public function send($type=TRUE){

			if ($this->url){
			
				$http	= curl_init();
				curl_setopt($http, CURLOPT_URL, $this->url);
				curl_setopt($http, CURLOPT_RETURNTRANSFER, 1);
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
					if ($type == FALSE) $response;
					else return json_decode($response, TRUE);
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
		public function setURL($url){

			if ($url){
				if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE){
					$this->url		= $url;
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

	} // End class.

?>